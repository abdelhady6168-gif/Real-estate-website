// Dark mode toggle — see the inline no-flash script in
// resources/views/layouts/nakheel.blade.php's <head>, which sets the initial
// `dark` class before paint. This just handles the click.

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');
    if (!button) return;

    const isDark = document.documentElement.classList.toggle('dark');

    try {
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    } catch (e) {
        // Storage unavailable (private mode, etc.) — the toggle still works
        // for the rest of this page view, it just won't persist.
    }
});
