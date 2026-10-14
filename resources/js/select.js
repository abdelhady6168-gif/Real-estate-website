// Custom dropdown — see resources/views/components/nakheel/filter-select.blade.php.
// Renders its own option list in regular HTML instead of a native <select> popup,
// since native option-list styling/sizing can't be controlled reliably across
// browsers (this is what was clipping Arabic text in Firefox's native list).

function closeAllSelects(except = null) {
    document.querySelectorAll('[data-select-menu]').forEach((menu) => {
        if (menu !== except) menu.classList.add('hidden');
    });
}

// A native <select> normally has no auto-submit either, but a filter
// dropdown reads as broken if picking a value doesn't do anything until a
// separate button is clicked — so submit immediately, carrying the current
// sort along (sort is only present in the request when its own button is
// the clicked control, so it has to be re-added here explicitly).
function submitFilterForm(form) {
    if (!form) return;
    const params = new URLSearchParams(new FormData(form));
    if (form.dataset.currentSort) params.set('sort', form.dataset.currentSort);
    window.location.href = `${form.action}?${params.toString()}`;
}

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-select-trigger]');
    if (trigger) {
        const wrapper = trigger.closest('[data-select]');
        const menu = wrapper.querySelector('[data-select-menu]');
        const wasOpen = !menu.classList.contains('hidden');
        closeAllSelects();
        menu.classList.toggle('hidden', wasOpen);
        return;
    }

    const option = event.target.closest('[data-select-option]');
    if (option) {
        const wrapper = option.closest('[data-select]');
        const input = wrapper.querySelector('[data-select-value]');
        const label = wrapper.querySelector('[data-select-label]');

        input.value = option.dataset.value;
        label.textContent = option.textContent.trim();

        wrapper.querySelectorAll('[data-select-option]').forEach((o) => {
            o.classList.remove('font-semibold', 'text-ink', 'bg-cream/60');
        });
        option.classList.add('font-semibold', 'text-ink', 'bg-cream/60');

        closeAllSelects();
        submitFilterForm(input.closest('form'));
        return;
    }

    if (!event.target.closest('[data-select]')) {
        closeAllSelects();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAllSelects();
});
