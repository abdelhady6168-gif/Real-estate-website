<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NakheelPropertyService;
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

/**
 * Handles signing in and out.
 *
 * This controller is PUBLIC: it overrides the base controller's `auth`
 * middleware so that guests can actually reach the login page. Every other
 * controller in the portal stays protected.
 */
class AuthController extends Controller
{
    public static function middleware(): array
    {
        return []; // login + logout must be reachable without being logged in
    }

    /** Show the login form (or bounce to the properties page if already signed in). */
    public function showLogin(NakheelPropertyService $properties)
    {
        if (Auth::check()) {
            return redirect()->route('properties.index');
        }

        return view('auth.login', [
            'featuredProperty' => $properties->featuredForLogin(),
        ]);
    }

    /** Validate the credentials and start a session. */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->isDisabled()) {
                Auth::logout();

                return back()
                    ->withErrors(['email' => 'This account has been disabled.'])
                    ->onlyInput('email');
            }

            // Prevent session fixation by rotating the session id on login.
            $request->session()->regenerate();

            return redirect()->intended(route('properties.index'));
        }

        return back()
            ->withErrors(['email' => 'Those credentials do not match our records.'])
            ->onlyInput('email');
    }

    /** Show the registration form (or bounce to the properties page if already in). */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('properties.index');
        }

        return view('auth.register');
    }

    /** Create a new account and sign the user in. */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => PasswordPolicy::signupRules(),
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed automatically by the model's cast
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('properties.index');
    }

    /** Log the user out and invalidate the session. */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Show the "forgot password" email-entry form. */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link — but only to non-admin accounts. Admin
     * and Sub Admin passwords are managed by the Main Admin from the admin
     * panel (see AdminUserController::resetPassword()), never through this
     * public, unauthenticated flow.
     *
     * Always shows the same generic confirmation regardless of whether the
     * email exists, is unverified, or belongs to an admin account — this
     * is the standard pattern for a reset-link form, so probing it can't
     * reveal which emails are registered (or which ones are admins).
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->is_admin) {
            Password::sendResetLink($request->only('email'));
        }

        return back()->with('status', __("If an account exists for that email, we've sent a password reset link."));
    }

    /** Show the "set a new password" form for a given reset token/email pair. */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Complete the reset. The is_admin re-check inside the callback is
     * defense in depth, not the primary guard — sendResetLink() above
     * already refuses to ever issue a token to an admin account, so this
     * branch should never actually trigger in normal operation. It's here
     * so an admin's password can never be changed through this endpoint
     * even if some other path ever produced a token for one.
     */
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => PasswordPolicy::rules(),
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            if ($user->is_admin) {
                return;
            }

            $user->update(['password' => $password]);
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __('Your password has been reset. You can now sign in.'));
        }

        return back()
            ->withErrors(['email' => __('This password reset link is invalid or has expired.')])
            ->onlyInput('email');
    }
}
