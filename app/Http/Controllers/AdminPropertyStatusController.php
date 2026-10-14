<?php

namespace App\Http\Controllers;

use App\Services\NakheelPropertyStatusService;
use Illuminate\Http\Request;

/**
 * Admin CRUD for the property availability status list (Purchasable/Booked/
 * Sold/Rentable by default). Own dedicated admin index page, same
 * card-grid/click-to-edit/red-minus pattern as AdminProjectController.
 */
class AdminPropertyStatusController extends Controller
{
    public function __construct(
        private NakheelPropertyStatusService $statuses,
    ) {}

    public function index()
    {
        return view('admin.statuses.index', [
            'statuses' => $this->statuses->all(),
        ]);
    }

    public function create()
    {
        return view('admin.statuses.create');
    }

    public function store(Request $request)
    {
        $this->statuses->create([...$this->validated($request), 'is_sold' => $request->boolean('is_sold')]);

        return redirect()
            ->route('admin.statuses.index')
            ->with('success', __('Status created successfully.'));
    }

    public function edit(int $id)
    {
        $status = $this->statuses->find($id);
        abort_unless($status, 404);

        return view('admin.statuses.edit', [
            'status' => $status,
        ]);
    }

    public function update(Request $request, int $id)
    {
        abort_unless($this->statuses->find($id), 404);

        $this->statuses->update($id, [...$this->validated($request), 'is_sold' => $request->boolean('is_sold')]);

        return redirect()
            ->route('admin.statuses.index')
            ->with('success', __('Status updated successfully.'));
    }

    public function destroy(int $id)
    {
        $this->statuses->delete($id);

        return redirect()
            ->route('admin.statuses.index')
            ->with('success', __('Status deleted.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:' . implode(',', array_keys(NakheelPropertyStatusService::COLORS))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
