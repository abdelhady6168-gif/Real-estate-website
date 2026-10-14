// Auto-dismisses the green "success" flash banner (see layouts/nakheel.blade.php
// and layouts/admin.blade.php) a few seconds after the page loads, instead of
// leaving it on screen until the user navigates away. Error banners are left
// alone — a user still needs time to read and act on those.
const DISMISS_AFTER_MS = 4000;
const FADE_MS = 300;

document.querySelectorAll('[data-flash-success]').forEach((banner) => {
    setTimeout(() => {
        banner.style.transition = `opacity ${FADE_MS}ms ease`;
        banner.style.opacity = '0';
        setTimeout(() => banner.remove(), FADE_MS);
    }, DISMISS_AFTER_MS);
});
