// Client-side filter for the "Add More Icons" grid — see
// resources/views/admin/why-invest-icons/index.blade.php. The catalog is
// small enough (bundled server-side, not fetched) that filtering the
// already-rendered options in the browser is simpler and safer than a
// search endpoint.

function filterIcons(input) {
    const form = input.closest('[data-icon-search-form]');
    if (!form) return;

    const query = input.value.trim().toLowerCase();
    const options = [...form.querySelectorAll('[data-icon-option]')];
    let visibleCount = 0;

    options.forEach((option) => {
        const matches = option.dataset.iconName.includes(query);
        option.classList.toggle('hidden', !matches);
        if (matches) visibleCount += 1;
    });

    form.querySelector('[data-icon-no-results]')?.classList.toggle('hidden', visibleCount > 0);
}

document.addEventListener('input', (event) => {
    if (!event.target.matches('[data-icon-search]')) return;
    filterIcons(event.target);
});
