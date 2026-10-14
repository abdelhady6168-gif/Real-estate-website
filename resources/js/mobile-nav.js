// Phone/tablet nav drawer — see the header markup in
// resources/views/layouts/nakheel.blade.php (the `lg:hidden` block right
// after </header>). Only toggles a data-state attribute; the slide/fade
// animation and which side it opens from (RTL-aware) are pure Tailwind
// data-[state=...] classes on the panel/backdrop themselves.

function panel() {
    return document.querySelector('[data-mobile-nav-panel]');
}

function backdrop() {
    return document.querySelector('[data-mobile-nav-backdrop]');
}

function trigger() {
    return document.querySelector('[data-mobile-nav-toggle]');
}

function openMobileNav() {
    const p = panel();
    const b = backdrop();
    if (!p || !b) return;

    p.dataset.state = 'open';
    b.dataset.state = 'open';
    trigger()?.setAttribute('aria-expanded', 'true');
    document.documentElement.classList.add('overflow-hidden');
}

function closeMobileNav() {
    const p = panel();
    const b = backdrop();
    if (!p || !b) return;

    p.dataset.state = 'closed';
    b.dataset.state = 'closed';
    trigger()?.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('overflow-hidden');
}

// Release the synchronous inline-style FOUC guard set by the small
// <script> right after the drawer markup in layouts/nakheel.blade.php —
// by the time this module runs, the real stylesheet is in control, so the
// data-[state=closed] CSS classes (already correct, "closed") can take
// back over instead of the inline styles.
(function releaseFoucGuard() {
    const p = panel();
    const b = backdrop();
    if (p) p.style.transform = '';
    if (b) {
        b.style.opacity = '';
        b.style.pointerEvents = '';
    }
})();

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-mobile-nav-toggle]')) {
        openMobileNav();
        return;
    }

    // The backdrop only covers the area NOT under the panel (the panel sits
    // above it), so a click reaching the backdrop is always "outside" the
    // panel — no extra bounds-checking needed.
    if (event.target.closest('[data-mobile-nav-close]') || event.target.closest('[data-mobile-nav-backdrop]')) {
        closeMobileNav();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMobileNav();
});
