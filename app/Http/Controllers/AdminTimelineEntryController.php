<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\NakheelTimelineEntryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD for the About Us page's "Our Project Portfolio" timeline.
 * No index route — entries render inline on the public About Us page with
 * Edit/Remove badges for admins.
 *
 * The "5 preset colors" for the status pill (see <x-nakheel.status-pill>)
 * reuse a curated subset of NakheelPropertyStatusService::COLORS — same
 * palette system already used for property availability statuses.
 */
class AdminTimelineEntryController extends Controller
{
    use HandlesImageUploads;
    use ValidatesBilingualFields;

    public const COLORS = ['emerald', 'sky', 'amber', 'rose', 'gray'];

    public function __construct(
        private NakheelTimelineEntryService $timeline,
    ) {}

    public function create()
    {
        return view('admin.timeline.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImagePath($request, 'image', null);

        $this->timeline->create($data);

        return redirect()->route('about')->with('success', __('Timeline entry added successfully.'));
    }

    public function edit(int $id)
    {
        $entry = $this->timeline->find($id);
        abort_unless($entry, 404);

        return view('admin.timeline.edit', ['entry' => $entry]);
    }

    public function update(Request $request, int $id)
    {
        $entry = $this->timeline->find($id);
        abort_unless($entry, 404);

        $data = $this->validated($request);
        $data['image'] = $entry['image'];

        $this->timeline->update($id, $data);

        return redirect()->route('about')->with('success', __('Timeline entry updated successfully.'));
    }

    public function destroy(int $id)
    {
        $entry = $this->timeline->find($id);
        $this->deleteImageIfStored($entry['image'] ?? null);

        $this->timeline->delete($id);

        return redirect()->route('about')->with('success', __('Timeline entry removed.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'year' => ['required', 'string', 'max:20'],
            'status' => ['required_without:status_ar', 'nullable', 'string', 'max:255'],
            'status_ar' => ['required_without:status', 'nullable', 'string', 'max:255'],
            'color' => ['required', 'string', Rule::in(self::COLORS)],
            'title' => ['required_without:title_ar', 'nullable', 'string', 'max:255'],
            'title_ar' => ['required_without:title', 'nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'string', 'max:255'],
            'meta_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], $this->bilingualRequiredMessages([
            'status' => 'status_ar',
            'title' => 'title_ar',
        ]));
    }
}
