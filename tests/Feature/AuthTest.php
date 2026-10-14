<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies the login system: the form loads, the dashboard is protected,
 * the seeded credentials work, bad credentials are rejected, and logout
 * ends the session.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // seeds the demo admin user (admin@example.com / password)
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
    }

    public function test_guest_cannot_reach_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_can_log_in_with_seeded_credentials(): void
    {
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->from('/login')->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'not-the-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_sees_the_dashboard(): void
    {
        $user = User::where('email', 'admin@example.com')->first();

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Portal Admin');
    }

    public function test_user_can_log_out(): void
    {
        $user = User::where('email', 'admin@example.com')->first();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertOk()->assertSee('Sign up');
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $this->post('/register', [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'password' => 'Secret-123!',
            'password_confirmation' => 'Secret-123!',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'new.user@example.com']);
    }

    public function test_register_requires_matching_password_confirmation(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Mismatch',
            'email' => 'mismatch@example.com',
            'password' => 'Secret-123!',
            'password_confirmation' => 'different',
        ])->assertRedirect('/register')->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_register_rejects_a_weak_password(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Weak',
            'email' => 'weak@example.com',
            'password' => 'secret123', // no uppercase, no special character
            'password_confirmation' => 'secret123',
        ])->assertRedirect('/register')->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }

    /**
     * User::$fillable includes is_admin/role/permissions (needed so the
     * Main Admin CAN set them via AdminUserController) — which would be a
     * mass-assignment privilege-escalation hole if the public register()
     * endpoint ever passed raw request input straight to User::create().
     * It doesn't (see AuthController::register()), but that's a one-line
     * discipline away from breaking, so it's worth a permanent test.
     */
    public function test_public_registration_cannot_self_grant_admin_privileges(): void
    {
        $this->post('/register', [
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'Attacker-Strong-9!',
            'password_confirmation' => 'Attacker-Strong-9!',
            'is_admin' => true,
            'role' => 'main',
            'permissions' => ['why_invest_icons' => ['view', 'create', 'delete']],
        ])->assertRedirect();

        $user = User::where('email', 'attacker@example.com')->firstOrFail();

        $this->assertFalse((bool) $user->is_admin);
        $this->assertNull($user->role);
        $this->assertFalse($user->isMainAdmin());
        $this->assertFalse($user->hasPermission('why_invest_icons', 'view'));
    }

    public function test_register_rejects_a_duplicate_email(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Dupe',
            'email' => 'admin@example.com', // already seeded
            'password' => 'Secret-123!',
            'password_confirmation' => 'Secret-123!',
        ])->assertRedirect('/register')->assertSessionHasErrors('email');
    }

    // --- Auth page UI: "Sign Up" wording + password field markup ----------

    public function test_login_page_offers_sign_up_not_request_access(): void
    {
        $response = $this->get('/login');

        $response->assertSee('Sign Up');
        $response->assertDontSee('Request access');
        $response->assertDontSee('Request Access');
    }

    public function test_login_password_field_is_a_plain_toggle_with_no_checklist(): void
    {
        $response = $this->get('/login');

        // Anti-autofill hooks present...
        $response->assertSee('data-password-input', false);
        $response->assertSee('readonly', false);
        // ...but no requirements/strength UI — login isn't account creation.
        $response->assertDontSee('data-password-rule', false);
        $response->assertDontSee('data-password-strength-wrap', false);
    }

    public function test_register_page_shows_password_requirements_and_strength_bar(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('data-password-input', false);
        $response->assertSee('data-password-strength-wrap', false);
        $response->assertSee('data-password-extras', false); // hidden until focus
        $response->assertSee('At least 8 characters');
        $response->assertSee('Uppercase letter');
        $response->assertSee('Special character');
    }
}
