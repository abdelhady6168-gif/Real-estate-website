<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users with is_admin = true. Applied to the /admin
 * group in routes/web.php, on top of the normal `auth` gate — so a
 * signed-in non-admin user gets a 403 instead of being able to manage
 * the site's listings.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_admin, 403, 'Only administrators can manage the site content.');

        return $next($request);
    }
}
