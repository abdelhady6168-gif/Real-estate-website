// Sold Properties Visibility toggle — see resources/views/nakheel/properties.blade.php.
// Animates instantly (the sliding thumb) on click, then persists the change
// via fetch; reverts the animation if the request fails. The properties grid
// itself is rendered server-side from this setting, so once the save
// succeeds the page reloads (after a short pause so the slide is actually
// seen) to bring the grid in line with the new value.

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function applyState(button, isOn) {
    const track = button.querySelector('[data-sold-toggle-track]');
    const thumb = button.querySelector('[data-sold-toggle-thumb]');
    const label = button.querySelector('[data-sold-toggle-label]');

    button.dataset.value = isOn ? '1' : '0';
    button.setAttribute('aria-pressed', isOn ? 'true' : 'false');
    button.className = isOn ? button.dataset.onButtonClass : button.dataset.offButtonClass;
    track.className = isOn ? button.dataset.onTrackClass : button.dataset.offTrackClass;
    thumb.className = isOn ? button.dataset.onThumbClass : button.dataset.offThumbClass;
    label.textContent = isOn ? button.dataset.onLabel : button.dataset.offLabel;
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-sold-toggle]');
    if (!button || button.disabled) return;

    const wasOn = button.dataset.value === '1';
    const next = !wasOn;

    applyState(button, next);
    button.disabled = true;

    fetch(button.dataset.endpoint, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({ show_sold_properties: next }),
    })
        .then((response) => {
            if (!response.ok) throw new Error('failed');
            // Stay disabled through the reload — nothing else to click here.
            setTimeout(() => window.location.reload(), 300);
        })
        .catch(() => {
            applyState(button, wasOn);
            button.disabled = false;
        });
});
