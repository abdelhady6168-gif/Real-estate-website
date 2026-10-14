<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the visitor's chosen language (English/Arabic — see LanguageController)
 * on every request, so it persists across the whole site (public + admin)
 * once picked from the header dropdown.
 */
class SetLocale
{
    private const SUPPORTED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', 'en');

        if (in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
