<?php

namespace App\Services;

use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/** NakheelUpcomingProjectService — the "Upcoming Projects" cards on the Future Projects page. */
class NakheelUpcomingProjectService
{
    use ResolvesTranslations;

    private string $table = 'nakheel_upcoming_projects';

    /** Locale-resolved rows for public display. */
    public function all(): array
    {
        return DB::table($this->table)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => [
                ...(array) $row,
                'badge' => $this->tr($row->badge, $row->badge_ar),
                'title' => $this->tr($row->title, $row->title_ar),
                'description' => $this->tr($row->description, $row->description_ar),
                'launch' => $this->tr($row->launch, $row->launch_ar),
            ])
            ->all();
    }

    public function find(int $id): ?array
    {
        $row = DB::table($this->table)->find($id);

        return $row ? (array) $row : null;
    }

    public function create(array $data): void
    {
        DB::table($this->table)->insert([
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
        return [
            'badge' => $data['badge'] ?? null,
            'badge_ar' => $data['badge_ar'] ?? null,
            'title' => $data['title'],
            'title_ar' => $data['title_ar'] ?? null,
            'description' => $data['description'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'launch' => $data['launch'] ?? null,
            'launch_ar' => $data['launch_ar'] ?? null,
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }
}
