// Hydrates <img data-src> into <img src> the moment the <dialog> containing
// it is actually opened — see <x-nakheel.ph :defer-src="true">, used by
// <x-nakheel.gallery> for every image it renders (property/floor-plan
// photos and the full-size lightbox), since all of that content starts out
// inside a closed dialog.
//
// Patching .showModal() itself (rather than listening for clicks on
// whatever triggers each dialog) catches every dialog site-wide in one
// place, regardless of how it's opened — data-dialog-open (most of them,
// see dialogs.js), or JS-driven (the lightbox, see lightbox.js) — with no
// changes needed at any of those call sites, and no risk of missing one.
const nativeShowModal = HTMLDialogElement.prototype.showModal;

HTMLDialogElement.prototype.showModal = function () {
    this.querySelectorAll('img[data-src]').forEach((img) => {
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
    });

    return nativeShowModal.call(this);
};
