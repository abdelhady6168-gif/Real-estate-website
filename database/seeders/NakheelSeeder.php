<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds all "Nakheel Properties" real-estate content: projects, properties,
 * property statuses, and the About Us / Future Projects marketing content
 * (stats, timeline, categories, page content, upcoming projects, why-invest
 * reasons, project highlights). Uses only the Query Builder, matching the
 * Service pattern used by the rest of the app.
 *
 * Every translatable field carries both its English value and an `_ar`
 * sibling with real Arabic content, so the bilingual system is demonstrable
 * out of the box. Fields that are never translated (contact numbers/emails,
 * slugs, colors, image paths) have no `_ar` sibling.
 *
 * Re-runnable: wipes every table (FK-safe order) before inserting.
 */
class NakheelSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('nakheel_properties')->delete();
        DB::table('nakheel_project_highlights')->delete();
        DB::table('nakheel_projects')->delete();
        DB::table('nakheel_property_statuses')->delete();
        DB::table('nakheel_stats')->delete();
        DB::table('nakheel_timeline_entries')->delete();
        DB::table('nakheel_categories')->delete();
        DB::table('nakheel_upcoming_projects')->delete();
        DB::table('nakheel_page_content')->delete();
        DB::table('nakheel_why_invest_items')->delete();
        DB::table('nakheel_why_invest_icons')->delete();

        $projects = [
            [
                'slug' => 'lotus-compound',
                'name' => 'Lotus Compound',
                'name_ar' => 'مجمع لوتس',
                'type' => 'Residential Compound',
                'type_ar' => 'مجمع سكني',
                'location' => 'Tripoli, Libya',
                'location_ar' => 'طرابلس، ليبيا',
                'tagline' => 'Elevated living in the heart of Tripoli',
                'tagline_ar' => 'حياة راقية في قلب طرابلس',
                'hero_images' => 'nakheel-lotus-hero,nakheel-lotus-hero-2,nakheel-lotus-hero-3',
                'description' => "Lotus Compound is an exclusive gated residential development spanning 24 hectares in the western suburbs of Tripoli. Designed around a central park and shared amenities, the compound offers a refined lifestyle with 24-hour security, landscaped gardens, and premium infrastructure.",
                'description_ar' => 'مجمع لوتس هو تطوير سكني مسوّر حصري يمتد على مساحة 24 هكتارًا في الضواحي الغربية لطرابلس. صُمم حول حديقة مركزية ومرافق مشتركة، ويوفر المجمع نمط حياة راقٍ مع أمن على مدار الساعة، وحدائق منسقة، وبنية تحتية متميزة.',
                'developer' => 'Nakheel Properties',
                'developer_ar' => 'نخيل العقارية',
                'status' => 'Active',
                'status_ar' => 'نشط',
                'completion_year' => '2022',
                'total_units' => 64,
                'available_units' => 3,
                'development_area' => '24 hectares',
                'development_area_ar' => '24 هكتارًا',
                'property_types' => 'Villas, Apartments',
                'property_types_ar' => 'فلل، شقق',
                'highlights' => [
                    ['label' => '24-Hour Security', 'label_ar' => 'أمن على مدار الساعة'],
                    ['label' => 'Landscaped Gardens', 'label_ar' => 'حدائق منسقة'],
                    ['label' => 'Swimming Pools', 'label_ar' => 'مسابح'],
                    ['label' => 'Clubhouse', 'label_ar' => 'نادي اجتماعي'],
                    ['label' => 'Underground Parking', 'label_ar' => 'مواقف تحت الأرض'],
                    ['label' => 'Smart Home Systems', 'label_ar' => 'أنظمة منزلية ذكية'],
                ],
                'environment_images' => 'nakheel-env-1,nakheel-env-2,nakheel-env-3',
                'manager_name' => 'Ahmed Al-Mansouri',
                'manager_role' => 'Sales Director · Lotus Compound',
                'manager_role_ar' => 'مدير المبيعات · مجمع لوتس',
                'manager_phone' => '+218 91 234 5678',
                'manager_whatsapp' => '+218 91 234 5678',
                'manager_email' => 'lotus@nakheel.ly',
                'office_name' => 'Nakheel Properties – Tripoli Office',
                'office_name_ar' => 'نخيل العقارية – مكتب طرابلس',
                'office_address' => 'Lotus Compound Sales Centre, Tripoli',
                'office_address_ar' => 'مركز مبيعات مجمع لوتس، طرابلس',
                'office_hours' => 'Sun–Thu: 9:00 AM – 5:00 PM',
                'office_hours_ar' => 'الأحد–الخميس: 9:00 صباحًا – 5:00 مساءً',
                'maps_url' => 'https://maps.google.com/?q=Lotus+Compound+Tripoli+Libya',
            ],
            [
                'slug' => 'azure-resort',
                'name' => 'Azure Resort',
                'name_ar' => 'منتجع أزور',
                'type' => 'Coastal Resort',
                'type_ar' => 'منتجع ساحلي',
                'location' => 'Sabratha Coast, Libya',
                'location_ar' => 'ساحل صبراتة، ليبيا',
                'tagline' => 'Mediterranean living, reimagined',
                'tagline_ar' => 'حياة متوسطية بحلة جديدة',
                'hero_images' => 'nakheel-azure-hero,nakheel-azure-hero-2,nakheel-azure-hero-3',
                'description' => "Azure Resort is Nakheel's flagship coastal development on the Sabratha coastline, offering private shore houses set around a marina, private beach club, and resort-managed amenities.",
                'description_ar' => 'منتجع أزور هو المشروع الساحلي الرائد لشركة نخيل على ساحل صبراتة، ويضم منازل شاطئية خاصة حول مرسى، ونادي شاطئي خاص، ومرافق يديرها المنتجع.',
                'developer' => 'Nakheel Properties',
                'developer_ar' => 'نخيل العقارية',
                'status' => 'Active',
                'status_ar' => 'نشط',
                'completion_year' => '2022',
                'total_units' => 40,
                'available_units' => 2,
                'development_area' => '12 hectares',
                'development_area_ar' => '12 هكتارًا',
                'property_types' => 'Summer Houses',
                'property_types_ar' => 'منازل صيفية',
                'highlights' => [
                    ['label' => 'Private Beach Access', 'label_ar' => 'وصول خاص إلى الشاطئ'],
                    ['label' => 'Marina', 'label_ar' => 'مرسى'],
                    ['label' => 'Beach Club', 'label_ar' => 'نادي شاطئي'],
                    ['label' => '24-Hour Security', 'label_ar' => 'أمن على مدار الساعة'],
                    ['label' => 'Landscaped Gardens', 'label_ar' => 'حدائق منسقة'],
                    ['label' => 'Water Sports Center', 'label_ar' => 'مركز رياضات مائية'],
                ],
                'environment_images' => 'nakheel-azure-env-1,nakheel-azure-env-2,nakheel-azure-env-3',
                'manager_name' => 'Layla Nuri',
                'manager_role' => 'Resort Sales Manager · Azure Resort',
                'manager_role_ar' => 'مدير مبيعات المنتجع · منتجع أزور',
                'manager_phone' => '+218 92 345 6789',
                'manager_whatsapp' => '+218 92 345 6789',
                'manager_email' => 'azure@nakheel.ly',
                'office_name' => 'Nakheel Properties – Sabratha Office',
                'office_name_ar' => 'نخيل العقارية – مكتب صبراتة',
                'office_address' => 'Azure Resort Sales Pavilion, Sabratha Coast',
                'office_address_ar' => 'جناح مبيعات منتجع أزور، ساحل صبراتة',
                'office_hours' => 'Sat–Thu: 9:00 AM – 6:00 PM',
                'office_hours_ar' => 'السبت–الخميس: 9:00 صباحًا – 6:00 مساءً',
                'maps_url' => 'https://maps.google.com/?q=Azure+Resort+Sabratha+Libya',
            ],
            [
                'slug' => 'mira-residences',
                'name' => 'Mira Residences',
                'name_ar' => 'مساكن ميرا',
                'type' => 'Residential Building',
                'type_ar' => 'مبنى سكني',
                'location' => 'Benghazi, Libya',
                'location_ar' => 'بنغازي، ليبيا',
                'tagline' => 'Modern city living in Benghazi',
                'tagline_ar' => 'حياة عصرية في قلب بنغازي',
                'hero_images' => 'nakheel-mira-hero,nakheel-mira-hero-2,nakheel-mira-hero-3',
                'description' => "Mira Residences is a boutique mid-rise apartment building in central Benghazi, offering modern apartments with smart building infrastructure, concierge services, and thoughtful urban design.",
                'description_ar' => 'مساكن ميرا هو مبنى سكني متوسط الارتفاع في وسط بنغازي، يوفر شققًا عصرية ببنية تحتية ذكية، وخدمات استقبال، وتصميمًا حضريًا مدروسًا.',
                'developer' => 'Nakheel Properties',
                'developer_ar' => 'نخيل العقارية',
                'status' => 'Active',
                'status_ar' => 'نشط',
                'completion_year' => '2024',
                'total_units' => 36,
                'available_units' => 2,
                'development_area' => '0.6 hectares',
                'development_area_ar' => '0.6 هكتار',
                'property_types' => 'Apartments',
                'property_types_ar' => 'شقق',
                'highlights' => [
                    ['label' => 'Concierge Desk', 'label_ar' => 'مكتب استقبال'],
                    ['label' => 'Smart Home Systems', 'label_ar' => 'أنظمة منزلية ذكية'],
                    ['label' => 'Underground Parking', 'label_ar' => 'مواقف تحت الأرض'],
                    ['label' => 'Rooftop Terrace', 'label_ar' => 'تراس على السطح'],
                    ['label' => '24-Hour Security', 'label_ar' => 'أمن على مدار الساعة'],
                    ['label' => 'High-Speed Elevators', 'label_ar' => 'مصاعد عالية السرعة'],
                ],
                'environment_images' => 'nakheel-mira-env-1,nakheel-mira-env-2,nakheel-mira-env-3',
                'manager_name' => 'Youssef Trabelsi',
                'manager_role' => 'Sales Director · Mira Residences',
                'manager_role_ar' => 'مدير المبيعات · مساكن ميرا',
                'manager_phone' => '+218 94 456 7890',
                'manager_whatsapp' => '+218 94 456 7890',
                'manager_email' => 'mira@nakheel.ly',
                'office_name' => 'Nakheel Properties – Benghazi Office',
                'office_name_ar' => 'نخيل العقارية – مكتب بنغازي',
                'office_address' => 'Mira Residences Lobby, Benghazi',
                'office_address_ar' => 'بهو مساكن ميرا، بنغازي',
                'office_hours' => 'Sun–Thu: 9:00 AM – 5:00 PM',
                'office_hours_ar' => 'الأحد–الخميس: 9:00 صباحًا – 5:00 مساءً',
                'maps_url' => 'https://maps.google.com/?q=Mira+Residences+Benghazi+Libya',
            ],
        ];

        $projectIds = [];

        foreach ($projects as $project) {
            $highlights = $project['highlights'];
            unset($project['highlights']);

            $projectIds[$project['slug']] = DB::table('nakheel_projects')->insertGetId([
                ...$project,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($highlights as $index => $highlight) {
                DB::table('nakheel_project_highlights')->insert([
                    'nakheel_project_id' => $projectIds[$project['slug']],
                    'label' => $highlight['label'],
                    'label_ar' => $highlight['label_ar'],
                    'image' => null,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $properties = [
            [
                'title' => 'Villa B5', 'title_ar' => 'فيلا B5',
                'type' => 'Villa', 'type_ar' => 'فيلا',
                'status' => 'Purchasable', 'price' => 485000,
                'project' => 'lotus-compound',
                'location' => 'Tripoli, Libya', 'location_ar' => 'طرابلس، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '4', 'value_ar' => '4'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '4', 'value_ar' => '4'],
                    ['label' => 'Floors', 'label_ar' => 'الطوابق', 'value' => '2', 'value_ar' => '2'],
                ],
                'images' => ['nakheel-villa-1', 'nakheel-villa-1-b', 'nakheel-villa-1-c'],
                'floor_plans' => ['nakheel-villa-1-plan'],
                'featured_on_login' => true,
                'description' => "A distinguished four-bedroom villa positioned on the northern edge of Lotus Compound, offering panoramic views across the landscaped gardens and the compound's main pool. Villa B5 features open-plan living areas, a private garden terrace, and a double garage.",
                'description_ar' => 'فيلا مميزة مكونة من أربع غرف نوم تقع على الطرف الشمالي لمجمع لوتس، وتوفر إطلالات بانورامية على الحدائق المنسقة والمسبح الرئيسي للمجمع. تتميز فيلا B5 بمساحات معيشة مفتوحة، وتراس حديقة خاص، ومرآب مزدوج.',
                'features' => [
                    ['value' => 'Private Pool', 'value_ar' => 'مسبح خاص'],
                    ['value' => 'Garden Terrace', 'value_ar' => 'تراس حديقة'],
                    ['value' => 'Double Garage', 'value_ar' => 'مرآب مزدوج'],
                    ['value' => 'Smart Home', 'value_ar' => 'منزل ذكي'],
                    ['value' => "Maid's Room", 'value_ar' => 'غرفة خادمة'],
                    ['value' => 'Storage', 'value_ar' => 'تخزين'],
                ],
            ],
            [
                'title' => 'Villa C12', 'title_ar' => 'فيلا C12',
                'type' => 'Villa', 'type_ar' => 'فيلا',
                'status' => 'Booked', 'price' => 620000,
                'project' => 'lotus-compound',
                'location' => 'Tripoli, Libya', 'location_ar' => 'طرابلس، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '5', 'value_ar' => '5'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '5', 'value_ar' => '5'],
                    ['label' => 'Floors', 'label_ar' => 'الطوابق', 'value' => '2', 'value_ar' => '2'],
                ],
                'images' => ['nakheel-villa-2', 'nakheel-villa-2-b'],
                'floor_plans' => [],
                'featured_on_login' => false,
                'description' => 'The largest villa type in Lotus Compound, set on a corner plot with dual street frontage. Five spacious bedrooms, a formal dining room, and a private courtyard make Villa C12 ideal for larger families.',
                'description_ar' => 'أكبر نوع فيلا في مجمع لوتس، تقع على قطعة أرض زاوية بواجهتين على الشارع. خمس غرف نوم واسعة، وغرفة طعام رسمية، وفناء خاص تجعل فيلا C12 مثالية للعائلات الكبيرة.',
                'features' => [
                    ['value' => 'Private Pool', 'value_ar' => 'مسبح خاص'],
                    ['value' => 'Corner Plot', 'value_ar' => 'قطعة أرض زاوية'],
                    ['value' => 'Triple Garage', 'value_ar' => 'مرآب ثلاثي'],
                    ['value' => 'Smart Home', 'value_ar' => 'منزل ذكي'],
                    ['value' => "Maid's Room", 'value_ar' => 'غرفة خادمة'],
                    ['value' => 'Study', 'value_ar' => 'غرفة مكتب'],
                ],
            ],
            [
                'title' => 'Apartment 4A', 'title_ar' => 'شقة 4A',
                'type' => 'Apartment', 'type_ar' => 'شقة',
                'status' => 'Purchasable', 'price' => 175000,
                'project' => 'lotus-compound',
                'location' => 'Tripoli, Libya', 'location_ar' => 'طرابلس، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '3', 'value_ar' => '3'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '2', 'value_ar' => '2'],
                    ['label' => 'Floor', 'label_ar' => 'الطابق', 'value' => '4', 'value_ar' => '4'],
                ],
                'images' => ['nakheel-interior-1', 'nakheel-interior-1-b'],
                'floor_plans' => ['nakheel-interior-1-plan'],
                'featured_on_login' => false,
                'description' => "A bright, north-facing three-bedroom apartment overlooking the compound's central park. Apartment 4A includes a private balcony, an open-plan kitchen, and access to all Lotus Compound shared amenities.",
                'description_ar' => 'شقة مضيئة مكونة من ثلاث غرف نوم تطل على الحديقة المركزية للمجمع باتجاه الشمال. تشمل شقة 4A شرفة خاصة، ومطبخًا مفتوحًا، والوصول إلى جميع مرافق مجمع لوتس المشتركة.',
                'features' => [
                    ['value' => 'Balcony', 'value_ar' => 'شرفة'],
                    ['value' => 'Shared Pool Access', 'value_ar' => 'وصول لمسبح مشترك'],
                    ['value' => 'Covered Parking', 'value_ar' => 'موقف مغطى'],
                    ['value' => 'Storage Room', 'value_ar' => 'غرفة تخزين'],
                ],
            ],
            [
                'title' => 'Shore House 7', 'title_ar' => 'المنزل الشاطئي 7',
                'type' => 'Summer House', 'type_ar' => 'منزل صيفي',
                'status' => 'Purchasable', 'price' => 310000,
                'project' => 'azure-resort',
                'location' => 'Sabratha Coast, Libya', 'location_ar' => 'ساحل صبراتة، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '3', 'value_ar' => '3'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '3', 'value_ar' => '3'],
                    ['label' => 'Area (m²)', 'label_ar' => 'المساحة (م²)', 'value' => '240', 'value_ar' => '240'],
                ],
                'images' => ['nakheel-beach-1', 'nakheel-beach-1-b', 'nakheel-beach-1-c'],
                'floor_plans' => [],
                'featured_on_login' => false,
                'description' => 'A three-bedroom shore house steps from the Azure Resort private beach, with an outdoor dining terrace and direct marina access. Shore House 7 comes fully furnished and ready for the summer season.',
                'description_ar' => 'منزل شاطئي مكون من ثلاث غرف نوم على بعد خطوات من شاطئ منتجع أزور الخاص، مع تراس طعام خارجي ووصول مباشر إلى المرسى. يأتي المنزل الشاطئي 7 مفروشًا بالكامل وجاهزًا لموسم الصيف.',
                'features' => [
                    ['value' => 'Beachfront', 'value_ar' => 'على الشاطئ مباشرة'],
                    ['value' => 'Private Terrace', 'value_ar' => 'تراس خاص'],
                    ['value' => 'Marina Access', 'value_ar' => 'وصول إلى المرسى'],
                    ['value' => 'Fully Furnished', 'value_ar' => 'مفروش بالكامل'],
                ],
            ],
            [
                'title' => 'Shore House 14', 'title_ar' => 'المنزل الشاطئي 14',
                'type' => 'Summer House', 'type_ar' => 'منزل صيفي',
                'status' => 'Sold', 'price' => 265000,
                'project' => 'azure-resort',
                'location' => 'Sabratha Coast, Libya', 'location_ar' => 'ساحل صبراتة، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '2', 'value_ar' => '2'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '2', 'value_ar' => '2'],
                    ['label' => 'Area (m²)', 'label_ar' => 'المساحة (م²)', 'value' => '190', 'value_ar' => '190'],
                ],
                'images' => ['nakheel-beach-2'],
                'floor_plans' => [],
                'featured_on_login' => false,
                'description' => 'A cozy two-bedroom shore house tucked in a quieter row of Azure Resort, close to the beach club and water sports center. Ideal as a lock-up-and-leave summer retreat.',
                'description_ar' => 'منزل شاطئي دافئ مكون من غرفتي نوم يقع في صف أكثر هدوءًا داخل منتجع أزور، بالقرب من النادي الشاطئي ومركز الرياضات المائية. مثالي كملاذ صيفي جاهز للاستخدام الفوري.',
                'features' => [
                    ['value' => 'Beach Club Access', 'value_ar' => 'وصول إلى النادي الشاطئي'],
                    ['value' => 'Water Sports Center', 'value_ar' => 'مركز رياضات مائية'],
                    ['value' => 'Furnished Terrace', 'value_ar' => 'تراس مفروش'],
                ],
            ],
            [
                'title' => 'Apartment 12F', 'title_ar' => 'شقة 12F',
                'type' => 'Apartment', 'type_ar' => 'شقة',
                'status' => 'Rentable', 'price' => 142000,
                'project' => 'mira-residences',
                'location' => 'Benghazi, Libya', 'location_ar' => 'بنغازي، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '2', 'value_ar' => '2'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '2', 'value_ar' => '2'],
                    ['label' => 'Floor', 'label_ar' => 'الطابق', 'value' => '12', 'value_ar' => '12'],
                ],
                'images' => ['nakheel-interior-2', 'nakheel-interior-2-b'],
                'floor_plans' => [],
                'featured_on_login' => false,
                'description' => "A high-floor two-bedroom apartment in Mira Residences with unobstructed city views, available for rent. Includes access to the building's concierge desk and rooftop terrace.",
                'description_ar' => 'شقة مكونة من غرفتي نوم في طابق مرتفع بمساكن ميرا مع إطلالات مدينية خلابة، متاحة للإيجار. تشمل الوصول إلى مكتب الاستقبال والتراس العلوي للمبنى.',
                'features' => [
                    ['value' => 'City Views', 'value_ar' => 'إطلالات على المدينة'],
                    ['value' => 'Concierge Desk', 'value_ar' => 'مكتب استقبال'],
                    ['value' => 'Rooftop Terrace', 'value_ar' => 'تراس على السطح'],
                    ['value' => 'High-Speed Elevator', 'value_ar' => 'مصعد عالي السرعة'],
                ],
            ],
            [
                'title' => 'Apartment 18A', 'title_ar' => 'شقة 18A',
                'type' => 'Apartment', 'type_ar' => 'شقة',
                'status' => 'Purchasable', 'price' => 195000,
                'project' => 'mira-residences',
                'location' => 'Benghazi, Libya', 'location_ar' => 'بنغازي، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '3', 'value_ar' => '3'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '3', 'value_ar' => '3'],
                    ['label' => 'Floor', 'label_ar' => 'الطابق', 'value' => '18', 'value_ar' => '18'],
                ],
                'images' => ['nakheel-building-1', 'nakheel-building-1-b'],
                'floor_plans' => ['nakheel-building-1-plan'],
                'featured_on_login' => false,
                'description' => 'A premium three-bedroom apartment near the top of Mira Residences, with a wraparound balcony and dedicated underground parking for two vehicles.',
                'description_ar' => 'شقة فاخرة مكونة من ثلاث غرف نوم بالقرب من قمة مساكن ميرا، مع شرفة محيطية وموقف مخصص تحت الأرض لسيارتين.',
                'features' => [
                    ['value' => 'Wraparound Balcony', 'value_ar' => 'شرفة محيطية'],
                    ['value' => 'Underground Parking', 'value_ar' => 'موقف تحت الأرض'],
                    ['value' => 'Smart Home', 'value_ar' => 'منزل ذكي'],
                    ['value' => 'Concierge Desk', 'value_ar' => 'مكتب استقبال'],
                ],
            ],
            [
                'title' => 'Villa B9', 'title_ar' => 'فيلا B9',
                'type' => 'Villa', 'type_ar' => 'فيلا',
                'status' => 'Purchasable', 'price' => 510000,
                'project' => 'lotus-compound',
                'location' => 'Tripoli, Libya', 'location_ar' => 'طرابلس، ليبيا',
                'specs' => [
                    ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '4', 'value_ar' => '4'],
                    ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '4', 'value_ar' => '4'],
                    ['label' => 'Floors', 'label_ar' => 'الطوابق', 'value' => '2', 'value_ar' => '2'],
                ],
                'images' => ['nakheel-villa-3', 'nakheel-villa-3-b'],
                'floor_plans' => [],
                'featured_on_login' => false,
                'description' => "A four-bedroom villa on Lotus Compound's southern row, backing directly onto the central park. Villa B9 offers a private garden, double garage, and an oversized rooftop terrace.",
                'description_ar' => 'فيلا مكونة من أربع غرف نوم في الصف الجنوبي لمجمع لوتس، تطل مباشرة على الحديقة المركزية. توفر فيلا B9 حديقة خاصة، ومرآب مزدوج، وتراسًا علويًا واسعًا.',
                'features' => [
                    ['value' => 'Private Garden', 'value_ar' => 'حديقة خاصة'],
                    ['value' => 'Double Garage', 'value_ar' => 'مرآب مزدوج'],
                    ['value' => 'Rooftop Terrace', 'value_ar' => 'تراس على السطح'],
                    ['value' => 'Smart Home', 'value_ar' => 'منزل ذكي'],
                ],
            ],
        ];

        foreach ($properties as $property) {
            DB::table('nakheel_properties')->insert([
                'nakheel_project_id' => $projectIds[$property['project']],
                'title' => $property['title'],
                'title_ar' => $property['title_ar'],
                'type' => $property['type'],
                'type_ar' => $property['type_ar'],
                'status' => $property['status'],
                'price' => $property['price'],
                'location' => $property['location'],
                'location_ar' => $property['location_ar'],
                'specs' => json_encode($property['specs']),
                'description' => $property['description'],
                'description_ar' => $property['description_ar'],
                'features' => json_encode($property['features']),
                'images' => implode(',', $property['images']),
                'floor_plans' => implode(',', $property['floor_plans']),
                'featured_on_login' => $property['featured_on_login'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $statuses = [
            ['name' => 'Purchasable', 'name_ar' => 'قابل للشراء', 'color' => 'emerald', 'sort_order' => 1, 'is_sold' => false],
            ['name' => 'Booked', 'name_ar' => 'محجوز', 'color' => 'amber', 'sort_order' => 2, 'is_sold' => false],
            ['name' => 'Sold', 'name_ar' => 'مباع', 'color' => 'gray', 'sort_order' => 3, 'is_sold' => true],
            ['name' => 'Rentable', 'name_ar' => 'قابل للإيجار', 'color' => 'sky', 'sort_order' => 4, 'is_sold' => false],
        ];

        foreach ($statuses as $status) {
            DB::table('nakheel_property_statuses')->insert([
                ...$status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('nakheel_settings')->insertOrIgnore([
            'key' => 'show_sold_properties',
            'value' => '0',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $stats = [
            ['value' => '12+', 'value_ar' => '+12', 'label' => 'Years of Experience', 'label_ar' => 'سنوات الخبرة', 'sort_order' => 1],
            ['value' => '8', 'value_ar' => '8', 'label' => 'Projects Completed', 'label_ar' => 'مشاريع مكتملة', 'sort_order' => 2],
            ['value' => '300+', 'value_ar' => '+300', 'label' => 'Properties Delivered', 'label_ar' => 'عقارات تم تسليمها', 'sort_order' => 3],
            ['value' => '3', 'value_ar' => '3', 'label' => 'Cities Active', 'label_ar' => 'مدن نشطة', 'sort_order' => 4],
        ];

        foreach ($stats as $stat) {
            DB::table('nakheel_stats')->insert([
                ...$stat,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $timeline = [
            [
                'year' => '2014', 'status' => 'Completed', 'status_ar' => 'مكتمل', 'color' => 'emerald',
                'title' => 'Villa Residences — Phase I', 'title_ar' => 'مساكن الفلل — المرحلة الأولى',
                'meta' => 'Tripoli, Libya · Residential Buildings', 'meta_ar' => 'طرابلس، ليبيا · مبانٍ سكنية',
                'description' => "Nakheel's inaugural project — a 14-floor residential building that established the company's reputation for quality construction and on-time delivery.",
                'description_ar' => 'المشروع الافتتاحي لشركة نخيل — مبنى سكني من 14 طابقًا أرسى سمعة الشركة في جودة البناء والتسليم في الموعد المحدد.',
                'image' => 'nakheel-timeline-1', 'sort_order' => 1,
            ],
            [
                'year' => '2017', 'status' => 'Completed', 'status_ar' => 'مكتمل', 'color' => 'emerald',
                'title' => 'Azure Resort — Phase I', 'title_ar' => 'منتجع أزور — المرحلة الأولى',
                'meta' => 'Sabratha Coast, Libya', 'meta_ar' => 'ساحل صبراتة، ليبيا',
                'description' => "The company's first resort development, bringing Mediterranean-quality summer properties to the Libyan coastline for the first time.",
                'description_ar' => 'أول مشروع منتجع للشركة، حيث جلبت عقارات صيفية بجودة متوسطية إلى الساحل الليبي لأول مرة.',
                'image' => 'nakheel-timeline-2', 'sort_order' => 2,
            ],
            [
                'year' => '2020', 'status' => 'Completed', 'status_ar' => 'مكتمل', 'color' => 'emerald',
                'title' => 'Lotus Compound', 'title_ar' => 'مجمع لوتس',
                'meta' => 'Tripoli, Libya · Residential Compound', 'meta_ar' => 'طرابلس، ليبيا · مجمع سكني',
                'description' => "The flagship Lotus Compound — a 24-hectare gated community of villas and apartments setting a new benchmark for Tripoli.",
                'description_ar' => 'مجمع لوتس الرائد — مجتمع سكني مسوّر بمساحة 24 هكتارًا من الفلل والشقق يضع معيارًا جديدًا لطرابلس.',
                'image' => 'nakheel-timeline-3', 'sort_order' => 3,
            ],
            [
                'year' => '2022', 'status' => 'Completed', 'status_ar' => 'مكتمل', 'color' => 'emerald',
                'title' => 'Azure Resort — Phase II', 'title_ar' => 'منتجع أزور — المرحلة الثانية',
                'meta' => 'Sabratha Coast, Libya', 'meta_ar' => 'ساحل صبراتة، ليبيا',
                'description' => "Expansion of the Azure Resort with an added row of shore houses, a private marina, and the resort's signature beach club.",
                'description_ar' => 'توسعة منتجع أزور بإضافة صف من المنازل الشاطئية، ومرسى خاص، والنادي الشاطئي المميز للمنتجع.',
                'image' => 'nakheel-timeline-4', 'sort_order' => 4,
            ],
            [
                'year' => '2024', 'status' => 'Completed', 'status_ar' => 'مكتمل', 'color' => 'emerald',
                'title' => 'Villa Residences — Phase II', 'title_ar' => 'مساكن الفلل — المرحلة الثانية',
                'meta' => 'Benghazi, Libya', 'meta_ar' => 'بنغازي، ليبيا',
                'description' => "A new 22-floor tower building on the success of Phase I, offering premium city-view apartments in Benghazi.",
                'description_ar' => 'برج جديد من 22 طابقًا يبني على نجاح المرحلة الأولى، ويقدم شققًا فاخرة مطلة على المدينة في بنغازي.',
                'image' => 'nakheel-timeline-5', 'sort_order' => 5,
            ],
        ];

        foreach ($timeline as $entry) {
            DB::table('nakheel_timeline_entries')->insert([
                ...$entry,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $categories = [
            [
                'title' => 'Villas in Compounds', 'title_ar' => 'فلل داخل مجمعات',
                'description' => 'Our villa developments sit inside gated compounds offering privacy, prestige, and community. Each villa is designed with spacious living and set within landscaped grounds with 24-hour security, shared pools, and premium infrastructure.',
                'description_ar' => 'تقع مشاريع الفلل لدينا داخل مجمعات مسوّرة توفر الخصوصية والمكانة والمجتمع. صُممت كل فيلا بمساحات معيشة واسعة وضمن أراضٍ منسقة مع أمن على مدار الساعة، ومسابح مشتركة، وبنية تحتية متميزة.',
                'image' => 'nakheel-cat-1', 'sort_order' => 1,
            ],
            [
                'title' => 'Summer Houses in Resorts', 'title_ar' => 'منازل صيفية في المنتجعات',
                'description' => "Living on the Libyan Mediterranean coastline. Our summer house developments combine private ownership with resort-managed amenities — private beaches, marinas, and beach clubs.",
                'description_ar' => 'الحياة على الساحل الليبي المتوسطي. تجمع مشاريع المنازل الصيفية لدينا بين الملكية الخاصة والمرافق التي يديرها المنتجع — شواطئ خاصة، ومراسٍ، ونوادٍ شاطئية.',
                'image' => 'nakheel-cat-2', 'sort_order' => 2,
            ],
            [
                'title' => 'Residential Buildings & Apartments', 'title_ar' => 'المباني السكنية والشقق',
                'description' => 'From boutique mid-rise buildings to landmark towers, our residential building portfolio delivers modern apartments with smart building infrastructure, concierge services, and thoughtful urban design in Libya\'s major cities.',
                'description_ar' => 'من المباني المتوسطة الارتفاع الأنيقة إلى الأبراج المميزة، تقدم محفظة المباني السكنية لدينا شققًا عصرية ببنية تحتية ذكية، وخدمات استقبال، وتصميمًا حضريًا مدروسًا في أهم مدن ليبيا.',
                'image' => 'nakheel-cat-3', 'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('nakheel_categories')->insert([
                ...$category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $upcoming = [
            [
                'badge' => 'Pre-Development', 'badge_ar' => 'قيد التطوير المبدئي',
                'title' => 'Pearl Coast Resort', 'title_ar' => 'منتجع ساحل اللؤلؤة',
                'description' => "Libya's first fully-integrated beachfront destination — combining a 5-star hotel, marina, and 60 private summer houses along the Mediterranean coastline in Sabratha.",
                'description_ar' => 'أول وجهة ساحلية متكاملة بالكامل في ليبيا — تجمع بين فندق خمس نجوم، ومرسى، و60 منزلاً صيفيًا خاصًا على طول الساحل المتوسطي في صبراتة.',
                'launch' => 'Expected launch: 2026', 'launch_ar' => 'الإطلاق المتوقع: 2026',
                'image' => 'nakheel-future-1', 'sort_order' => 1,
            ],
            [
                'badge' => 'Planned', 'badge_ar' => 'مخطط له',
                'title' => 'Tripoli Business Park', 'title_ar' => 'مجمع طرابلس التجاري',
                'description' => "A landmark mixed-use development in Tripoli's commercial center combining Grade-A office space, retail, and a premium serviced apartment complex.",
                'description_ar' => 'مشروع متعدد الاستخدامات بارز في مركز طرابلس التجاري يجمع بين مساحات مكتبية من الفئة أ، ومحلات تجارية، ومجمع شقق فندقية متميز.',
                'launch' => 'Expected launch: 2027', 'launch_ar' => 'الإطلاق المتوقع: 2027',
                'image' => 'nakheel-future-2', 'sort_order' => 2,
            ],
            [
                'badge' => 'Pre-Development', 'badge_ar' => 'قيد التطوير المبدئي',
                'title' => 'Horizon Residences', 'title_ar' => 'مساكن هورايزون',
                'description' => 'A 120-unit gated compound in Tripoli\'s western expansion zone, targeting the growing demand for premium family housing in the city\'s fastest-growing suburb.',
                'description_ar' => 'مجمع مسوّر من 120 وحدة في منطقة التوسع الغربي لطرابلس، يستهدف الطلب المتنامي على الإسكان العائلي الفاخر في أسرع ضواحي المدينة نموًا.',
                'launch' => 'Expected launch: 2026', 'launch_ar' => 'الإطلاق المتوقع: 2026',
                'image' => 'nakheel-future-3', 'sort_order' => 3,
            ],
            [
                'badge' => 'Planned', 'badge_ar' => 'مخطط له',
                'title' => 'Benghazi Towers', 'title_ar' => 'أبراج بنغازي',
                'description' => "A twin-tower residential development anchoring Benghazi's revitalized waterfront — 200 apartments, ground-floor retail, and a signature amenity deck.",
                'description_ar' => 'مشروع سكني ببرجين توأمين يرسي واجهة بنغازي البحرية المتجددة — 200 شقة، ومحلات تجارية في الطابق الأرضي، وسطح مرافق مميز.',
                'launch' => 'Expected launch: 2028', 'launch_ar' => 'الإطلاق المتوقع: 2028',
                'image' => 'nakheel-future-4', 'sort_order' => 4,
            ],
        ];

        foreach ($upcoming as $item) {
            DB::table('nakheel_upcoming_projects')->insert([
                ...$item,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $whyInvest = [
            [
                'title' => 'Strategic Locations', 'title_ar' => 'مواقع استراتيجية',
                'description' => 'Every Nakheel development is chosen for its long-term growth potential — from established city centers to emerging coastal and suburban corridors across Libya.',
                'description_ar' => 'يتم اختيار كل مشروع من مشاريع نخيل لإمكاناته النمو طويلة المدى — من مراكز المدن الراسخة إلى الممرات الساحلية والضاحية الناشئة في جميع أنحاء ليبيا.',
                'image' => null, 'sort_order' => 1,
            ],
            [
                'title' => 'High-Quality Development', 'title_ar' => 'تطوير عالي الجودة',
                'description' => 'We build to investment-grade standards, using premium materials and construction partners held to strict quality and timeline benchmarks on every project.',
                'description_ar' => 'نبني وفق معايير استثمارية عالية، باستخدام مواد فاخرة وشركاء بناء يلتزمون بمعايير صارمة للجودة والمواعيد في كل مشروع.',
                'image' => null, 'sort_order' => 2,
            ],
            [
                'title' => 'Experienced Development Team', 'title_ar' => 'فريق تطوير ذو خبرة',
                'description' => 'Over a decade of delivery experience across residential compounds, coastal resorts, and urban residential buildings gives our team unmatched execution capability.',
                'description_ar' => 'أكثر من عقد من الخبرة في التسليم عبر المجمعات السكنية، والمنتجعات الساحلية، والمباني السكنية الحضرية يمنح فريقنا قدرة تنفيذ لا مثيل لها.',
                'image' => null, 'sort_order' => 3,
            ],
            [
                'title' => 'Future Growth Potential', 'title_ar' => 'إمكانات نمو مستقبلية',
                'description' => "Nakheel's pipeline of upcoming developments and expanding market presence positions early investors to benefit from sustained property value growth.",
                'description_ar' => 'يضع خط أنابيب مشاريع نخيل القادمة وحضورها المتنامي في السوق المستثمرين الأوائل في موقع يستفيدون فيه من نمو مستدام في قيمة العقارات.',
                'image' => null, 'sort_order' => 4,
            ],
        ];

        foreach ($whyInvest as $reason) {
            DB::table('nakheel_why_invest_items')->insert([
                ...$reason,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // The original preset icons, available by default in the Why-Invest
        // icon picker. See App\Support\WhyInvestIcons for the full trusted
        // catalog the Main Admin can add more of via Icon Management.
        $defaultWhyInvestIcons = [
            'location', 'security', 'investment', 'quality', 'buildings',
            'construction', 'community', 'lifestyle', 'growth', 'reliability',
        ];

        foreach ($defaultWhyInvestIcons as $key) {
            DB::table('nakheel_why_invest_icons')->insert([
                'key' => $key,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $pageContent = [
            [
                'key' => 'about_story',
                'heading' => "Building Libya's finest addresses since 2012",
                'heading_ar' => 'نبني أرقى العناوين في ليبيا منذ عام 2012',
                'body' => "Nakheel Properties was founded in Tripoli with a single ambition: to develop real estate that sets a new standard for quality, design, and long-term value in Libya. Over the past decade we have grown from a single residential project into a diversified property development and investment company operating across three of Libya's major cities.\n\nWe specialize in three categories of real estate — gated villa compounds, coastal resort developments, and urban residential buildings — and we approach each project with the same commitment to architectural excellence, investment-grade construction, and exceptional delivery.",
                'body_ar' => "تأسست نخيل العقارية في طرابلس بطموح واحد: تطوير عقارات ترسي معيارًا جديدًا للجودة والتصميم والقيمة طويلة المدى في ليبيا. على مدى العقد الماضي، نمت الشركة من مشروع سكني واحد إلى شركة تطوير واستثمار عقاري متنوعة تعمل في ثلاث من أهم مدن ليبيا.\n\nنحن متخصصون في ثلاث فئات من العقارات — المجمعات السكنية المسوّرة، والمشاريع المنتجعية الساحلية، والمباني السكنية الحضرية — ونتعامل مع كل مشروع بنفس الالتزام بالتميز المعماري، والبناء بمعايير استثمارية، والتسليم الاستثنائي.",
                'image' => null,
                'phone' => null,
                'whatsapp' => null,
                'telegram' => null,
                'email' => null,
                'office_address' => null,
                'office_address_ar' => null,
                'office_hours' => null,
                'office_hours_ar' => null,
            ],
            [
                'key' => 'future_vision',
                'heading' => 'Our Future Vision',
                'heading_ar' => 'رؤيتنا المستقبلية',
                'body' => "Nakheel's next chapter focuses on expanding into new Libyan markets, developing Libya's first fully integrated resort-town destination on the western Mediterranean coast, and establishing investment partnership structures for international buyers.\n\nWe are also entering the commercial real estate sector with two planned business park developments in Tripoli and Misrata, and a landmark branded residences project in partnership with a leading international hospitality group.",
                'body_ar' => "يركّز الفصل القادم من مسيرة نخيل على التوسع في أسواق ليبية جديدة، وتطوير أول وجهة منتجعية متكاملة بالكامل في ليبيا على الساحل المتوسطي الغربي، وإنشاء هياكل شراكة استثمارية للمشترين الدوليين.\n\nكما ندخل قطاع العقارات التجارية بمشروعين مخطط لهما للمجمعات التجارية في طرابلس ومصراتة، ومشروع سكني مميز بالشراكة مع مجموعة ضيافة دولية رائدة.",
                'image' => 'nakheel-future-vision',
                'phone' => null,
                'whatsapp' => null,
                'telegram' => null,
                'email' => null,
                'office_address' => null,
                'office_address_ar' => null,
                'office_hours' => null,
                'office_hours_ar' => null,
            ],
            [
                'key' => 'office_contact',
                'heading' => 'Get in Touch',
                'heading_ar' => 'تواصل معنا',
                'body' => "Have a question about one of our upcoming developments or want to discuss investment opportunities? Our team is on hand to help.",
                'body_ar' => 'هل لديك سؤال حول أحد مشاريعنا القادمة أو ترغب في مناقشة فرص الاستثمار؟ فريقنا جاهز لمساعدتك.',
                'image' => null,
                'phone' => '+218 91 000 0000',
                'whatsapp' => '+218 91 000 0000',
                'telegram' => '@nakheelproperties',
                'email' => 'invest@nakheel.ly',
                'office_address' => 'Nakheel Properties — Head Office, Tripoli, Libya',
                'office_address_ar' => 'نخيل العقارية — المكتب الرئيسي، طرابلس، ليبيا',
                'office_hours' => 'Sun–Thu: 9:00 AM – 5:00 PM',
                'office_hours_ar' => 'الأحد–الخميس: 9:00 صباحًا – 5:00 مساءً',
            ],
        ];

        foreach ($pageContent as $content) {
            DB::table('nakheel_page_content')->insert([
                ...$content,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($this->command) {
            $this->command->getOutput()->writeln('');
            $this->command->info(sprintf(
                'Seeded %d Nakheel projects and %d properties.',
                DB::table('nakheel_projects')->count(),
                DB::table('nakheel_properties')->count(),
            ));
        }
    }
}
