<?php

namespace App\Services\Concerns;

/**
 * Turns the admin's day-dropdown + time-picker selections into a clean,
 * locale-aware sentence (e.g. "Sunday - Thursday, 9:00 AM - 5:00 PM" /
 * "الأحد - الخميس، 9:00 ص - 5:00 م"). Falls back to the legacy free-text
 * office_hours/office_hours_ar columns for rows saved before this existed.
 */
trait FormatsOfficeHours
{
    use ResolvesTranslations;

    private const DAYS = [
        'sunday' => ['en' => 'Sunday', 'ar' => 'الأحد'],
        'monday' => ['en' => 'Monday', 'ar' => 'الاثنين'],
        'tuesday' => ['en' => 'Tuesday', 'ar' => 'الثلاثاء'],
        'wednesday' => ['en' => 'Wednesday', 'ar' => 'الأربعاء'],
        'thursday' => ['en' => 'Thursday', 'ar' => 'الخميس'],
        'friday' => ['en' => 'Friday', 'ar' => 'الجمعة'],
        'saturday' => ['en' => 'Saturday', 'ar' => 'السبت'],
    ];

    protected function formatOfficeHours(
        ?string $fromDay,
        ?string $toDay,
        ?string $fromTime,
        ?string $toTime,
        ?string $legacyEn,
        ?string $legacyAr,
    ): ?string {
        if (blank($fromDay) || blank($fromTime)) {
            return $this->tr($legacyEn, $legacyAr);
        }

        $ar = app()->getLocale() === 'ar';

        $days = self::DAYS[$fromDay][$ar ? 'ar' : 'en'] ?? $fromDay;
        if (filled($toDay) && $toDay !== $fromDay) {
            $days .= ' - '.(self::DAYS[$toDay][$ar ? 'ar' : 'en'] ?? $toDay);
        }

        $times = $this->formatTime($fromTime, $ar);
        if (filled($toTime)) {
            $times .= ' - '.$this->formatTime($toTime, $ar);
        }

        return $ar ? "{$days}، {$times}" : "{$days}, {$times}";
    }

    private function formatTime(string $time, bool $ar): string
    {
        [$hour, $minute] = array_pad(array_map('intval', explode(':', $time)), 2, 0);
        $period = $hour >= 12 ? ($ar ? 'م' : 'PM') : ($ar ? 'ص' : 'AM');
        $hour12 = $hour % 12 ?: 12;

        return sprintf('%d:%02d %s', $hour12, $minute, $period);
    }

    /** @return array<string, string> day-key => English label, for building the admin dropdown options */
    protected function officeHourDayOptions(): array
    {
        return array_map(fn ($day) => $day['en'], self::DAYS);
    }
}
