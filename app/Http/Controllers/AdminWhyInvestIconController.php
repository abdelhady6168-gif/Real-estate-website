<?php

namespace App\Http\Controllers;

use App\Support\WhyInvestIcons;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Main Admin (or a Sub Admin granted the `why_invest_icons` permission —
 * see AdminPermissions) manages which icons from the trusted
 * App\Support\WhyInvestIcons catalog are available in the Why-Invest icon
 * picker. This never accepts uploaded files or raw SVG/HTML — only a `key`
 * that must already exist in the hardcoded catalog (Rule::in below), so an
 * admin can never introduce markup that isn't already vetted in the
 * codebase.
 */
class AdminWhyInvestIconController extends Controller
{
    public function index()
    {
        $catalog = WhyInvestIcons::all();
        $availableKeys = array_keys(WhyInvestIcons::available());

        return view('admin.why-invest-icons.index', [
            'available' => WhyInvestIcons::available(),
            'more' => collect($catalog)->except($availableKeys)->all(),
            'usage' => WhyInvestIcons::usageCounts(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', Rule::in(array_keys(WhyInvestIcons::all()))],
        ]);

        WhyInvestIcons::addAvailable($data['key']);

        return redirect()
            ->route('admin.why-invest-icons.index')
            ->with('success', __('Icon added to the library.'));
    }

    public function destroy(string $key)
    {
        if (WhyInvestIcons::isInUse($key)) {
            return redirect()
                ->route('admin.why-invest-icons.index')
                ->with('error', __('This icon is currently used by one or more Why Invest reasons and cannot be removed. Change those reasons to a different icon first.'));
        }

        WhyInvestIcons::removeAvailable($key);

        return redirect()
            ->route('admin.why-invest-icons.index')
            ->with('success', __('Icon removed from the library.'));
    }
}
