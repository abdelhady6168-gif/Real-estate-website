// Same-page prev/next project switching — see the data-project-nav arrows
// and the data-project-content wrapper in resources/views/nakheel/project-show.blade.php.
// Reuses the exact same route/controller/view as a normal visit (no new
// backend endpoint): fetches the target project's full page, pulls out
// just its data-project-content region, and swaps it in — instead of a
// full browser navigation, which was causing a visible flash/reload.
// Every other page's navigation is untouched.
//
// On top of that, once the current project has shown, the previous and next
// projects are fetched quietly in the background (plus their hero image), so
// an arrow click usually swaps in content that has already arrived instead
// of waiting on a round trip. A click while a preload is still in flight
// just waits on that same request — never a second one.

// url -> Promise<string> of that project page's HTML.
const pageCache = new Map();

function fetchProjectHtml(url) {
    if (!pageCache.has(url)) {
        const request = fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => {
                if (!response.ok) throw new Error('Bad response: ' + response.status);
                return response.text();
            })
            .catch((error) => {
                pageCache.delete(url);
                throw error;
            });

        pageCache.set(url, request);
    }

    return pageCache.get(url);
}

function takeProjectHtml(url) {
    const request = fetchProjectHtml(url);
    // Used once, then dropped: the page is re-preloaded fresh the next time
    // it's a neighbour, so a cached copy never goes stale for long.
    pageCache.delete(url);
    return request;
}

function preloadNeighbours() {
    if (navigator.connection?.saveData) return;

    document.querySelectorAll('[data-project-content] [data-project-nav]').forEach((link) => {
        fetchProjectHtml(link.href)
            .then((html) => {
                // Warms the hero photo too: once it's been loaded in this
                // document, the swapped-in <img> for the same URL shows it
                // straight away instead of downloading it after the swap.
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const heroSrc = doc.querySelector('[data-project-content] img[src]')?.getAttribute('src');
                if (heroSrc) new Image().src = heroSrc;
            })
            .catch(() => {});
    });
}

// Waits for the current project's hero image before preloading, so the
// background requests don't compete with what's actually on screen.
function preloadNeighboursWhenSettled() {
    const hero = document.querySelector('[data-project-content] img[src]');

    if (!hero || hero.complete) {
        preloadNeighbours();
        return;
    }

    let started = false;
    const start = () => {
        if (started) return;
        started = true;
        preloadNeighbours();
    };

    hero.addEventListener('load', start, { once: true });
    hero.addEventListener('error', start, { once: true });
    setTimeout(start, 3000);
}

async function swapProjectContent(url, options = {}) {
    const current = document.querySelector('[data-project-content]');
    if (!current) return false;

    // Deliberately no loading/dimmed state while this is in flight: the
    // fetch is normally fast enough (same route/data a real navigation
    // would need anyway) that the old content just stays fully visible
    // until the new content is ready to swap in — the same thing a plain
    // browser navigation does (old page stays put until the new one has
    // arrived), just without the full reload. Introducing our own
    // "loading" visual here was making an otherwise-ordinary fetch read as
    // a stall, especially on a slower connection.
    try {
        const html = await takeProjectHtml(url);
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const next = doc.querySelector('[data-project-content]');
        if (!next) throw new Error('No data-project-content in response');

        current.replaceWith(next);
        document.title = doc.title;

        if (!options.skipPushState) {
            history.pushState({ projectNav: true }, '', url);
        }

        window.scrollTo({ top: 0 });
        // Lets per-element scripts re-run on the new content (fit-text.js).
        document.dispatchEvent(new Event('project:swapped'));
        preloadNeighboursWhenSettled();
        return true;
    } catch (e) {
        return false;
    }
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-project-nav]');
    if (!link) return;

    event.preventDefault();
    swapProjectContent(link.href).then((ok) => {
        if (!ok) window.location.href = link.href;
    });
});

window.addEventListener('popstate', (event) => {
    if (!document.querySelector('[data-project-content]')) return;
    swapProjectContent(window.location.href, { skipPushState: true }).then((ok) => {
        if (!ok) window.location.reload();
    });
});

if (document.querySelector('[data-project-content]')) {
    if (document.readyState === 'complete') {
        preloadNeighboursWhenSettled();
    } else {
        window.addEventListener('load', preloadNeighboursWhenSettled, { once: true });
    }
}
