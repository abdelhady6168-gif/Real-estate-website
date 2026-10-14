{{--
    Shared password + confirm-password fields with a live strength checklist
    and show/hide toggles, styled for the dark public auth pages (login,
    register, reset-password). Same data-* hooks and resources/js/
    password-strength.js behavior as the light-themed twin used by the
    admin panel — admin/users/_password-fields.blade.php — just a
    different palette.

    The checklist mirrors whatever password policy the calling page
    enforces server-side — pass $minLength to match it. This UI only
    prevents accidental weak submissions; backend validation is
    authoritative.

    Expects (optional):
      $passwordLabel, $confirmLabel — field labels.
      $minLength — minimum length checked client-side (default 12).
      $showStrengthBar — render the strength meter (default false).
      $revealOnFocus — hide requirements/strength until the password field
        is first focused, instead of showing them immediately (default false).

    `autocomplete="off"` on both fields is a (best-effort — browsers vary
    in how much they honor it) anti-autofill hint: without it, some
    browsers silently populate a saved credential into the field before
    the user interacts with it at all, and the eye-icon toggle would then
    "reveal" a password the user never typed. An earlier version of this
    additionally marked both inputs `readonly` and removed it on first
    focus for a stronger version of the same effect, but that broke the
    mobile on-screen keyboard: readonly at the moment focus lands is what
    mobile browsers use to decide whether to show a keyboard at all, and
    removing the attribute a moment later (inside the same focus event)
    doesn't change that decision retroactively.
--}}
@php
    $passwordLabel ??= 'PASSWORD';
    $confirmLabel ??= 'CONFIRM PASSWORD';
    $minLength ??= 12;
    $showStrengthBar ??= false;
    $revealOnFocus ??= false;
    $passwordRules = [
        'length' => __('At least :count characters', ['count' => $minLength]),
        'upper' => __('Uppercase letter'),
        'lower' => __('Lowercase letter'),
        'number' => __('Number'),
        'symbol' => __('Special character'),
    ];
@endphp

<div>
    <label for="password" class="block text-[10px] font-semibold tracking-[0.15em] text-white/40 mb-2">{{ __($passwordLabel) }}</label>
    <div class="relative">
        <input id="password" type="password" name="password" required minlength="{{ $minLength }}"
               autocomplete="off" data-min-length="{{ $minLength }}"
               data-password-input
               class="w-full bg-transparent border-b border-white/15 pb-2 pr-8 text-white placeholder-white/25 focus:border-gold focus:outline-none">
        <button type="button" data-password-toggle="password" tabindex="-1" aria-label="{{ __('Show password') }}"
                class="absolute right-0 bottom-2 text-white/40 hover:text-white/70">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
<div>
    <label for="password_confirmation" class="block text-[10px] font-semibold tracking-[0.15em] text-white/40 mb-2">{{ __($confirmLabel) }}</label>
    <div class="relative">
        <input id="password_confirmation" type="password" name="password_confirmation" required minlength="{{ $minLength }}"
               autocomplete="off"
               data-password-confirm-input
               class="w-full bg-transparent border-b border-white/15 pb-2 pr-8 text-white placeholder-white/25 focus:border-gold focus:outline-none">
        <button type="button" data-password-toggle="password_confirmation" tabindex="-1" aria-label="{{ __('Show password') }}"
                class="absolute right-0 bottom-2 text-white/40 hover:text-white/70">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    <p data-password-mismatch class="mt-1 hidden text-xs text-red-300">{{ __('Passwords do not match.') }}</p>
</div>
<div @if ($revealOnFocus) class="hidden" data-password-extras @endif>
    @if ($showStrengthBar)
        <div class="mb-3" data-password-strength-wrap
             data-label-very-weak="{{ __('Very Weak') }}"
             data-label-weak="{{ __('Weak') }}"
             data-label-strong="{{ __('Strong') }}"
             data-label-very-strong="{{ __('Very Strong') }}">
            <div class="flex items-center justify-between mb-1.5">
                <p class="text-[10px] font-semibold tracking-[0.15em] text-white/40">{{ __('PASSWORD STRENGTH') }}</p>
                <p data-password-strength-label class="text-[10px] font-semibold text-white/60"></p>
            </div>
            <div class="h-1 w-full rounded-full bg-white/10 overflow-hidden">
                <div data-password-strength-bar class="h-full w-0 rounded-full transition-all duration-200 ease-out"></div>
            </div>
        </div>
    @endif
    <p class="text-[10px] font-semibold tracking-[0.15em] text-white/40 mb-2">{{ __('Password must contain') }}</p>
    <ul class="grid grid-cols-2 gap-x-4 gap-y-1.5">
        @foreach ($passwordRules as $rule => $label)
            <li data-password-rule="{{ $rule }}" data-met="false" class="group flex items-center gap-1.5 text-xs text-white/40 transition-colors group-data-[met=true]:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5 shrink-0 text-white/25 group-data-[met=true]:hidden">
                    <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="hidden h-3.5 w-3.5 shrink-0 text-emerald-400 group-data-[met=true]:block">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                </svg>
                <span>{{ $label }}</span>
            </li>
        @endforeach
    </ul>
</div>
