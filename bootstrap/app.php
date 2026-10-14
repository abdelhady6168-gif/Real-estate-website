<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsMainAdmin;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'main-admin' => EnsureUserIsMainAdmin::class,
            'permission' => EnsureUserHasPermission::class,
        ]);
        $middleware->web(append: [SetLocale::class]);
        $middleware->append(AddSecurityHeaders::class);

        // Trust X-Forwarded-* headers from any reverse proxy in front of the
        // app (VS Code port forwarding / devtunnels, ngrok, a real load
        // balancer in production, etc.) so Laravel's own generated URLs
        // (asset/CSS/JS links, redirects) use the proxy's public
        // host/scheme instead of the app's own internal one — without
        // this, tunneling the dev server anywhere shows an unstyled page
        // because the CSS/JS links point at the wrong host.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
