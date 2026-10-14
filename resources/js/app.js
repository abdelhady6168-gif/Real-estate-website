import './lazy-dialog-images.js';
import './dialogs.js';
import './images.js';
import './repeater.js';
import './lightbox.js';
import './select.js';
import './password-strength.js';
import './icon-search.js';
import './theme.js';
import './mobile-nav.js';
import './project-nav.js';
import './fit-text.js';
import './calendar.js';
import './date-picker.js';
import './availability.js';
import './sold-visibility.js';
import './flash.js';

// Generic image carousel — see resources/views/components/nakheel/image-carousel.blade.php.
// Works via data attributes + event delegation so no per-instance script tags are needed.
function goToSlide(root, index) {
    const slides = [...root.querySelectorAll('[data-slide]')];
    const dots = [...root.querySelectorAll('[data-dot]')];
    if (!slides.length) return;

    const next = ((index % slides.length) + slides.length) % slides.length;

    slides.forEach((slide, i) => slide.classList.toggle('hidden', i !== next));
    dots.forEach((dot, i) => {
        dot.classList.toggle('bg-white', i === next);
        dot.classList.toggle('w-4', i === next);
        dot.classList.toggle('bg-white/40', i !== next);
        dot.classList.toggle('w-1.5', i !== next);
    });
}

document.addEventListener('click', (event) => {
    const prevBtn = event.target.closest('[data-carousel-prev]');
    const nextBtn = event.target.closest('[data-carousel-next]');
    const dotBtn = event.target.closest('[data-dot]');
    const button = prevBtn || nextBtn || dotBtn;
    if (!button) return;

    const root = button.closest('[data-carousel]');
    if (!root) return;

    const slides = [...root.querySelectorAll('[data-slide]')];
    const current = slides.findIndex((slide) => !slide.classList.contains('hidden'));

    if (dotBtn) {
        goToSlide(root, Number(dotBtn.dataset.dot));
    } else {
        goToSlide(root, current + (nextBtn ? 1 : -1));
    }
});
