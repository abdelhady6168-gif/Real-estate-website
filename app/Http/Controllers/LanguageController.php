<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Switches the site language (see the header dropdown) and remembers it in the session. */
class LanguageController extends Controller
{
    public static function middleware(): array
    {
        return [];
    }

    public function switch(Request $request, string $locale)
    {
        abort_unless(in_array($locale, ['en', 'ar'], true), 404);

        $request->session()->put('locale', $locale);

        return redirect()->back();
    }
}
