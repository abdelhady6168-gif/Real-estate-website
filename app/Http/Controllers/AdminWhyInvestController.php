<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\NakheelWhyInvestService;
use App\Support\WhyInvestIcons;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD for the Future Projects page's "Why Invest With Nakheel" cards.
 * No index route — cards render inline on the public Future Projects page
 * with Edit/Remove badges for admins.
 */
class AdminWhyInvestController extends Controller
{
    use HandlesImageUploads;
    use ValidatesBilingualFields;

    public function __construct(
        private NakheelWhyInvestService $whyInvest,
    ) {}

    public function create()
    {
        return view('admin.why-invest.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $this->whyInvest->create($data);

        return redirect()->route('future-projects')->with('success', __('Reason added successfully.'));
    }

    public function edit(int $id)
    {
        $item = $this->whyInvest->find($id);
        abort_unless($item, 404);

        return view('admin.why-invest.edit', ['item' => $item]);
    }

    public function update(Request $request, int $id)
    {
        $item = $this->whyInvest->find($id);
        abort_unless($item, 404);

        $data = $this->validated($request);

        $this->whyInvest->update($id, $data);

        return redirect()->route('future-projects')->with('success', __('Reason updated successfully.'));
    }

    public function destroy(int $id)
    {
        $item = $this->whyInvest->find($id);
        $this->deleteImageIfStored($item['image'] ?? null);

        $this->whyInvest->delete($id);

        return redirect()->route('future-projects')->with('success', __('Reason removed.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required_without:title_ar', 'nullable', 'string', 'max:255'],
            'title_ar' => ['required_without:title', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', Rule::in(array_keys(WhyInvestIcons::available()))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], $this->bilingualRequiredMessages([
            'title' => 'title_ar',
        ]));
    }
}
