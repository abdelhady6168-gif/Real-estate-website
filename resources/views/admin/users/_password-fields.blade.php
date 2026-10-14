{{--
    Shared password + confirm-password fields with a live strength checklist
    and show/hide toggles, styled for the light admin panel. Used by
    admin/users/_form.blade.php (create) and admin/users/password.blade.php
    (reset). Included inside a parent `grid grid-cols-1 sm:grid-cols-2
    gap-5` container in both places.

    (The public forgot-password flow uses the dark-themed twin of this
    partial — auth/_password-fields.blade.php — same data-* hooks, same
    resources/js/password-strength.js behavior, different palette.)

    The checklist mirrors App\Support\PasswordPolicy — keep both in sync if
    the policy changes. This UI only prevents accidental weak submissions;
    the backend validation (App\Support\PasswordPolicy::rules()) is
    authoritative.

    Expects (optional): $passwordLabel, $confirmLabel.
--}}
@php
    $passwordLabel ??= 'Password';
    $confirmLabel ??= 'Confirm Password';
    $passwordRules = [
        'length' => __('At least 12 characters'),
        'upper' => __('Contains an uppercase letter'),
        'lower' => __('Contains a lowercase letter'),
        'number' => __('Contains a number'),
        'symbol' => __('Contains a special character'),
    ];
@endphp

<div>
    <label for="password" class="block text-xs font-medium text-ink/60 mb-1.5">{{ __($passwordLabel) }}</label>
    <div class="relative">
        <input type="password" name="password" id="password" required minlength="12" autocomplete="new-password"
               data-password-input
               class="w-full rounded-md border border-border px-3 py-2 pr-9 text-sm focus:border-gold focus:outline-none">
        <button type="button" data-password-toggle="password" tabindex="-1" aria-label="{{ __('Show password') }}"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-ink/30 hover:text-ink/60">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
<div>
    <label for="password_confirmation" class="block text-xs font-medium text-ink/60 mb-1.5">{{ __($confirmLabel) }}</label>
    <div class="relative">
        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="12" autocomplete="new-password"
               data-password-confirm-input
               class="w-full rounded-md border border-border px-3 py-2 pr-9 text-sm focus:border-gold focus:outline-none">
        <button type="button" data-password-toggle="password_confirmation" tabindex="-1" aria-label="{{ __('Show password') }}"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-ink/30 hover:text-ink/60">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    <p data-password-mismatch class="mt-1 hidden text-xs text-red-600">{{ __('Passwords do not match.') }}</p>
</div>
<div class="sm:col-span-2">
    <p class="text-xs font-medium text-ink/60 mb-2">{{ __('Password requirements') }}</p>
    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1.5">
        @foreach ($passwordRules as $rule => $label)
            <li data-password-rule="{{ $rule }}" data-met="false" class="group flex items-center gap-1.5 text-xs text-ink/40 transition-colors group-data-[met=true]:text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5 shrink-0 text-ink/25 group-data-[met=true]:hidden">
                    <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="hidden h-3.5 w-3.5 shrink-0 text-emerald-600 group-data-[met=true]:block">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                </svg>
                <span>{{ $label }}</span>
            </li>
        @endforeach
    </ul>
</div>
