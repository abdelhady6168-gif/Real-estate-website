// Delegated replacements for what used to be inline onclick/onsubmit
// handlers in the Blade views — inline handlers are blocked by the
// Content-Security-Policy (see App\Http\Middleware\AddSecurityHeaders),
// so the markup now only carries data-* attributes and this file supplies
// the behavior. Same event-delegation style as the rest of resources/js,
// so it also covers content swapped in later (e.g. project-nav.js).
//
//   data-dialog-open="<id>"        on a button: opens that <dialog> as a modal
//   data-dialog-close              on a button inside a <dialog>: closes it
//   data-dialog-backdrop-close     on a <dialog>: clicking its backdrop closes it
//   data-confirm="<message>"       on a <form>: asks before submitting
//
// data-confirm also replaces onsubmit="return confirm('{{ ... }}')", which
// was unsafe as well as CSP-blocked: Blade's {{ }} escaping is for HTML,
// and the browser decodes it again before running the attribute as JS, so
// a quote in a title/email could break out of the string. A data attribute
// is only ever read as text.

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-dialog-open]');
    if (opener) {
        document.getElementById(opener.dataset.dialogOpen)?.showModal();
        return;
    }

    const closer = event.target.closest('[data-dialog-close]');
    if (closer) {
        closer.closest('dialog')?.close();
        return;
    }

    // A click on the dialog element itself (not on anything inside it) is a
    // click on its backdrop.
    if (event.target instanceof HTMLDialogElement && event.target.hasAttribute('data-dialog-backdrop-close')) {
        event.target.close();
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm]');
    if (form && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});
