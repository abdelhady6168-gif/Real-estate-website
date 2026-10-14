<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds the two new page-content keys the About Us / Future Projects hero
     * sections now read from, and backfills structured office-hours columns
     * on the existing seeded `office_contact` row from its free-text value
     * so the new day/time picker has something to show admins immediately.
     */
    public function up(): void
    {
        $now = now();

        foreach ($this->rows($now) as $row) {
            DB::table('nakheel_page_content')->insertOrIgnore($row);
        }

        DB::table('nakheel_page_content')
            ->where('key', 'office_contact')
            ->whereNull('office_hours_from_day')
            ->update([
                'office_hours_from_day' => 'sunday',
                'office_hours_to_day' => 'thursday',
                'office_hours_from_time' => '09:00',
                'office_hours_to_time' => '17:00',
            ]);
    }

    public function down(): void
    {
        DB::table('nakheel_page_content')->whereIn('key', ['about_hero', 'future_projects_hero'])->delete();
    }

    private function rows($now): array
    {
        return [
            [
                'key' => 'about_hero',
                'heading' => 'About Nakheel Properties',
                'heading_ar' => 'عن نخيل العقارية',
                'body' => "A leading real estate development and investment company shaping Libya's built environment.",
                'body_ar' => 'شركة رائدة في التطوير والاستثمار العقاري تشكّل ملامح البيئة العمرانية في ليبيا.',
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'future_projects_hero',
                'heading' => 'Future Projects & Investment Opportunities',
                'heading_ar' => 'المشاريع المستقبلية وفرص الاستثمار',
                'body' => 'Nakheel Properties is developing the next generation of Libyan real estate. Explore our upcoming locations and investment partners to join us in building these future projects.',
                'body_ar' => 'تعمل نخيل العقارية على تطوير الجيل القادم من العقارات الليبية. اكتشف مواقعنا القادمة وشركاء الاستثمار للانضمام إلينا في بناء هذه المشاريع المستقبلية.',
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }
};
