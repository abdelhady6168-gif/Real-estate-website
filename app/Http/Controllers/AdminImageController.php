<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * One generic AJAX endpoint backing every admin image field in the app
 * (<x-nakheel.image-field> and <x-nakheel.image-list-field>). Lets an admin
 * add or remove a single image immediately — no full-page form save — while
 * every other image on the record is left untouched.
 *
 * Deliberately reaches into DB::table() directly (like every other Service
 * in this app) rather than adding a one-off "updateField" method to eight
 * different services: the field being touched is chosen from the fixed
 * RESOURCES registry below, never from user input, so this stays safe while
 * avoiding eight near-identical helper methods.
 */
class AdminImageController extends Controller
{
    use HandlesImageUploads;

    /** resource key => [table, key column, field => is-list-of-images]. */
    private const RESOURCES = [
        'properties' => ['table' => 'nakheel_properties', 'key' => 'id', 'fields' => ['images' => true, 'floor_plans' => true]],
        'projects' => ['table' => 'nakheel_projects', 'key' => 'id', 'fields' => ['hero_images' => false, 'environment_images' => true]],
        'categories' => ['table' => 'nakheel_categories', 'key' => 'id', 'fields' => ['image' => false]],
        'timeline' => ['table' => 'nakheel_timeline_entries', 'key' => 'id', 'fields' => ['image' => false]],
        'upcoming' => ['table' => 'nakheel_upcoming_projects', 'key' => 'id', 'fields' => ['image' => false]],
        'page-content' => ['table' => 'nakheel_page_content', 'key' => 'key', 'fields' => ['image' => false]],
        'highlights' => ['table' => 'nakheel_project_highlights', 'key' => 'id', 'fields' => ['image' => false]],
    ];

    public function store(Request $request, string $resource, string $id)
    {
        [$table, $keyColumn, $field, $isList] = $this->resolveTarget($request, $resource);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Decode/re-encode (the slow part) happens first, outside the
        // transaction, so the row is only locked for the quick read-modify-
        // write below.
        $path = $this->storeImage($request->file('image'));

        try {
            $replaced = $this->updateFieldAtomically($table, $keyColumn, $id, $field, function (?string $current) use ($isList, $path) {
                if ($isList) {
                    $list = collect(explode(',', $current ?? ''))->map(fn ($v) => trim($v))->filter();

                    return [$list->push($path)->implode(','), null];
                }

                return [$path, $current];
            });
        } catch (\Throwable $e) {
            $this->deleteImageIfStored($path);
            throw $e;
        }

        // Only once the record no longer points at it.
        $this->deleteImageIfStored($replaced);

        return response()->json([
            'path' => $path,
            'url' => \Illuminate\Support\Facades\Storage::url($path),
        ], 201);
    }

    public function destroy(Request $request, string $resource, string $id)
    {
        [$table, $keyColumn, $field, $isList] = $this->resolveTarget($request, $resource);

        $data = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $removed = $this->updateFieldAtomically($table, $keyColumn, $id, $field, function (?string $current) use ($isList, $data) {
            if ($isList) {
                $list = collect(explode(',', $current ?? ''))->map(fn ($v) => trim($v))->filter();

                // The submitted path must actually belong to this record's own
                // field — otherwise an admin authorized on THIS resource/field
                // could delete an unrelated image (a different record, or one
                // outside their permission scope entirely) just by naming its
                // path. Never delete a file the record doesn't currently claim.
                // Checked against the locked, current value — not an earlier read.
                abort_unless($list->contains($data['path']), 404, 'That image is not part of this record.');

                $new = $list->reject(fn ($v) => $v === $data['path'])->implode(',');

                return [$new === '' ? null : $new, $data['path']];
            }

            abort_unless($current === $data['path'], 404, 'That image is not part of this record.');

            return [null, $data['path']];
        });

        // Only once the record no longer points at it.
        $this->deleteImageIfStored($removed);

        return response()->noContent();
    }

    /**
     * Read-modify-write of one image column as a single locked step.
     *
     * Several uploads to the same list field routinely arrive at once (the
     * list field uploads each picked file in its own request — see
     * resources/js/images.js), and each one appends to whatever the column
     * held when it read it. Without the lock, two requests can both read the
     * same list and the later write silently drops the earlier one's image
     * (its file left orphaned on disk). The row lock (MySQL/Postgres) / write
     * lock + retry on "database is locked" (SQLite) makes each request see
     * the value the previous one committed.
     *
     * $change receives the current value and returns [newValue, fileToDeleteAfterwards].
     */
    private function updateFieldAtomically(string $table, string $keyColumn, string $id, string $field, \Closure $change): ?string
    {
        return DB::transaction(function () use ($table, $keyColumn, $id, $field, $change) {
            $current = DB::table($table)->where($keyColumn, $id)->lockForUpdate()->value($field);

            [$new, $fileToDelete] = $change($current);

            DB::table($table)->where($keyColumn, $id)->update([$field => $new, 'updated_at' => now()]);

            return $fileToDelete;
        }, 5);
    }

    /** @return array{0: string, 1: string, 2: string, 3: bool} */
    private function resolveTarget(Request $request, string $resource): array
    {
        abort_unless(array_key_exists($resource, self::RESOURCES), 404);
        $config = self::RESOURCES[$resource];

        $field = (string) $request->input('field');
        abort_unless(array_key_exists($field, $config['fields']), 404, 'Unknown image field.');

        return [$config['table'], $config['key'], $field, $config['fields'][$field]];
    }
}
