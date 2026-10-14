// Keeps a [data-fit-text] label on ONE line inside its fixed-width box:
// text that already fits is left exactly as styled; text that's too wide
// has its font size stepped down just far enough to fit — no wrapping, no
// truncation, and the box never grows. Used for the project page's
// Property Types stat and highlight labels (nakheel/project-show.blade.php).
//
// The floor is 10px — the size of the small uppercase captions already
// used in these same boxes, i.e. the smallest text the design has — so
// nothing becomes unreadably small. If a label is so long it still doesn't
// fit at the floor, it's allowed to wrap at that smallest size rather than
// spill out of its box.

const MIN_PX = 10;
const STEP_PX = 0.5;

function fit(el) {
    // Back to the stylesheet's size first, so growing the viewport restores it.
    el.style.fontSize = '';
    el.style.whiteSpace = 'nowrap';
    el.style.overflowWrap = '';

    // Hidden at this breakpoint (display: none) — nothing to measure.
    if (el.clientWidth === 0) return;

    let size = parseFloat(getComputedStyle(el).fontSize);

    while (el.scrollWidth > el.clientWidth && size > MIN_PX) {
        size = Math.max(MIN_PX, size - STEP_PX);
        el.style.fontSize = size + 'px';
    }

    if (el.scrollWidth > el.clientWidth) {
        el.style.whiteSpace = 'normal';
        el.style.overflowWrap = 'break-word';
    }
}

function fitAll() {
    document.querySelectorAll('[data-fit-text]').forEach(fit);
}

let queued = false;
function scheduleFit() {
    if (queued) return;
    queued = true;
    requestAnimationFrame(() => {
        queued = false;
        fitAll();
    });
}

fitAll();
// Web fonts change text widths once they finish loading.
document.fonts?.ready.then(fitAll);
window.addEventListener('resize', scheduleFit);
// Same-page project switching swaps in new labels — see project-nav.js.
document.addEventListener('project:swapped', fitAll);
