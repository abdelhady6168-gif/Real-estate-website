<?php

namespace App\Http\Middleware;

use App\Support\AdminPermissions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Section-level (and action-level) authorization for the /admin route
 * group, on top of the existing `admin` middleware's coarse is_admin check.
 * The Main Admin always passes (see User::hasPermission()) — a Sub Admin
 * only passes if they were granted the specific action on the specific
 * section this route performs.
 *
 * Usage:
 *   ->middleware('permission:properties')   — static section, e.g. a plain resource route
 *   ->middleware('permission')              — no section given: resolved at runtime from the
 *                                              route's {resource}/{key} parameter (page-content
 *                                              and the shared image upload/delete routes, which
 *                                              serve multiple sections through one route)
 */
class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, ?string $section = null): Response
    {
        $user = $request->user();
        abort_if($user?->isDisabled(), 403, 'Your account has been disabled.');

        $route = $request->route();
        $section ??= $this->resolveDynamicSection($route);
        $action = AdminPermissions::actionForRouteMethod($route->getActionMethod());

        abort_unless($section && $user?->hasPermission($section, $action), 403, 'You do not have permission to do that.');

        return $next($request);
    }

    private function resolveDynamicSection($route): ?string
    {
        if ($resource = $route->parameter('resource')) {
            return AdminPermissions::sectionForImageResource($resource, $route->parameter('id'));
        }

        if ($key = $route->parameter('key')) {
            return AdminPermissions::sectionForPageContentKey($key);
        }

        return null;
    }
}
