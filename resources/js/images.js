// Powers <x-nakheel.image-field> and <x-nakheel.image-list-field> in "AJAX mode"
// (an existing record — resource + id are known). New images are uploaded and
// previewed instantly; existing images are removed instantly; every other
// image on the record is left untouched. See app/Http/Controllers/AdminImageController.php.

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function setStatus(field, text, isError = false) {
    const status = field.querySelector('[data-image-status]');
    if (!status) return;
    status.textContent = text;
    status.classList.toggle('text-red-600', isError);
    status.classList.toggle('text-ink/40', !isError);
}

async function uploadTo(endpoint, fieldName, file) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('field', fieldName);

    const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
        body: formData,
    });

    if (!response.ok) {
        throw new Error('upload failed');
    }

    return response.json();
}

async function deleteFrom(endpoint, fieldName, path) {
    const response = await fetch(endpoint, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({ field: fieldName, path }),
    });

    if (!response.ok) {
        throw new Error('delete failed');
    }
}

// ---- single-image field ----

function handleSingleFieldChange(input) {
    const field = input.closest('[data-image-field]');
    if (!field) return;

    const file = input.files?.[0];
    if (!file) return;

    const preview = field.querySelector('[data-image-preview]');
    const removeBtn = field.querySelector('[data-image-remove]');
    const objectUrl = URL.createObjectURL(file);
    preview.innerHTML = `<img src="${objectUrl}" class="h-full w-full object-cover">`;
    setStatus(field, 'Uploading…');

    uploadTo(field.dataset.endpoint, field.dataset.field, file)
        .then(({ path, url }) => {
            preview.innerHTML = `<img src="${url}" class="h-full w-full object-cover">`;
            removeBtn.dataset.path = path;
            removeBtn.classList.remove('hidden');
            setStatus(field, '');
        })
        .catch(() => setStatus(field, 'Upload failed — try again.', true));
}

function handleSingleFieldRemove(button) {
    const field = button.closest('[data-image-field]');
    if (!field || !button.dataset.path) return;
    if (!confirm('Remove this image?')) return;

    setStatus(field, 'Removing…');

    deleteFrom(field.dataset.deleteEndpoint, field.dataset.field, button.dataset.path)
        .then(() => {
            const preview = field.querySelector('[data-image-preview]');
            const placeholder = field.querySelector('template[data-image-placeholder]');
            preview.innerHTML = placeholder ? placeholder.innerHTML : '';
            button.dataset.path = '';
            button.classList.add('hidden');
            setStatus(field, '');
        })
        .catch(() => setStatus(field, 'Remove failed — try again.', true));
}

// ---- multi-image list field ----

function handleListFieldChange(input) {
    const field = input.closest('[data-image-list-field]');
    if (!field) return;

    const files = [...(input.files ?? [])];
    if (!files.length) return;

    const list = field.querySelector('[data-image-list-items]');

    files.forEach((file) => {
        const objectUrl = URL.createObjectURL(file);
        const tile = document.createElement('div');
        tile.className = 'relative opacity-60';
        tile.dataset.imageListItem = '';
        tile.innerHTML = `
            <img src="${objectUrl}" class="aspect-square rounded-md w-full object-cover">
            <button type="button" data-image-list-remove class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs shadow" aria-label="Remove">×</button>
        `;
        list.appendChild(tile);

        uploadTo(field.dataset.endpoint, field.dataset.field, file)
            .then(({ path, url }) => {
                tile.classList.remove('opacity-60');
                tile.dataset.path = path;
                tile.querySelector('img').src = url;
            })
            .catch(() => {
                tile.querySelector('img').classList.add('ring-2', 'ring-red-500');
                tile.classList.remove('opacity-60');
            });
    });

    input.value = '';
}

function handleListFieldRemove(button) {
    const field = button.closest('[data-image-list-field]');
    const tile = button.closest('[data-image-list-item]');
    if (!field || !tile || !tile.dataset.path) return;
    if (!confirm('Remove this image?')) return;

    tile.classList.add('opacity-40');

    deleteFrom(field.dataset.deleteEndpoint, field.dataset.field, tile.dataset.path)
        .then(() => tile.remove())
        .catch(() => tile.classList.remove('opacity-40'));
}

// ---- legacy fields (create forms — no record yet, nothing to upload to
// yet, so these only render a local preview; the real files go up with the
// full form submit and are resolved server-side by HandlesImageUploads) ----

function handleLegacySingleFieldChange(input) {
    const preview = input.closest('.flex')?.querySelector('[data-legacy-image-preview]');
    const file = input.files?.[0];
    if (!preview || !file) return;

    const objectUrl = URL.createObjectURL(file);
    preview.innerHTML = `<img src="${objectUrl}" class="h-full w-full object-cover">`;
}

// Each "Browse" click on a native file input replaces its FileList outright —
// picking a second image would otherwise discard the first. So the running
// selection is kept here (per input) and re-synced onto input.files via
// DataTransfer before every submit, letting picks accumulate like the AJAX
// list field does.
const legacyListFiles = new WeakMap();

function syncLegacyListInput(input) {
    const dt = new DataTransfer();
    legacyListFiles.get(input).forEach((file) => dt.items.add(file));
    input.files = dt.files;
}

function renderLegacyListPreviews(input) {
    const container = input.closest('div')?.querySelector('[data-legacy-new-previews]');
    if (!container) return;

    container.innerHTML = '';
    legacyListFiles.get(input).forEach((file, index) => {
        const objectUrl = URL.createObjectURL(file);
        const tile = document.createElement('div');
        tile.className = 'relative';
        tile.innerHTML = `
            <img src="${objectUrl}" class="aspect-square rounded-md w-full object-cover">
            <button type="button" data-legacy-list-remove data-index="${index}" aria-label="Remove"
                    class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs shadow hover:bg-red-600">&times;</button>
        `;
        container.appendChild(tile);
    });
}

function handleLegacyListFieldChange(input) {
    if (!legacyListFiles.has(input)) legacyListFiles.set(input, []);
    legacyListFiles.get(input).push(...(input.files ?? []));

    syncLegacyListInput(input);
    renderLegacyListPreviews(input);
}

function handleLegacyListFieldRemove(button) {
    const container = button.closest('[data-legacy-new-previews]');
    const input = container?.parentElement?.querySelector('[data-legacy-image-list-pick]');
    if (!input || !legacyListFiles.has(input)) return;

    legacyListFiles.get(input).splice(Number(button.dataset.index), 1);
    syncLegacyListInput(input);
    renderLegacyListPreviews(input);
}

document.addEventListener('change', (event) => {
    if (event.target.matches('[data-image-pick]')) handleSingleFieldChange(event.target);
    if (event.target.matches('[data-image-list-pick]')) handleListFieldChange(event.target);
    if (event.target.matches('[data-legacy-image-pick]')) handleLegacySingleFieldChange(event.target);
    if (event.target.matches('[data-legacy-image-list-pick]')) handleLegacyListFieldChange(event.target);
});

document.addEventListener('click', (event) => {
    const removeBtn = event.target.closest('[data-image-remove]');
    if (removeBtn) handleSingleFieldRemove(removeBtn);

    const listRemoveBtn = event.target.closest('[data-image-list-remove]');
    if (listRemoveBtn) handleListFieldRemove(listRemoveBtn);

    const legacyListRemoveBtn = event.target.closest('[data-legacy-list-remove]');
    if (legacyListRemoveBtn) handleLegacyListFieldRemove(legacyListRemoveBtn);
});
