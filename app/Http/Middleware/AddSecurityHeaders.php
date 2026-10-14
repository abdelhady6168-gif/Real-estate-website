<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content-Security-Policy + anti-framing headers for every response
 * (registered globally in bootstrap/app.php, so 404s for unknown URLs are
 * covered too, not just matched routes).
 *
 * Scripts: only this app's own origin (the Vite-built bundle) plus inline
 * <script> blocks carrying this request's nonce — Vite::useCspNonce() makes
 * @vite add it to the tags it renders, and the few intentional inline
 * scripts (the before-paint dark-mode / mobile-nav guards in the layouts,
 * the admin timeline form) print it via Vite::cspNonce(). No inline event
 * handlers anywhere (see resources/js/dialogs.js), no 'unsafe-inline' /
 * 'unsafe-eval' for scripts.
 *
 * Everything else the site loads is same-origin: the bundled CSS, the fonts
 * (self-hosted by laravel-vite-plugin at build time), uploaded images under
 * /storage. The only other scheme needed is blob:, for the admin image
 * upload previews (URL.createObjectURL in resources/js/images.js). Outbound
 * links (Google Maps, WhatsApp, Telegram) are plain navigations, which CSP
 * doesn't restrict, so they need no entries.
 */
class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        // Laravel's own debug exception page (shown for unexpected 500s with
        // APP_DEBUG=true only — never in production) is built from inline
        // scripts/styles this app doesn't control; leave it readable rather
        // than blank while developing.
        $exception = $response->exception ?? null;
        if ($exception && config('app.debug') && $response->getStatusCode() >= 500) {
            return $this->withFrameProtection($response);
        }

        $response->headers->set('Content-Security-Policy', $this->policy(
            Vite::cspNonce(),
            // Laravel's built-in 404/419/429/500 pages (vendor views, not
            // editable here) style themselves with inline <style> blocks.
            // Allowed for styles only, and only on those error responses —
            // scripts stay nonce-only everywhere.
            allowInlineStyles: $exception !== null,
        ));

        return $this->withFrameProtection($response);
    }

    /**
     * The site is never meant to be shown inside another site's frame.
     * frame-ancestors (in the CSP above) is the modern control;
     * X-Frame-Options is kept alongside it for older browsers that don't
     * support frame-ancestors — where both are present, browsers that know
     * frame-ancestors use it and ignore X-Frame-Options.
     *
     * Temporary escape hatch for local testing tools that load the site in
     * an iframe (e.g. a mobile-preview browser extension): ALLOW_FRAMING=true
     * in .env (config('app.allow_framing')) drops BOTH anti-framing controls.
     * Off by default — never enable it on a real host.
     */
    private function withFrameProtection(Response $response): Response
    {
        if (! config('app.allow_framing')) {
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        return $response;
    }

    private function policy(string $nonce, bool $allowInlineStyles): string
    {
        $script = ["'self'", "'nonce-{$nonce}'"];
        $style = ["'self'"];
        $img = ["'self'", 'blob:'];
        $font = ["'self'"];
        $connect = ["'self'"];

        if ($allowInlineStyles) {
            $style[] = "'unsafe-inline'";
        }

        // `npm run dev`: assets come from the Vite dev server (a different
        // origin, e.g. http://localhost:5173) instead of /build, it injects
        // CSS as <style> tags, and it keeps a websocket open for hot reload.
        // Only when public/hot exists, i.e. only while developing locally.
        if (Vite::isRunningHot()) {
            $origin = rtrim(trim(file_get_contents(Vite::hotFile())), '/');
            $socket = preg_replace('/^http/', 'ws', $origin);

            $script[] = $origin;
            array_push($style, $origin, "'unsafe-inline'");
            $img[] = $origin;
            $font[] = $origin;
            array_push($connect, $origin, $socket);
        }

        return implode('; ', array_filter([
            "default-src 'self'",
            'script-src '.implode(' ', $script),
            'style-src '.implode(' ', array_unique($style)),
            'img-src '.implode(' ', $img),
            'font-src '.implode(' ', $font),
            'connect-src '.implode(' ', $connect),
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            // See withFrameProtection() for the ALLOW_FRAMING switch.
            config('app.allow_framing') ? null : "frame-ancestors 'none'",
        ]));
    }
}
