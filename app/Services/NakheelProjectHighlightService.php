<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * NakheelProjectHighlightService — the "PROJECT HIGHLIGHTS" cards on a
 * project's public page, each with a label and an optional image. Scoped to
 * one project (nakheel_project_id), managed inline on that project's admin
 * edit form rather than through its own admin index.
 */
class NakheelProjectHighlightService
{
    private string $table = 'nakheel_project_highlights';

    public function forProject(int $projectId): array
    {
        return DB::table($this->table)
            ->where('nakheel_project_id', $projectId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    public function find(int $id): ?array
    {
        $row = DB::table($this->table)->find($id);

        return $row ? (array) $row : null;
    }

    public function create(int $projectId, array $data): int
    {
        return DB::table($this->table)->insertGetId([
            'nakheel_project_id' => $projectId,
            ...$this->columns($data),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $id, array $data): void
    {
        DB::table($this->table)->where('id', $id)->update([
            ...$this->columns($data),
            'updated_at' => now(),
        ]);
    }

    public function delete(int $id): void
    {
        DB::table($this->table)->delete($id);
    }

    private function columns(array $data): array
    {
        $columns = [
            'label' => $data['label'],
            'label_ar' => $data['label_ar'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        // Only set on create (new rows with an uploaded file). Existing
        // rows' images are managed independently via AdminImageController,
        // so omitting the key here on update() leaves the image untouched.
        if (array_key_exists('image', $data)) {
            $columns['image'] = $data['image'];
        }

        return $columns;
    }
}
