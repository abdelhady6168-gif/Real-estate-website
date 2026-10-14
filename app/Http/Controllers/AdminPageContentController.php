<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\Concerns\FormatsOfficeHours;
use App\Services\NakheelPageContentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin edit-only controller for singleton text+image content blocks
 * (About Us story, Future Vision, Future Projects office contact) — keyed
 * by name, always exist, no create/delete. Redirects back to whichever
 * public page the block lives on.
 */
class AdminPageContentController extends Controller
{
    use HandlesImageUploads;
    use FormatsOfficeHours;
    use ValidatesBilingualFields;

    private const REDIRECTS = [
        'about_story' => 'about',
        'about_hero' => 'about',
        'future_vision' => 'about',
        'office_contact' => 'future-projects',
        'future_projects_hero' => 'future-projects',
    ];

    public function __construct(
        private NakheelPageContentService $content,
    ) {}

    public function edit(string $key)
    {
        $content = $this->content->find($key);
        abort_unless($content, 404);

        return view('admin.page-content.edit', ['content' => $content]);
    }

    public function update(Request $request, string $key)
    {
        $existing = $this->content->find($key);
        abort_unless($existing, 404);
        abort_unless(array_key_exists($key, self::REDIRECTS), 404);

        $data = $request->validate([
            'heading' => ['required_without:heading_ar', 'nullable', 'string', 'max:255'],
            'heading_ar' => ['required_without:heading', 'nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'body_ar' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:255'],
            'office_address_ar' => ['nullable', 'string', 'max:255'],
            'office_hours' => ['nullable', 'string', 'max:255'],
            'office_hours_ar' => ['nullable', 'string', 'max:255'],
            'office_hours_from_day' => ['nullable', 'string', Rule::in(array_keys($this->officeHourDayOptions()))],
            'office_hours_to_day' => ['nullable', 'string', Rule::in(array_keys($this->officeHourDayOptions()))],
            'office_hours_from_time' => ['nullable', 'date_format:H:i'],
            'office_hours_to_time' => ['nullable', 'date_format:H:i'],
        ], $this->bilingualRequiredMessages([
            'heading' => 'heading_ar',
        ]));

        // Image is managed live via AdminImageController (AJAX) on the edit
        // form, so it's simply carried over unchanged here.
        $data['image'] = $existing['image'];

        $this->content->update($key, $data);

        return redirect()
            ->route(self::REDIRECTS[$key])
            ->with('success', __('Content updated successfully.'));
    }
}
