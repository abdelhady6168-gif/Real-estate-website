<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * The public "forgot password" self-service flow — deliberately restricted
 * to non-admin accounts. Admin/Sub Admin passwords are only ever reset by
 * the Main Admin through the admin panel (see AdminSecurityTest).
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // --- Who gets a reset link -------------------------------------------

    public function test_regular_user_requesting_a_reset_link_actually_receives_one(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_admin' => false]);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_admin_account_never_receives_a_reset_link(): void
    {
        Notification::fake();
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->post('/forgot-password', ['email' => $admin->email])
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_sub_admin_account_never_receives_a_reset_link(): void
    {
        Notification::fake();
        $sub = User::create([
            'name' => 'sub@example.com',
            'email' => 'sub@example.com',
            'password' => 'Sub-Admin-Strong-1!',
            'is_admin' => true,
            'role' => 'sub',
        ]);

        $this->post('/forgot-password', ['email' => $sub->email])
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_response_is_identical_whether_or_not_the_email_exists(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_admin' => false]);

        $realEmailResponse = $this->post('/forgot-password', ['email' => $user->email]);
        $realStatus = session('status');

        $fakeEmailResponse = $this->post('/forgot-password', ['email' => 'nobody-at-all@example.com']);
        $fakeStatus = session('status');

        $this->assertSame($realEmailResponse->status(), $fakeEmailResponse->status());
        $this->assertSame($realStatus, $fakeStatus);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    // --- Completing the reset ---------------------------------------------

    public function test_weak_password_is_rejected_on_the_public_reset_form(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ])->assertSessionHasErrors('password');

        // Unchanged from the factory default ('password') — the weak value was never applied.
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_strong_password_completes_the_reset_and_consumes_the_token(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Brand-New-Pass9!',
            'password_confirmation' => 'Brand-New-Pass9!',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('Brand-New-Pass9!', $user->fresh()->password));

        // The token was single-use — trying again must fail even with the same token.
        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Another-Strong-9!',
            'password_confirmation' => 'Another-Strong-9!',
        ])->assertSessionHasErrors('email');
    }

    public function test_an_expired_or_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->post('/reset-password', [
            'token' => 'not-a-real-token',
            'email' => $user->email,
            'password' => 'Brand-New-Pass9!',
            'password_confirmation' => 'Brand-New-Pass9!',
        ])->assertSessionHasErrors('email');
    }

    /**
     * The exact "use a URL to gain access to another user's account"
     * scenario: take a real, validly-issued reset link/token and try to
     * apply it to a DIFFERENT account by swapping the email. Tokens are
     * looked up per-email and hashed at rest (see
     * Illuminate\Auth\Passwords\DatabaseTokenRepository::exists()), so this
     * must fail — proving it here rather than just trusting the framework.
     */
    public function test_a_valid_token_cannot_be_reused_against_a_different_users_email(): void
    {
        $victim = User::factory()->create(['is_admin' => false, 'email' => 'victim@example.com']);
        $victimOriginalHash = $victim->password;
        $attackerOwnToken = Password::createToken(
            User::factory()->create(['is_admin' => false, 'email' => 'attacker@example.com'])
        );

        $this->post('/reset-password', [
            'token' => $attackerOwnToken,
            'email' => $victim->email, // swapped to the victim's email
            'password' => 'Attacker-Chosen-9!',
            'password_confirmation' => 'Attacker-Chosen-9!',
        ])->assertSessionHasErrors('email');

        $this->assertSame($victimOriginalHash, $victim->fresh()->password);
        $this->assertFalse(Hash::check('Attacker-Chosen-9!', $victim->fresh()->password));
    }

    public function test_admin_password_cannot_be_changed_through_the_public_reset_endpoint_even_with_a_valid_token(): void
    {
        // Defense in depth: even if a token somehow existed for an admin
        // account (sendResetLink() should make this impossible in normal
        // operation), the reset endpoint must never apply it.
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $originalHash = $admin->password;
        $token = Password::createToken($admin);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $admin->email,
            'password' => 'Attacker-Chosen-9!',
            'password_confirmation' => 'Attacker-Chosen-9!',
        ]);

        $this->assertSame($originalHash, $admin->fresh()->password);
        $this->assertFalse(Hash::check('Attacker-Chosen-9!', $admin->fresh()->password));
    }

    // --- Abuse prevention ---------------------------------------------------

    public function test_reset_link_requests_are_rate_limited(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/forgot-password', ['email' => $user->email]);
        }

        $this->post('/forgot-password', ['email' => $user->email])->assertStatus(429);
    }
}
