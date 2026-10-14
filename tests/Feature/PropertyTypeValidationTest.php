<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Property type ("نوع العقار"), English and Arabic — see
 * AdminPropertyController::validatePropertyType(). Enforced server-side on
 * both create and edit; invalid input never reaches the database.
 */
class PropertyTypeValidationTest extends TestCase
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

    private function payload(array $overrides): array
    {
        return array_merge([
            'nakheel_project_id' => '',
            'title' => 'Type Test Home',
            'type' => 'Villa',
            'type_ar' => 'فيلا',
            'status' => DB::table('nakheel_property_statuses')->orderBy('id')->value('name'),
            'price' => 100000,
            'location' => 'Tripoli',
            'features' => '[]',
        ], $overrides);
    }

    public static function validTypes(): array
    {
        return [
            'Villa' => ['Villa', 'فيلا'],
            'Apartment' => ['Apartment', 'شقة'],
            'Summer House (two words)' => ['Summer House', 'منزل صيفي'],
            'several types' => ['Villas, Apartments', 'فلل، شقق'],
            'slash-separated' => ['Villa / Townhouse', 'فيلا / تاون هاوس'],
            'hyphenated' => ['Multi-Family', 'متعدد-العائلات'],
            'English only' => ['Penthouse', ''],
            'Arabic only' => ['', 'شقة'],
        ];
    }

    #[DataProvider('validTypes')]
    public function test_legitimate_types_are_accepted(string $type, string $typeAr): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload(['type' => $type, 'type_ar' => $typeAr]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/properties');

        $this->assertDatabaseHas('nakheel_properties', ['title' => 'Type Test Home']);
    }

    public static function invalidTypes(): array
    {
        return [
            '1f12f (the reported bug)' => ['1f12f'],
            'numbers only' => ['123'],
            'Arabic-keyboard digits' => ['١٢٣'],
            'letters and digits' => ['1e2r1'],
            'word with a number' => ['Villa2'],
            'symbols' => ['@@@'],
            'a number among several types' => ['Villas, 123'],
            'empty item between commas' => ['Villas,,Apartments'],
        ];
    }

    #[DataProvider('invalidTypes')]
    public function test_invalid_english_types_are_rejected_on_create(string $type): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload(['type' => $type]))
            ->assertSessionHasErrors('type');

        $this->assertDatabaseMissing('nakheel_properties', ['title' => 'Type Test Home']);
    }

    #[DataProvider('invalidTypes')]
    public function test_invalid_arabic_types_are_rejected_on_create(string $type): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload(['type_ar' => $type]))
            ->assertSessionHasErrors('type_ar');

        $this->assertDatabaseMissing('nakheel_properties', ['title' => 'Type Test Home']);
    }

    public function test_1f12f_is_rejected_on_edit_and_the_stored_property_is_untouched(): void
    {
        $before = DB::table('nakheel_properties')->orderBy('id')->first();

        $this->actingAs($this->admin())
            ->put("/admin/properties/{$before->id}", $this->payload(['title' => $before->title, 'type' => '1f12f', 'type_ar' => '1f12f']))
            ->assertSessionHasErrors(['type', 'type_ar']);

        $this->assertEquals($before, DB::table('nakheel_properties')->where('id', $before->id)->first());
    }

    public function test_a_type_is_still_required_in_at_least_one_language(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/properties', $this->payload(['type' => '', 'type_ar' => '']))
            ->assertSessionHasErrors(['type', 'type_ar']);
    }

    public function test_every_seeded_property_type_is_valid(): void
    {
        $property = DB::table('nakheel_properties')->orderBy('id')->first();

        foreach (DB::table('nakheel_properties')->get(['type', 'type_ar']) as $row) {
            $this->actingAs($this->admin());
            $this->put("/admin/properties/{$property->id}", $this->payload([
                'title' => $property->title, 'type' => $row->type, 'type_ar' => $row->type_ar ?? '',
                'edit_version' => $this->editVersion("/admin/properties/{$property->id}/edit"),
            ]))->assertSessionHasNoErrors();
        }
    }
}
