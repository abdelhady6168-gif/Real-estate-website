// Live password-strength checklist, strength bar, and show/hide toggle for
// every password field on the site (admin Sub Admin create/reset, the
// public forgot-password flow, and login/sign-up). Mirrors the relevant
// server-side policy (App\Support\PasswordPolicy) for instant feedback;
// the backend re-validates independently, so this is UX only, never the
// source of truth.
//
// Deliberately theme-agnostic: this only sets data-* state attributes
// (data-met, data-password-state); each partial's own Tailwind classes
// (group-data-[met=true]:..., data-[password-state=...]:...) decide what
// each state looks like, so the same script works on both the light admin
// panel and the dark public auth pages without hardcoding either palette.

const RULES = {
    length: (value, minLength) => value.length >= minLength,
    upper: (value) => /[A-Z]/.test(value),
    lower: (value) => /[a-z]/.test(value),
    number: (value) => /[0-9]/.test(value),
    symbol: (value) => /[^A-Za-z0-9]/.test(value),
};

// Four levels, scored by how many of the five rules above are satisfied —
// deliberately not tied to any one rule so it reflects overall strength.
const STRENGTH_LEVELS = [
    { maxScore: 1, key: 'very-weak', width: '25%', barClass: 'bg-red-500' },
    { maxScore: 3, key: 'weak', width: '50%', barClass: 'bg-amber-500' },
    { maxScore: 4, key: 'strong', width: '75%', barClass: 'bg-emerald-500' },
    { maxScore: 5, key: 'very-strong', width: '100%', barClass: 'bg-emerald-600' },
];
const ALL_BAR_CLASSES = STRENGTH_LEVELS.map((level) => level.barClass);

function evaluatePasswordForm(form) {
    const input = form?.querySelector('[data-password-input]');
    if (!input) return;

    const minLength = Number(input.dataset.minLength) || 12;
    const confirmInput = form.querySelector('[data-password-confirm-input]');
    const mismatch = form.querySelector('[data-password-mismatch]');
    const submit = form.querySelector('button[type="submit"]');
    const value = input.value;
    const ruleItems = form.querySelectorAll('[data-password-rule]');
    let score = 0;

    ruleItems.forEach((item) => {
        const check = RULES[item.dataset.passwordRule];
        const met = check ? check(value, minLength) : true;
        if (met) score += 1;

        item.dataset.met = met ? 'true' : 'false';
    });

    // A form with no checklist (e.g. the plain login field) has nothing to
    // gate on — only forms that render the requirements list should block
    // submission until every rule passes.
    const allMet = ruleItems.length === 0 || score === ruleItems.length;

    const strengthWrap = form.querySelector('[data-password-strength-wrap]');
    if (strengthWrap) {
        const bar = strengthWrap.querySelector('[data-password-strength-bar]');
        const label = strengthWrap.querySelector('[data-password-strength-label]');
        const level = value.length === 0 ? null : STRENGTH_LEVELS.find((l) => score <= l.maxScore);

        if (bar) {
            bar.style.width = level ? level.width : '0%';
            bar.classList.remove(...ALL_BAR_CLASSES);
            if (level) bar.classList.add(level.barClass);
        }
        if (label) {
            label.textContent = level ? strengthWrap.dataset[toCamelCase(`label-${level.key}`)] ?? '' : '';
        }
    }

    let matches = true;
    if (confirmInput) {
        matches = confirmInput.value.length > 0 && confirmInput.value === value;
        mismatch?.classList.toggle('hidden', matches || confirmInput.value.length === 0);
    }

    const valid = allMet && matches;

    if (submit) {
        submit.disabled = !valid;
        submit.classList.toggle('opacity-40', !valid);
        submit.classList.toggle('cursor-not-allowed', !valid);
    }
}

function toCamelCase(value) {
    return value.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
}

document.addEventListener('input', (event) => {
    if (!event.target.matches('[data-password-input], [data-password-confirm-input]')) return;
    evaluatePasswordForm(event.target.closest('form'));
});

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-password-toggle]');
    if (!toggle) return;
    const target = document.getElementById(toggle.dataset.passwordToggle);
    if (target) target.type = target.type === 'password' ? 'text' : 'password';
});

// Reveals the requirements checklist (and strength bar, if shown) the
// first time the password field is genuinely focused, on forms built with
// revealOnFocus — see auth/_password-fields.blade.php.
document.addEventListener('focusin', (event) => {
    if (!event.target.matches('[data-password-input], [data-password-confirm-input]')) return;

    const extras = event.target.closest('form')?.querySelector('[data-password-extras]');
    extras?.classList.remove('hidden');
});

document.querySelectorAll('[data-password-input]').forEach((input) => {
    evaluatePasswordForm(input.closest('form'));
});
