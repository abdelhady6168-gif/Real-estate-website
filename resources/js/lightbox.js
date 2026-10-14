// Powers <x-nakheel.gallery> — opens a <dialog> lightbox at the clicked
// image's index and lets the visitor page through every image in the set
// (not just the 4 shown in the grid).

function goToGallerySlide(dialog, index) {
    const slides = [...dialog.querySelectorAll('[data-gallery-slide]')];
    if (!slides.length) return;

    const next = ((index % slides.length) + slides.length) % slides.length;
    slides.forEach((slide, i) => slide.classList.toggle('hidden', i !== next));

    const counter = dialog.querySelector('[data-gallery-counter]');
    if (counter) counter.textContent = `${next + 1} / ${slides.length}`;
}

document.addEventListener('click', (event) => {
    const openBtn = event.target.closest('[data-gallery-open]');
    if (openBtn) {
        const dialog = document.getElementById(openBtn.dataset.galleryTarget);
        if (dialog) {
            goToGallerySlide(dialog, Number(openBtn.dataset.galleryIndex));
            dialog.showModal();
        }
        return;
    }

    const navBtn = event.target.closest('[data-gallery-prev], [data-gallery-next]');
    if (navBtn) {
        const dialog = navBtn.closest('[data-gallery-dialog]');
        const slides = [...dialog.querySelectorAll('[data-gallery-slide]')];
        const current = slides.findIndex((slide) => !slide.classList.contains('hidden'));
        goToGallerySlide(dialog, current + (navBtn.hasAttribute('data-gallery-next') ? 1 : -1));
    }
});
