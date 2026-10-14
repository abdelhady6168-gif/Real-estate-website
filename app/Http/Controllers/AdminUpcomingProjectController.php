<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\NakheelUpcomingProjectService;
use Illuminate\Http\Request;

/**
 * Admin CRUD for the Future Projects page's "Upcoming Projects" cards.
 * No index route — cards render inline on the public Future Projects page
 * with Edit/Remove badges for admins.
 */
class AdminUpcomingProjectController extends Controller
{
    use HandlesImageUploads;
    use ValidatesBilingualFields;

    public function __construct(
        private NakheelUpcomingProjectService $upcoming,
    ) {}

    public function create()
    {
        return view('admin.upcoming.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImagePath($request, 'image', null);

        $this->upcoming->create($data);

        return redirect()->route('future-projects')->with('success', __('Upcoming project added successfully.'));
    }

    public function edit(int $id)
    {
        $item = $this->upcoming->find($id);
        abort_unless($item, 404);

        return view('admin.upcoming.edit', ['item' => $item]);
    }

    public function update(Request $request, int $id)
    {
        $item = $this->upcoming->find($id);
        abort_unless($item, 404);

        $data = $this->validated($request);
        $data['image'] = $item['image'];

        $this->upcoming->update($id, $data);

        return redirect()->route('future-projects')->with('success', __('Upcoming project updated successfully.'));
    }

    public function destroy(int $id)
    {
        $item = $this->upcoming->find($id);
        $this->deleteImageIfStored($item['image'] ?? null);

        $this->upcoming->delete($id);

        return redirect()->route('future-projects')->with('success', __('Upcoming project removed.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'badge' => ['nullable', 'string', 'max:255'],
            'badge_ar' => ['nullable', 'string', 'max:255'],
            'title' => ['required_without:title_ar', 'nullable', 'string', 'max:255'],
            'title_ar' => ['required_without:title', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'launch' => ['nullable', 'string', 'max:255'],
            'launch_ar' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], $this->bilingualRequiredMessages([
            'title' => 'title_ar',
        ]));
    }
}
