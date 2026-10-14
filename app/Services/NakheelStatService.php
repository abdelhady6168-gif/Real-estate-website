<?php

namespace App\Services;

use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/** NakheelStatService — the "12+ Years of Experience" style stat cards on the About Us page. */
class NakheelStatService
{
    use ResolvesTranslations;

    private string $table = 'nakheel_stats';

    /** Locale-resolved rows for public display. */
    public function all(): array
    {
        return DB::table($this->table)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => [
                ...(array) $row,
                'value' => $this->tr($row->value, $row->value_ar),
                'label' => $this->tr($row->label, $row->label_ar),
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
            'value' => $data['value'],
            'value_ar' => $data['value_ar'] ?? null,
            'label' => $data['label'],
            'label_ar' => $data['label_ar'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }
}
