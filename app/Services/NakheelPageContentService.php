<?php

namespace App\Services;

use App\Services\Concerns\FormatsOfficeHours;
use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/**
 * NakheelPageContentService — singleton text+image content blocks, keyed by
 * name (e.g. 'about_story', 'future_vision'). No create/delete — these
 * blocks always exist, only their content is edited.
 */
class NakheelPageContentService
{
    use ResolvesTranslations;
    use FormatsOfficeHours;

    private string $table = 'nakheel_page_content';

    /** Raw row (both English and Arabic columns) for the admin edit form. */
    public function find(string $key): ?array
    {
        $row = DB::table($this->table)->where('key', $key)->first();

        return $row ? (array) $row : null;
    }

    /** Locale-resolved row for public display. Contact channels (phone/whatsapp/telegram/email) are never translated. */
    public function findForDisplay(string $key): ?array
    {
        $content = $this->find($key);

        if (! $content) {
            return null;
        }

        $content['heading'] = $this->tr($content['heading'], $content['heading_ar'] ?? null);
        $content['body'] = $this->tr($content['body'], $content['body_ar'] ?? null);
        $content['office_address'] = $this->tr($content['office_address'] ?? null, $content['office_address_ar'] ?? null);
        $content['office_hours'] = $this->formatOfficeHours(
            $content['office_hours_from_day'] ?? null, $content['office_hours_to_day'] ?? null,
            $content['office_hours_from_time'] ?? null, $content['office_hours_to_time'] ?? null,
            $content['office_hours'] ?? null, $content['office_hours_ar'] ?? null,
        );

        return $content;
    }

    /** Body paragraphs split on a blank line, for rendering as separate <p> tags. */
    public function paragraphs(string $key): array
    {
        $content = $this->findForDisplay($key);

        if (! $content || blank($content['body'])) {
            return [];
        }

        return collect(explode("\n\n", $content['body']))
            ->map(fn ($p) => trim($p))
            ->filter()
            ->all();
    }

    public function update(string $key, array $data): void
    {
        DB::table($this->table)->where('key', $key)->update([
            'heading' => $data['heading'] ?? null,
            'heading_ar' => $data['heading_ar'] ?? null,
            'body' => $data['body'] ?? null,
            'body_ar' => $data['body_ar'] ?? null,
            'image' => $data['image'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'telegram' => $data['telegram'] ?? null,
            'email' => $data['email'] ?? null,
            'office_address' => $data['office_address'] ?? null,
            'office_address_ar' => $data['office_address_ar'] ?? null,
            'office_hours' => $data['office_hours'] ?? null,
            'office_hours_ar' => $data['office_hours_ar'] ?? null,
            'office_hours_from_day' => $data['office_hours_from_day'] ?? null,
            'office_hours_to_day' => $data['office_hours_to_day'] ?? null,
            'office_hours_from_time' => $data['office_hours_from_time'] ?? null,
            'office_hours_to_time' => $data['office_hours_to_time'] ?? null,
            'updated_at' => now(),
        ]);
    }
}
