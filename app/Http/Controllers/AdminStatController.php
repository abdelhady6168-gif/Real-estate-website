<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\NakheelStatService;
use Illuminate\Http\Request;

/**
 * Admin CRUD for the About Us page's stat cards ("12+ Years of Experience").
 * No index route — cards render inline on the public About Us page with
 * Edit/Remove badges for admins (see resources/views/nakheel/about.blade.php).
 */
class AdminStatController extends Controller
{
    use ValidatesBilingualFields;

    public function __construct(
        private NakheelStatService $stats,
    ) {}

    public function create()
    {
        return view('admin.stats.create');
    }

    public function store(Request $request)
    {
        $this->stats->create($this->validated($request));

        return redirect()->route('about')->with('success', __('Stat added successfully.'));
    }

    public function edit(int $id)
    {
        $stat = $this->stats->find($id);
        abort_unless($stat, 404);

        return view('admin.stats.edit', ['stat' => $stat]);
    }

    public function update(Request $request, int $id)
    {
        abort_unless($this->stats->find($id), 404);

        $this->stats->update($id, $this->validated($request));

        return redirect()->route('about')->with('success', __('Stat updated successfully.'));
    }

    public function destroy(int $id)
    {
        $this->stats->delete($id);

        return redirect()->route('about')->with('success', __('Stat removed.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'value' => ['required_without:value_ar', 'nullable', 'string', 'max:255'],
            'value_ar' => ['required_without:value', 'nullable', 'string', 'max:255'],
            'label' => ['required_without:label_ar', 'nullable', 'string', 'max:255'],
            'label_ar' => ['required_without:label', 'nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], $this->bilingualRequiredMessages([
            'value' => 'value_ar',
            'label' => 'label_ar',
        ]));
    }
}
