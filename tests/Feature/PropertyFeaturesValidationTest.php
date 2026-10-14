<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Property features (AdminPropertyController::validateFeatures()): no
 * digits anywhere, in English or Arabic; enforced server-side on create
 * and edit, and a rejected edit leaves the stored features untouched.
 */
class PropertyFeaturesValidationTest extends TestCase
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

    private function payload(mixed $features): array
    {
        return [
            'nakheel_project_id' => '',
            'title' => 'Feature Test Home',
            'type' => 'Villa',
            'status' => DB::table('nakheel_property_statuses')->orderBy('id')->value('name'),
            'price' => 100000,
            'location' => 'Tripoli',
            'features' => is_string($features) ? $features : json_encode($features),
        ];
    }

    private function storedFeatures(): array
    {
        return json_decode(DB::table('nakheel_properties')->where('title', 'Feature Test Home')->value('features'), true);
    }

    public function test_descriptive_english_and_arabic_features_are_saved(): void
    {
        $features = [
            ['value' => 'Private Garden', 'value_ar' => 'حديقة خاصة'],
            ['value' => "Children's Play Area", 'value_ar' => 'منطقة ألعاب للأطفال'],
            ['value' => 'Sea-view, open-plan kitchen (fitted)', 'value_ar' => 'مطبخ مفتوح (مجهز)'],
            ['value' => 'Rooftop Terrace', 'value_ar' => null],
        ];

        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload($features))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/properties');

        $this->assertSame($features, $this->storedFeatures());
    }

    public static function valuesWithDigits(): array
    {
        return [
            'numbers only' => ['123'],
            'Feature123' => ['Feature123'],
            '1f12f' => ['1f12f'],
            'digit in the middle' => ['Garage 2 cars'],
            'Arabic-keyboard digit' => ['حديقة ٢'],
            'Persian digit' => ['باغ ۳'],
        ];
    }

    #[DataProvider('valuesWithDigits')]
    public function test_english_values_with_digits_are_rejected_on_create(string $value): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([['value' => 'Private Garden', 'value_ar' => null], ['value' => $value, 'value_ar' => null]]))
            ->assertSessionHasErrors('features');

        $this->assertDatabaseMissing('nakheel_properties', ['title' => 'Feature Test Home']);
    }

    #[DataProvider('valuesWithDigits')]
    public function test_arabic_values_with_digits_are_rejected_on_create(string $value): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([['value' => 'Private Garden', 'value_ar' => $value]]))
            ->assertSessionHasErrors('features');

        $this->assertDatabaseMissing('nakheel_properties', ['title' => 'Feature Test Home']);
    }

    public function test_digits_are_rejected_on_edit_and_stored_features_are_untouched(): void
    {
        $property = DB::table('nakheel_properties')->whereNotNull('features')->where('features', '!=', '[]')->orderBy('id')->first();

        $this->actingAs($this->admin())
            ->put("/admin/properties/{$property->id}", array_merge(
                $this->payload([['value' => 'Feature123', 'value_ar' => null]]),
                ['title' => $property->title]
            ))
            ->assertSessionHasErrors('features');

        $this->assertEquals($property, DB::table('nakheel_properties')->where('id', $property->id)->first());
    }

    public function test_valid_features_are_saved_on_edit(): void
    {
        $property = DB::table('nakheel_properties')->orderBy('id')->first();
        $features = [['value' => 'Smart Home', 'value_ar' => 'منزل ذكي']];

        $this->actingAs($this->admin());
        $this->put("/admin/properties/{$property->id}", array_merge($this->payload($features), [
            'title' => $property->title,
            'edit_version' => $this->editVersion("/admin/properties/{$property->id}/edit"),
        ]))->assertSessionHasNoErrors();

        $this->assertSame($features, json_decode(DB::table('nakheel_properties')->where('id', $property->id)->value('features'), true));
    }

    public function test_blank_and_whitespace_rows_are_dropped_as_before(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([
                ['value' => '   ', 'value_ar' => '  '],
                ['value' => '', 'value_ar' => null],
                ['value' => '  Swimming Pool  ', 'value_ar' => ' مسبح '],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame([['value' => 'Swimming Pool', 'value_ar' => 'مسبح']], $this->storedFeatures());
    }

    public function test_no_features_at_all_is_still_allowed(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload([]))
            ->assertSessionHasNoErrors();

        $this->assertSame([], $this->storedFeatures());
    }

    public function test_a_malformed_features_payload_is_rejected_instead_of_wiping_features(): void
    {
        $property = DB::table('nakheel_properties')->whereNotNull('features')->where('features', '!=', '[]')->orderBy('id')->first();

        foreach (['not json', '{"value":"Garden"}', '[["Garden"]]', '[{"value":["x"]}]'] as $payload) {
            $this->actingAs($this->admin())
                ->put("/admin/properties/{$property->id}", array_merge($this->payload($payload), ['title' => $property->title]))
                ->assertSessionHasErrors('features');
        }

        $this->assertSame($property->features, DB::table('nakheel_properties')->where('id', $property->id)->value('features'));
    }

    public function test_every_seeded_feature_is_valid(): void
    {
        $property = DB::table('nakheel_properties')->orderBy('id')->first();

        foreach (DB::table('nakheel_properties')->pluck('features') as $features) {
            $this->actingAs($this->admin());
            $this->put("/admin/properties/{$property->id}", array_merge($this->payload($features ?? '[]'), [
                'title' => $property->title,
                'edit_version' => $this->editVersion("/admin/properties/{$property->id}/edit"),
            ]))->assertSessionHasNoErrors();
        }
    }
}
