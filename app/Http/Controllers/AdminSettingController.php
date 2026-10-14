<?php

namespace App\Http\Controllers;

use App\Services\NakheelSettingService;
use Illuminate\Http\Request;

/**
 * Tiny controller for the handful of global site toggles backed by
 * nakheel_settings. Currently just the Sold Properties Visibility switch
 * shown at the top of the Properties page (see NakheelController::properties()).
 *
 * Called via fetch() (see resources/js/sold-visibility.js) so the toggle can
 * animate instantly instead of round-tripping a full page reload — returns
 * JSON directly rather than relying on the framework default, which (per
 * shouldRenderJsonWhen() in bootstrap/app.php) only auto-renders JSON for
 * `api/*` routes.
 */
class AdminSettingController extends Controller
{
    public function __construct(
        private NakheelSettingService $settings,
    ) {}

    public function updateSoldVisibility(Request $request)
    {
        $value = $request->boolean('show_sold_properties');

        $this->settings->set('show_sold_properties', $value);

        // Flashed now, read on the page reload resources/js/sold-visibility.js
        // triggers right after this — same session('success') banner every
        // other admin action in this app already uses (see layouts/nakheel.blade.php).
        session()->flash('success', $value
            ? __('Sold properties are now visible to normal users.')
            : __('Sold properties are now hidden from normal users.'));

        return response()->json(['show_sold_properties' => $value]);
    }
}
