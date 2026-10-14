<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Two admins editing the same property/project (GuardsConcurrentEdits):
 * whoever saves second is told instead of silently overwriting the first
 * admin's changes, and an edit of a since-deleted record fails cleanly.
 */
class ConcurrentEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function adminA(): User
    {
        return User::where('email', 'admin@example.com')->firstOrFail();
    }

    private function adminB(): User
    {
        return User::firstOrCreate(['email' => 'second@example.com'], [
            'name' => 'second@example.com',
            'password' => 'Second-Admin-Strong-1!',
            'is_admin' => true,
            'role' => 'main',
        ]);
    }

    private function property(): object
    {
        return DB::table('nakheel_properties')->orderBy('id')->first();
    }

    /** The property's edit form as a browser would submit it, with changes. */
    private function propertyForm(object $p, string $version, array $changes = []): array
    {
        return array_merge([
            'edit_version' => $version,
            'nakheel_project_id' => $p->nakheel_project_id ?? '',
            'title' => $p->title, 'title_ar' => $p->title_ar,
            'type' => $p->type, 'type_ar' => $p->type_ar,
            'status' => $p->status, 'price' => $p->price,
            'location' => $p->location, 'location_ar' => $p->location_ar,
            'specs' => collect(json_decode($p->specs ?? '[]', true) ?: [])->mapWithKeys(fn ($s, $i) => ["r$i" => $s])->all(),
            'features' => $p->features ?? '[]',
            'description' => $p->description, 'description_ar' => $p->description_ar,
        ], $changes);
    }

    private function openPropertyForm(User $admin, int $id): string
    {
        $this->actingAs($admin);

        return $this->editVersion("/admin/properties/{$id}/edit");
    }

    // --- Properties ---------------------------------------------------------

    public function test_a_single_admin_edit_still_saves_normally(): void
    {
        $p = $this->property();
        $version = $this->openPropertyForm($this->adminA(), $p->id);
        $this->assertNotSame('', $version);

        $this->put("/admin/properties/{$p->id}", $this->propertyForm($p, $version, ['price' => 999000]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/properties');

        $this->assertSame(999000, (int) DB::table('nakheel_properties')->where('id', $p->id)->value('price'));
    }

    public function test_the_second_save_is_refused_instead_of_overwriting_the_first(): void
    {
        $p = $this->property();
        $versionA = $this->openPropertyForm($this->adminA(), $p->id);
        $versionB = $this->openPropertyForm($this->adminB(), $p->id);

        // B saves first: new price.
        $this->actingAs($this->adminB())
            ->put("/admin/properties/{$p->id}", $this->propertyForm($p, $versionB, ['price' => 777000]))
            ->assertSessionHasNoErrors();

        // A saves second from the form they opened earlier (old price, new title).
        $this->actingAs($this->adminA())
            ->put("/admin/properties/{$p->id}", $this->propertyForm($p, $versionA, ['title' => 'A Renamed It']))
            ->assertRedirect("/admin/properties/{$p->id}/edit")
            ->assertSessionHasErrors('edit_version');

        $row = DB::table('nakheel_properties')->where('id', $p->id)->first();
        $this->assertSame(777000, (int) $row->price, "B's change must survive");
        $this->assertSame($p->title, $row->title, "A's stale save must not be written");

        // A reloads the form (fresh version) and can now save on top of B's change.
        $fresh = $this->editVersion("/admin/properties/{$p->id}/edit");
        $this->put("/admin/properties/{$p->id}", $this->propertyForm(DB::table('nakheel_properties')->where('id', $p->id)->first(), $fresh, ['title' => 'A Renamed It']))
            ->assertSessionHasNoErrors();
        $row = DB::table('nakheel_properties')->where('id', $p->id)->first();
        $this->assertSame(['A Renamed It', 777000], [$row->title, (int) $row->price]);
    }

    public function test_the_conflict_message_is_shown_on_the_reloaded_form(): void
    {
        $p = $this->property();
        $versionA = $this->openPropertyForm($this->adminA(), $p->id);
        DB::table('nakheel_properties')->where('id', $p->id)->update(['price' => 1]); // someone else's save

        $this->put("/admin/properties/{$p->id}", $this->propertyForm($p, $versionA))
            ->assertRedirect("/admin/properties/{$p->id}/edit");
        $this->followingRedirects()->get("/admin/properties/{$p->id}/edit")
            ->assertSee('Another admin changed this property after you opened it');
    }

    public function test_a_missing_or_tampered_version_is_refused(): void
    {
        $p = $this->property();
        $this->actingAs($this->adminA());

        foreach (['', 'not-a-version', str_repeat('0', 64)] as $bad) {
            $this->put("/admin/properties/{$p->id}", $this->propertyForm($p, $bad, ['price' => 5]))
                ->assertSessionHasErrors('edit_version');
        }
        $this->assertSame((int) $p->price, (int) DB::table('nakheel_properties')->where('id', $p->id)->value('price'));
    }

    public function test_an_admins_own_photo_upload_does_not_block_their_save_or_get_dropped(): void
    {
        $p = $this->property();
        $version = $this->openPropertyForm($this->adminA(), $p->id);

        // A photo added live on the edit page after the form was opened (AdminImageController does this).
        $images = trim(($p->images ?? '') . ',nakheel-images/new-photo.jpg', ',');
        DB::table('nakheel_properties')->where('id', $p->id)->update(['images' => $images, 'updated_at' => now()->addMinute()]);

        $this->put("/admin/properties/{$p->id}", $this->propertyForm($p, $version, ['price' => 123000]))
            ->assertSessionHasNoErrors();

        $row = DB::table('nakheel_properties')->where('id', $p->id)->first();
        $this->assertSame(123000, (int) $row->price);
        $this->assertSame($images, $row->images, 'the photo added after opening the form must be kept');
    }

    public function test_saving_a_property_that_another_admin_deleted_fails_cleanly(): void
    {
        $p = $this->property();
        $version = $this->openPropertyForm($this->adminA(), $p->id);

        $this->actingAs($this->adminB())->delete("/admin/properties/{$p->id}");
        $this->assertDatabaseMissing('nakheel_properties', ['id' => $p->id]);

        $this->actingAs($this->adminA())
            ->put("/admin/properties/{$p->id}", $this->propertyForm($p, $version))
            ->assertRedirect('/properties')
            ->assertSessionHas('error', 'This property was deleted by another admin, so your changes could not be saved.');

        $this->assertDatabaseMissing('nakheel_properties', ['id' => $p->id]);
    }

    // --- Projects -----------------------------------------------------------

    private function projectForm(int $id, string $version, array $changes = [], array $highlightLabels = []): array
    {
        $p = (array) DB::table('nakheel_projects')->where('id', $id)->first();
        $highlights = collect(DB::table('nakheel_project_highlights')->where('nakheel_project_id', $id)->orderBy('id')->get())
            ->mapWithKeys(fn ($h) => ["h{$h->id}" => ['id' => $h->id, 'label' => $highlightLabels[$h->id] ?? $h->label, 'label_ar' => $h->label_ar]])
            ->all();

        return array_merge(
            collect($p)->except(['id', 'hero_images', 'environment_images', 'created_at', 'updated_at'])->map(fn ($v) => $v ?? '')->all(),
            ['edit_version' => $version, 'highlights' => $highlights],
            $changes,
        );
    }

    public function test_project_second_save_is_refused_including_highlight_changes(): void
    {
        $id = DB::table('nakheel_projects')->orderBy('id')->value('id');
        $highlightId = DB::table('nakheel_project_highlights')->where('nakheel_project_id', $id)->orderBy('id')->value('id');
        $originalName = DB::table('nakheel_projects')->where('id', $id)->value('name');

        $this->actingAs($this->adminA());
        $versionA = $this->editVersion("/admin/projects/{$id}/edit");
        $this->actingAs($this->adminB());
        $versionB = $this->editVersion("/admin/projects/{$id}/edit");

        // B only renames a highlight.
        $this->put("/admin/projects/{$id}", $this->projectForm($id, $versionB, [], [$highlightId => 'B Highlight']))
            ->assertSessionHasNoErrors();

        // A, from the older form, renames the project — refused.
        $this->actingAs($this->adminA())
            ->put("/admin/projects/{$id}", $this->projectForm($id, $versionA, ['name' => 'A Project Name']))
            ->assertRedirect("/admin/projects/{$id}/edit")
            ->assertSessionHasErrors('edit_version');

        $this->assertSame($originalName, DB::table('nakheel_projects')->where('id', $id)->value('name'));
        $this->assertSame('B Highlight', DB::table('nakheel_project_highlights')->where('id', $highlightId)->value('label'));
    }

    public function test_a_single_admin_project_edit_still_saves_normally(): void
    {
        $id = DB::table('nakheel_projects')->orderBy('id')->value('id');
        $this->actingAs($this->adminA());
        $version = $this->editVersion("/admin/projects/{$id}/edit");

        $this->put("/admin/projects/{$id}", $this->projectForm($id, $version, ['name' => 'Renamed Project']))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/projects');

        $this->assertSame('Renamed Project', DB::table('nakheel_projects')->where('id', $id)->value('name'));
    }

    public function test_saving_a_project_that_another_admin_deleted_fails_cleanly(): void
    {
        $id = DB::table('nakheel_projects')->orderBy('id')->value('id');
        $this->actingAs($this->adminA());
        $version = $this->editVersion("/admin/projects/{$id}/edit");
        $form = $this->projectForm($id, $version);

        $this->actingAs($this->adminB())->delete("/admin/projects/{$id}");

        $this->actingAs($this->adminA())
            ->put("/admin/projects/{$id}", $form)
            ->assertRedirect('/admin/projects')
            ->assertSessionHas('error', 'This project was deleted by another admin, so your changes could not be saved.');
        $this->assertDatabaseMissing('nakheel_projects', ['id' => $id]);
    }
}
