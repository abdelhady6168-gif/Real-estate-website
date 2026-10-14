<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Covers the security requirements added on top of the Main Admin / Sub
 * Admin system: permission-gated route/API access with a proper 403 (not
 * just hidden nav), the stronger Sub Admin password policy, and the safe
 * Why-Invest icon library (trusted catalog only, no arbitrary markup,
 * removal can't orphan an icon still in use).
 */
class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function mainAdmin(): User
    {
        return User::where('email', 'admin@example.com')->firstOrFail();
    }

    private function subAdmin(array $permissions = []): User
    {
        return User::create([
            'name' => 'sub@example.com',
            'email' => 'sub@example.com',
            'password' => 'Sub-Admin-Strong-1!',
            'is_admin' => true,
            'role' => 'sub',
            'permissions' => $permissions,
        ]);
    }

    // --- Section access: 403 page, not raw exposure -----------------------

    public function test_seeded_main_admin_actually_has_main_admin_role(): void
    {
        // Regression guard for the UserSeeder bug: without an explicit
        // role on the seeded row, admin@example.com ends up with role=null
        // and therefore zero permissions on a fresh install.
        $this->assertTrue($this->mainAdmin()->isMainAdmin());
    }

    public function test_sub_admin_without_permission_gets_403_page_not_the_page_content(): void
    {
        $sub = $this->subAdmin([]);

        $response = $this->actingAs($sub)->get('/admin/projects');

        $response->assertStatus(403);
        $response->assertSee('Access Denied');
        $response->assertDontSee('ADD PROJECT');
    }

    public function test_direct_url_access_is_blocked_even_without_nav_link(): void
    {
        $sub = $this->subAdmin(['properties' => ['create']]); // no 'projects' grant at all

        $this->actingAs($sub)->get('/admin/projects')->assertStatus(403);
        $this->actingAs($sub)->get('/admin/projects/create')->assertStatus(403);
    }

    public function test_view_only_sub_admin_can_view_but_not_edit_or_delete(): void
    {
        $projectId = DB::table('nakheel_projects')->value('id');
        $sub = $this->subAdmin(['projects' => ['view']]);

        $this->actingAs($sub)->get('/admin/projects')->assertOk();
        $this->actingAs($sub)->get("/admin/projects/{$projectId}/edit")->assertStatus(403);
        $this->actingAs($sub)->put("/admin/projects/{$projectId}", ['name' => 'Hacked'])->assertStatus(403);
        $this->actingAs($sub)->delete("/admin/projects/{$projectId}")->assertStatus(403);

        // The unauthorized write must never have applied.
        $this->assertDatabaseMissing('nakheel_projects', ['id' => $projectId, 'name' => 'Hacked']);
    }

    public function test_main_admin_always_has_full_access(): void
    {
        $this->actingAs($this->mainAdmin())->get('/admin/projects')->assertOk();
        $this->actingAs($this->mainAdmin())->get('/admin/why-invest-icons')->assertOk();
        $this->actingAs($this->mainAdmin())->get('/admin/users')->assertOk();
    }

    public function test_main_admin_account_can_never_be_deleted_through_sub_admin_management(): void
    {
        $main = $this->mainAdmin();

        $this->actingAs($main)->delete("/admin/users/{$main->id}")->assertStatus(404);
        $this->assertDatabaseHas('users', ['id' => $main->id]);
    }

    // --- Password policy ----------------------------------------------------

    public function test_weak_password_is_rejected_when_creating_a_sub_admin(): void
    {
        $response = $this->actingAs($this->mainAdmin())->post('/admin/users', [
            'email' => 'new-sub@example.com',
            'password' => 'short1A!',
            'password_confirmation' => 'short1A!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'new-sub@example.com']);
    }

    public function test_password_missing_complexity_is_rejected(): void
    {
        // 12+ chars but no uppercase/symbol — must still fail.
        $response = $this->actingAs($this->mainAdmin())->post('/admin/users', [
            'email' => 'new-sub2@example.com',
            'password' => 'lowercaseonly123',
            'password_confirmation' => 'lowercaseonly123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'new-sub2@example.com']);
    }

    public function test_strong_password_is_accepted_and_stored_hashed(): void
    {
        $response = $this->actingAs($this->mainAdmin())->post('/admin/users', [
            'email' => 'new-sub3@example.com',
            'password' => 'Correct-Horse-9!',
            'password_confirmation' => 'Correct-Horse-9!',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $user = User::where('email', 'new-sub3@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('Correct-Horse-9!', $user->getRawOriginal('password'));
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Correct-Horse-9!', $user->password));
    }

    public function test_password_reset_enforces_the_same_policy(): void
    {
        $sub = $this->subAdmin(['projects' => ['view']]);

        $this->actingAs($this->mainAdmin())
            ->put("/admin/users/{$sub->id}/password", [
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ])
            ->assertSessionHasErrors('password');

        $this->actingAs($this->mainAdmin())
            ->put("/admin/users/{$sub->id}/password", [
                'password' => 'Brand-New-Pass9!',
                'password_confirmation' => 'Brand-New-Pass9!',
            ])
            ->assertSessionDoesntHaveErrors();

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Brand-New-Pass9!', $sub->fresh()->password));
    }

    public function test_sub_admin_cannot_reach_admin_management_regardless_of_permissions(): void
    {
        $sub = $this->subAdmin(['properties' => ['create', 'edit', 'delete'], 'projects' => ['view', 'create', 'edit', 'delete']]);

        $this->actingAs($sub)->get('/admin/users')->assertStatus(403);
    }

    // --- Why Invest icon library ---------------------------------------------

    public function test_why_invest_reason_rejects_an_icon_key_not_in_the_catalog(): void
    {
        $response = $this->actingAs($this->mainAdmin())->post('/admin/why-invest', [
            'title' => 'Malicious',
            'icon' => '<script>alert(1)</script>',
            'sort_order' => 0,
        ]);

        $response->assertSessionHasErrors('icon');
        $this->assertDatabaseMissing('nakheel_why_invest_items', ['title' => 'Malicious']);
    }

    public function test_why_invest_reason_rejects_a_catalog_icon_that_is_not_yet_available(): void
    {
        // 'star' is in the trusted catalog but not seeded as available.
        $response = $this->actingAs($this->mainAdmin())->post('/admin/why-invest', [
            'title' => 'Uses unavailable icon',
            'icon' => 'star',
            'sort_order' => 0,
        ]);

        $response->assertSessionHasErrors('icon');
    }

    public function test_main_admin_can_add_an_icon_then_use_it(): void
    {
        $this->actingAs($this->mainAdmin())
            ->post('/admin/why-invest-icons', ['key' => 'star'])
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('nakheel_why_invest_icons', ['key' => 'star']);

        $this->actingAs($this->mainAdmin())
            ->post('/admin/why-invest', ['title' => 'Now allowed', 'icon' => 'star', 'sort_order' => 0])
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('nakheel_why_invest_items', ['title' => 'Now allowed', 'icon' => 'star']);
    }

    public function test_adding_an_icon_rejects_a_key_outside_the_trusted_catalog(): void
    {
        $response = $this->actingAs($this->mainAdmin())->post('/admin/why-invest-icons', [
            'key' => 'javascript:alert(1)',
        ]);

        $response->assertSessionHasErrors('key');
        $this->assertDatabaseMissing('nakheel_why_invest_icons', ['key' => 'javascript:alert(1)']);
    }

    public function test_icon_removal_is_blocked_while_it_is_in_use(): void
    {
        DB::table('nakheel_why_invest_items')->insert([
            'title' => 'Uses location icon',
            'icon' => 'location',
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->mainAdmin())
            ->delete('/admin/why-invest-icons/location')
            ->assertSessionHas('error');

        // Still available — existing reason must keep rendering its icon.
        $this->assertDatabaseHas('nakheel_why_invest_icons', ['key' => 'location']);
    }

    public function test_icon_removal_succeeds_once_nothing_uses_it(): void
    {
        $this->actingAs($this->mainAdmin())
            ->delete('/admin/why-invest-icons/growth')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('nakheel_why_invest_icons', ['key' => 'growth']);
    }

    public function test_sub_admin_cannot_manage_icons_without_the_dedicated_permission(): void
    {
        // Granted why_invest itself, but NOT why_invest_icons.
        $sub = $this->subAdmin(['why_invest' => ['create', 'edit', 'delete']]);

        $this->actingAs($sub)->get('/admin/why-invest-icons')->assertStatus(403);
        $this->actingAs($sub)->post('/admin/why-invest-icons', ['key' => 'star'])->assertStatus(403);
    }

    public function test_sub_admin_with_icon_permission_can_manage_the_library(): void
    {
        $sub = $this->subAdmin(['why_invest_icons' => ['view', 'create', 'delete']]);

        $this->actingAs($sub)->get('/admin/why-invest-icons')->assertOk();
        $this->actingAs($sub)
            ->post('/admin/why-invest-icons', ['key' => 'star'])
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('nakheel_why_invest_icons', ['key' => 'star']);
    }

    // --- View rendering sanity (catches Blade syntax errors in edited views) --

    public function test_edited_admin_views_render_without_errors(): void
    {
        $main = $this->mainAdmin();
        $sub = User::where('email', 'sub@example.com')->first() ?? $this->subAdmin(['projects' => ['view']]);

        $this->actingAs($main)->get('/admin/users/create')->assertOk()->assertSee('Password requirements');
        $this->actingAs($main)->get("/admin/users/{$sub->id}/password")->assertOk()->assertSee('Password requirements');
        $this->actingAs($main)->get('/admin/why-invest/create')->assertOk()->assertSee('Icon');
        $this->actingAs($main)->get('/admin/why-invest-icons')->assertOk()->assertSee('Why Invest Icons');
    }

    public function test_forbidden_response_never_leaks_protected_data(): void
    {
        $sub = $this->subAdmin([]);

        $response = $this->actingAs($sub)->get('/admin/users');

        $response->assertStatus(403);
        $response->assertDontSee('sub@example.com');
        $response->assertDontSee('CREATE SUB ADMIN');
    }
}
