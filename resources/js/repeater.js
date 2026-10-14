// Powers <x-nakheel.repeater> (Specifications "fields" mode, Features "tags"
// mode) — pure DOM manipulation, no framework, event-delegated so it also
// works for repeaters added dynamically later. Both modes are bilingual:
// "fields" rows carry English+Arabic label/value inputs (a generic
// <template> clone, doesn't care how many inputs are in a row); "tags"
// chips carry an English+Arabic pair, synced into a hidden JSON input.

function addFieldsRow(repeater) {
    const template = repeater.querySelector('template[data-repeater-template]');
    const rows = repeater.querySelector('[data-repeater-rows]');
    const key = `new_${Date.now()}_${Math.floor(Math.random() * 10000)}`;

    const fragment = template.content.cloneNode(true);
    fragment.querySelectorAll('[name]').forEach((el) => {
        el.name = el.name.replace('__KEY__', key);
    });
    rows.appendChild(fragment);
}

function removeFieldsRow(button) {
    button.closest('[data-repeater-row]')?.remove();
}

function syncTagsValue(repeater) {
    const values = [...repeater.querySelectorAll('[data-repeater-chip]')].map((chip) => ({
        value: chip.dataset.value,
        value_ar: chip.dataset.valueAr || null,
    }));
    repeater.querySelector('[data-repeater-value]').value = JSON.stringify(values);
}

function addTag(repeater) {
    const inputEn = repeater.querySelector('[data-repeater-chip-input-en]');
    const inputAr = repeater.querySelector('[data-repeater-chip-input-ar]');
    const valueEn = inputEn.value.trim();
    const valueAr = inputAr.value.trim();
    if (!valueEn) return;

    // Feature names are words — no digits (Western or Arabic-keyboard).
    // A convenience only: the server re-checks every feature on save
    // (AdminPropertyController::validateFeatures()).
    const message = repeater.dataset.noDigitsMessage || '';
    for (const input of [inputEn, inputAr]) {
        input.setCustomValidity(/\p{Nd}/u.test(input.value) ? message : '');
        if (!input.reportValidity()) return;
    }

    const chips = repeater.querySelector('[data-repeater-chips]');
    const chip = document.createElement('span');
    // Same classes as the server-rendered chips in repeater.blade.php —
    // bg-surface (not bg-white) so a newly added chip follows the active
    // light/dark theme like the existing ones.
    chip.className = 'inline-flex items-center gap-1.5 rounded-md border border-border bg-surface px-3 py-1.5 text-xs text-ink/80';
    chip.dataset.repeaterChip = '';
    chip.dataset.value = valueEn;
    chip.dataset.valueAr = valueAr;

    // Built via textContent, not innerHTML, so a typed value like
    // "<img src=x onerror=alert(1)>" renders as plain text instead of
    // executing — admin-typed text is untrusted input just like any other.
    const enSpan = document.createElement('span');
    enSpan.textContent = valueEn;
    chip.appendChild(enSpan);

    if (valueAr) {
        const arSpan = document.createElement('span');
        arSpan.className = 'text-ink/40';
        arSpan.dir = 'rtl';
        arSpan.textContent = `(${valueAr})`;
        chip.appendChild(document.createTextNode(' '));
        chip.appendChild(arSpan);
    }

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.dataset.repeaterChipRemove = '';
    removeBtn.setAttribute('aria-label', 'Remove');
    removeBtn.className = 'text-ink/40 hover:text-red-600';
    removeBtn.textContent = '×';
    chip.appendChild(document.createTextNode(' '));
    chip.appendChild(removeBtn);

    chips.appendChild(chip);

    inputEn.value = '';
    inputAr.value = '';
    syncTagsValue(repeater);
}

function removeTag(button) {
    const repeater = button.closest('[data-repeater="tags"]');
    button.closest('[data-repeater-chip]')?.remove();
    syncTagsValue(repeater);
}

document.addEventListener('click', (event) => {
    const addFieldsBtn = event.target.closest('[data-repeater-add]');
    if (addFieldsBtn) addFieldsRow(addFieldsBtn.closest('[data-repeater="fields"]'));

    const removeFieldsBtn = event.target.closest('[data-repeater-remove]');
    if (removeFieldsBtn) removeFieldsRow(removeFieldsBtn);

    const addTagBtn = event.target.closest('[data-repeater-chip-add]');
    if (addTagBtn) addTag(addTagBtn.closest('[data-repeater="tags"]'));

    const removeTagBtn = event.target.closest('[data-repeater-chip-remove]');
    if (removeTagBtn) removeTag(removeTagBtn);
});

// Clear a "no digits" message as soon as the admin edits the field again.
document.addEventListener('input', (event) => {
    if (event.target.matches('[data-repeater-chip-input-en], [data-repeater-chip-input-ar]')) {
        event.target.setCustomValidity('');
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && event.target.matches('[data-repeater-chip-input-en], [data-repeater-chip-input-ar]')) {
        event.preventDefault();
        addTag(event.target.closest('[data-repeater="tags"]'));
    }
});
