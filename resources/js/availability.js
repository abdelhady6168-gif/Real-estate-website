// Admin unavailable-period manager — see
// resources/views/components/nakheel/availability-manager.blade.php and
// AdminPropertyAvailabilityController. A property is available by default;
// every row here is a period it ISN'T. Same fetch/CSRF conventions as
// resources/js/images.js.

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// The dates are picked from <x-nakheel.date-picker-field> (see
// resources/js/date-picker.js), which always displays DD/MM/YYYY but keeps
// the real ISO value in a hidden input — this just reads that value back
// out, and formats it for display in the saved-ranges list below.
function pickerValue(wrapper) {
    return wrapper.querySelector('[data-date-picker-value]').value;
}

function isoToDisplay(iso) {
    const [y, m, d] = iso.split('-');
    return `${d}/${m}/${y}`;
}

function emptyRow(manager) {
    const p = document.createElement('p');
    p.dataset.availabilityEmpty = '';
    p.className = 'px-4 py-6 text-center text-sm text-ink/40';
    p.textContent = manager.dataset.emptyLabel;
    return p;
}

function renderRow(manager, range) {
    const row = document.createElement('div');
    row.dataset.availabilityRow = '';
    row.dataset.id = range.id;
    row.dataset.start = range.start_date;
    row.dataset.end = range.end_date;
    row.className = 'grid grid-cols-[1fr_1fr_auto] gap-3 items-center px-4 py-2.5 text-sm';

    // Mirrors the @if ($canEdit) / @if ($canDelete) guards in
    // availability-manager.blade.php's initial server-rendered rows — this
    // re-render (after an add/edit/delete) must honor the same "Manage
    // Property Availability" permission, or a Sub Admin granted only
    // "create" would see Edit/Delete buttons reappear here even though the
    // server would reject clicking them.
    const buttons = [
        manager.dataset.canEdit === '1' ? `<button type="button" data-availability-edit class="text-gold hover:underline">${manager.dataset.labelEdit}</button>` : '',
        manager.dataset.canDelete === '1' ? `<button type="button" data-availability-delete class="text-red-600 hover:underline">${manager.dataset.labelDelete}</button>` : '',
    ].join('');

    row.innerHTML = `
        <span data-availability-start-cell class="text-ink/70">${isoToDisplay(range.start_date)}</span>
        <span data-availability-end-cell class="text-ink/70">${isoToDisplay(range.end_date)}</span>
        <span class="flex items-center gap-3 text-xs font-medium">${buttons}</span>
    `;

    return row;
}

function renderList(manager, ranges) {
    const list = manager.querySelector('[data-availability-list]');
    list.innerHTML = '';

    if (!ranges.length) {
        list.appendChild(emptyRow(manager));
        return;
    }

    ranges.forEach((range) => list.appendChild(renderRow(manager, range)));
}

function showError(manager, message) {
    const box = manager.querySelector('[data-availability-error]');
    if (!message) {
        box.classList.add('hidden');
        box.textContent = '';
        return;
    }
    box.textContent = message;
    box.classList.remove('hidden');
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function resetForm(manager) {
    const form = manager.querySelector('[data-availability-form]');
    form.reset();
    form.querySelector('[data-availability-editing-id]').value = '';
    window.setDatePickerValue(form.querySelector('[data-availability-start]'), null);
    window.setDatePickerValue(form.querySelector('[data-availability-end]'), null);
    manager.querySelector('[data-availability-submit]').textContent = form.dataset.addLabel;
    manager.querySelector('[data-availability-cancel-edit]').classList.add('hidden');
}

async function submitForm(manager, event) {
    event.preventDefault();
    const form = event.target;
    const editingId = form.querySelector('[data-availability-editing-id]').value;

    const startIso = pickerValue(form.querySelector('[data-availability-start]'));
    const endIso = pickerValue(form.querySelector('[data-availability-end]'));

    if (!startIso || !endIso) {
        showError(manager, manager.dataset.labelInvalidDate);
        return;
    }

    const payload = { start_date: startIso, end_date: endIso };

    const endpoint = editingId
        ? `${manager.dataset.baseEndpoint}/${editingId}`
        : manager.dataset.storeEndpoint;

    showError(manager, null);

    try {
        const response = await fetch(endpoint, {
            method: editingId ? 'PUT' : 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const body = await response.json().catch(() => ({}));
            const fieldError = body.errors ? Object.values(body.errors).flat()[0] : null;
            showError(manager, fieldError || body.message || manager.dataset.labelError);
            return;
        }

        const { ranges } = await response.json();
        renderList(manager, ranges);
        resetForm(manager);
    } catch (e) {
        showError(manager, manager.dataset.labelError);
    }
}

function beginEdit(manager, row) {
    const form = manager.querySelector('[data-availability-form]');
    form.querySelector('[data-availability-editing-id]').value = row.dataset.id;
    window.setDatePickerValue(form.querySelector('[data-availability-start]'), row.dataset.start);
    window.setDatePickerValue(form.querySelector('[data-availability-end]'), row.dataset.end);

    manager.querySelector('[data-availability-submit]').textContent = form.dataset.saveLabel;
    manager.querySelector('[data-availability-cancel-edit]').classList.remove('hidden');
    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

async function deleteRange(manager, row) {
    if (!confirm(manager.dataset.confirmDelete)) return;

    const id = row.dataset.id;
    showError(manager, null);

    try {
        const response = await fetch(`${manager.dataset.baseEndpoint}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
        });

        if (!response.ok) {
            showError(manager, manager.dataset.labelError);
            return;
        }

        const list = manager.querySelector('[data-availability-list]');
        row.remove();
        if (!list.querySelector('[data-availability-row]')) {
            list.appendChild(emptyRow(manager));
        }
    } catch (e) {
        showError(manager, manager.dataset.labelError);
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-availability-form]');
    if (!form) return;

    const manager = form.closest('[data-availability-manager]');
    if (manager) submitForm(manager, event);
});

document.addEventListener('click', (event) => {
    const manager = event.target.closest('[data-availability-manager]');
    if (!manager) return;

    const editBtn = event.target.closest('[data-availability-edit]');
    if (editBtn) beginEdit(manager, editBtn.closest('[data-availability-row]'));

    const deleteBtn = event.target.closest('[data-availability-delete]');
    if (deleteBtn) deleteRange(manager, deleteBtn.closest('[data-availability-row]'));

    const cancelBtn = event.target.closest('[data-availability-cancel-edit]');
    if (cancelBtn) resetForm(manager);
});
