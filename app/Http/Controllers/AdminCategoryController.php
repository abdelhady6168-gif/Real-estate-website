<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\NakheelCategoryService;
use Illuminate\Http\Request;

/**
 * Admin CRUD for the About Us page's "Real Estate Categories" cards.
 * No index route — cards render inline on the public About Us page with
 * Edit/Remove badges for admins.
 */
class AdminCategoryController extends Controller
{
    use HandlesImageUploads;
    use ValidatesBilingualFields;

    public function __construct(
        private NakheelCategoryService $categories,
    ) {}

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImagePath($request, 'image', null);

        $this->categories->create($data);

        return redirect()->route('about')->with('success', __('Category added successfully.'));
    }

    public function edit(int $id)
    {
        $category = $this->categories->find($id);
        abort_unless($category, 404);

        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, int $id)
    {
        $category = $this->categories->find($id);
        abort_unless($category, 404);

        $data = $this->validated($request);
        $data['image'] = $category['image'];

        $this->categories->update($id, $data);

        return redirect()->route('about')->with('success', __('Category updated successfully.'));
    }

    public function destroy(int $id)
    {
        $category = $this->categories->find($id);
        $this->deleteImageIfStored($category['image'] ?? null);

        $this->categories->delete($id);

        return redirect()->route('about')->with('success', __('Category removed.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required_without:title_ar', 'nullable', 'string', 'max:255'],
            'title_ar' => ['required_without:title', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], $this->bilingualRequiredMessages([
            'title' => 'title_ar',
        ]));
    }
}
