<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Property specification rules (AdminPropertyController::validateSpecRow()):
 * values are whole numbers, or decimals only for a measurement whose name
 * carries a unit like "Area (m²)"; names are words. Enforced server-side on
 * both create and update, regardless of what the browser allows.
 */
class PropertySpecsValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@example.com')->firstOrFail();
    }

    private function payload(array $specs): array
    {
        return [
            'nakheel_project_id' => '', // the form's "no project" option
            'title' => 'Spec Test Villa',
            'type' => 'Villa',
            'status' => DB::table('nakheel_property_statuses')->orderBy('id')->value('name'),
            'price' => 100000,
            'location' => 'Tripoli',
            'specs' => $specs,
            'features' => '[]',
        ];
    }

    private function storedSpecs(): array
    {
        return json_decode(DB::table('nakheel_properties')->where('title', 'Spec Test Villa')->value('specs'), true);
    }

    private function bedrooms(string $value, ?string $valueAr = null): array
    {
        return ['a' => ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => $value, 'value_ar' => $valueAr]];
    }

    public function test_whole_number_specs_with_arabic_names_are_saved(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                'a' => ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '4', 'value_ar' => '4'],
                'b' => ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '3', 'value_ar' => '3'],
            ]))
            ->assertRedirect('/properties')
            ->assertSessionHasNoErrors();

        $this->assertSame([
            ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '4', 'value_ar' => '4'],
            ['label' => 'Bathrooms', 'label_ar' => 'الحمامات', 'value' => '3', 'value_ar' => '3'],
        ], $this->storedSpecs());
    }

    #[DataProvider('invalidCountValues')]
    public function test_non_whole_number_values_are_rejected_and_nothing_is_saved(string $value): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload($this->bedrooms($value)))
            ->assertSessionHasErrors('specs.a');

        $this->assertDatabaseMissing('nakheel_properties', ['title' => 'Spec Test Villa']);
    }

    public static function invalidCountValues(): array
    {
        return [
            'banana' => ['banana'],
            'four' => ['four'],
            '4.5' => ['4.5'],
            '3abc' => ['3abc'],
            'negative' => ['-2'],
            'above the 1000 limit' => ['1001'],
        ];
    }

    public function test_the_arabic_value_field_is_validated_too(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload($this->bedrooms('4', 'banana')))
            ->assertSessionHasErrors('specs.a');
    }

    public function test_arabic_keyboard_digits_are_accepted_as_the_same_number(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload($this->bedrooms('4', '٤')))
            ->assertSessionHasNoErrors();

        $this->assertSame('4', $this->storedSpecs()[0]['value_ar']);
    }

    public function test_decimals_are_allowed_only_for_a_measurement_with_a_unit_in_its_name(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                'a' => ['label' => 'Area (m²)', 'label_ar' => 'المساحة (م²)', 'value' => '240.5', 'value_ar' => '240.5'],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('240.5', $this->storedSpecs()[0]['value']);
    }

    public function test_a_measurement_still_rejects_text(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                'a' => ['label' => 'Area (m²)', 'label_ar' => 'المساحة (م²)', 'value' => '240 m²', 'value_ar' => null],
            ]))
            ->assertSessionHasErrors('specs.a');
    }

    public function test_numeric_only_names_are_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                'a' => ['label' => '123', 'label_ar' => 'غرف النوم', 'value' => '4', 'value_ar' => null],
            ]))
            ->assertSessionHasErrors('specs.a');
    }

    public function test_a_value_needs_a_name_and_a_name_needs_a_value(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload(['a' => ['label' => '', 'label_ar' => '', 'value' => '4', 'value_ar' => null]]))
            ->assertSessionHasErrors('specs.a');

        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload(['a' => ['label' => 'Bedrooms', 'label_ar' => '', 'value' => '', 'value_ar' => null]]))
            ->assertSessionHasErrors('specs.a');
    }

    public function test_an_arabic_only_row_is_kept(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                'a' => ['label' => '', 'label_ar' => 'غرف النوم', 'value' => '', 'value_ar' => '2'],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertCount(1, $this->storedSpecs());
    }

    public function test_update_rejects_invalid_values_and_leaves_the_stored_specs_untouched(): void
    {
        $property = DB::table('nakheel_properties')->orderBy('id')->first();

        $this->actingAs($this->admin())
            ->put("/admin/properties/{$property->id}", array_merge($this->payload($this->bedrooms('banana')), ['title' => $property->title]))
            ->assertSessionHasErrors('specs.a');

        $this->assertSame($property->specs, DB::table('nakheel_properties')->where('id', $property->id)->value('specs'));
    }

    public function test_every_existing_property_can_be_re_saved_with_its_own_specs(): void
    {
        foreach (DB::table('nakheel_properties')->get() as $property) {
            $specs = collect(json_decode($property->specs ?? '[]', true) ?: [])
                ->mapWithKeys(fn ($spec, $i) => ["row$i" => $spec])->all();

            $this->actingAs($this->admin());
            $this
                ->put("/admin/properties/{$property->id}", [
                    'edit_version' => $this->editVersion("/admin/properties/{$property->id}/edit"),
                    'nakheel_project_id' => $property->nakheel_project_id ?? '',
                    'title' => $property->title,
                    'title_ar' => $property->title_ar,
                    'type' => $property->type,
                    'type_ar' => $property->type_ar,
                    'status' => $property->status,
                    'price' => $property->price,
                    'location' => $property->location,
                    'location_ar' => $property->location_ar,
                    'specs' => $specs,
                    'features' => $property->features ?? '[]',
                ])
                ->assertSessionHasNoErrors()
                ->assertRedirect('/properties');

            $this->assertEquals(
                json_decode($property->specs ?? '[]', true),
                json_decode(DB::table('nakheel_properties')->where('id', $property->id)->value('specs'), true),
                "Specs of {$property->title} changed on re-save"
            );
        }
    }

    public function test_every_seeded_property_spec_satisfies_the_rules(): void
    {
        foreach (DB::table('nakheel_properties')->pluck('specs') as $json) {
            foreach (json_decode($json ?? '[]', true) ?: [] as $spec) {
                $isMeasurement = (bool) preg_match('/\(.+\)/u', ($spec['label'] ?? '') . ' ' . ($spec['label_ar'] ?? ''));
                $this->assertMatchesRegularExpression($isMeasurement ? '/^\d+(\.\d{1,2})?$/' : '/^\d+$/', (string) $spec['value'], "Seeded spec {$spec['label']}");
            }
        }
    }

    public function test_saved_specs_display_on_the_listing(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                'a' => ['label' => 'Bedrooms', 'label_ar' => 'غرف النوم', 'value' => '4', 'value_ar' => '4'],
                'b' => ['label' => 'Area (m²)', 'label_ar' => 'المساحة (م²)', 'value' => '240', 'value_ar' => '240'],
            ]));

        $this->actingAs($this->admin())->get('/properties')
            ->assertOk()
            ->assertSee('4 Bedrooms · 240 Area (m²)', false);
    }
}
