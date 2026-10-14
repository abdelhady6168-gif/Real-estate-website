<?php

namespace App\Services;

use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/** NakheelWhyInvestService — the "Why Invest With Nakheel" cards on the Future Projects page. */
class NakheelWhyInvestService
{
    use ResolvesTranslations;

    private string $table = 'nakheel_why_invest_items';

    /** Locale-resolved rows for public display. */
    public function all(): array
    {
        return DB::table($this->table)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => [
                ...(array) $row,
                'title' => $this->tr($row->title, $row->title_ar),
                'description' => $this->tr($row->description, $row->description_ar),
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
            'title' => $data['title'],
            'title_ar' => $data['title_ar'] ?? null,
            'description' => $data['description'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }
}
