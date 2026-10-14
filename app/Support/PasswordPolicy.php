<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

/**
 * Single source of truth for every password strength policy in the app.
 * The live checklist in resources/js/password-strength.js mirrors these
 * exact rules for instant feedback; this is what's actually enforced
 * server-side, so keep both in sync if either policy changes.
 *
 * Two distinct policies, deliberately: admin accounts (Main Admin / Sub
 * Admin — see AdminUserController, AuthController::resetPassword()) hold
 * elevated privileges and get the stricter 12-character bar; public site
 * accounts created via the "Sign Up" form (AuthController::register())
 * get the standard-but-solid 8-character bar. Same shape (length + mixed
 * case + number + symbol) either way.
 */
class PasswordPolicy
{
    public const ADMIN_MIN_LENGTH = 12;

    public const SIGNUP_MIN_LENGTH = 8;

    /** @return array<int, mixed> */
    public static function rules(): array
    {
        return self::rulesForMinLength(self::ADMIN_MIN_LENGTH);
    }

    /** @return array<int, mixed> */
    public static function signupRules(): array
    {
        return self::rulesForMinLength(self::SIGNUP_MIN_LENGTH);
    }

    /** @return array<int, mixed> */
    private static function rulesForMinLength(int $minLength): array
    {
        return [
            'required',
            'string',
            'confirmed',
            Password::min($minLength)->mixedCase()->numbers()->symbols(),
        ];
    }
}
