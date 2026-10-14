<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to the Main Admin only. Applied to the Admin Management
 * (/admin/users) group — a Sub Admin can never reach it, no matter what
 * permissions they've been granted, closing off self-promotion entirely.
 */
class EnsureUserIsMainAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isMainAdmin(), 403, 'Only the Main Admin can manage administrator accounts.');

        return $next($request);
    }
}
