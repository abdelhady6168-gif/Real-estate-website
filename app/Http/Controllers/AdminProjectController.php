<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsConcurrentEdits;
use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\Concerns\FormatsOfficeHours;
use App\Services\NakheelProjectHighlightService;
use App\Services\NakheelProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD for Nakheel projects (developments): Route -> Controller ->
 * Service, like the rest of the admin controllers.
 *
 * On the edit form, `hero_images`/`environment_images` are managed live via
 * AdminImageController (AJAX add/remove), so update() leaves those two
 * columns untouched; only create() (no id yet to AJAX-upload against) still
 * resolves them from the legacy form-submit path. Highlights are a nested
 * repeater synced on save — see syncHighlights().
 */
class AdminProjectController extends Controller
{
    use GuardsConcurrentEdits;
    use HandlesImageUploads;
    use FormatsOfficeHours;
    use ValidatesBilingualFields;

    public function __construct(
        private NakheelProjectService $projects,
        private NakheelProjectHighlightService $highlights,
    ) {}

    public function index()
    {
        return view('admin.projects.index', [
            'projects' => $this->projects->all(),
        ]);
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['hero_images'] = $this->resolveImagePath($request, 'hero_images', null);
        $data['environment_images'] = $this->resolveImageList($request, 'environment_images', null);

        $id = $this->projects->create($data);
        $this->syncHighlights($request, $id);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', __('Project created successfully.'));
    }

    public function edit(int $id)
    {
        $project = $this->projects->find($id);
        abort_unless($project, 404);

        return view('admin.projects.edit', [
            'project' => $project,
            'editVersion' => $this->editVersion($this->projectSnapshot($id)),
            'highlights' => $this->highlights->forProject($id),
        ]);
    }

    /**
     * What the edit form saves, for GuardsConcurrentEdits: the project's own
     * fields plus its highlights (edited on the same form). Photos — the
     * project's and each highlight's — and timestamps are excluded (see the trait).
     */
    private function projectSnapshot(int $id): ?array
    {
        $row = $this->projects->find($id);
        if (! $row) {
            return null;
        }

        return [
            'project' => Arr::except($row, ['hero_images', 'environment_images', 'created_at', 'updated_at']),
            'highlights' => collect($this->highlights->forProject($id))
                ->map(fn ($highlight) => Arr::except($highlight, ['image', 'created_at', 'updated_at']))
                ->all(),
        ];
    }

    private function projectDeletedMeanwhile()
    {
        return redirect()
            ->route('admin.projects.index')
            ->with('error', __('This project was deleted by another admin, so your changes could not be saved.'));
    }

    public function update(Request $request, int $id)
    {
        if (! $this->projects->find($id)) {
            return $this->projectDeletedMeanwhile();
        }

        $data = $this->validated($request, $id);

        $result = $this->saveUnlessChanged('nakheel_projects', $id, $request->input('edit_version'),
            fn () => $this->projectSnapshot($id),
            function () use ($request, $id, $data) {
                // Photos are managed live on the edit page (AdminImageController),
                // so keep them as they are now, not as they were when the form opened.
                $current = $this->projects->find($id);
                $data['hero_images'] = $current['hero_images'];
                $data['environment_images'] = $current['environment_images'];

                $this->projects->update($id, $data);
                $this->syncHighlights($request, $id);
            });

        return match ($result) {
            'saved' => redirect()->route('admin.projects.index')->with('success', __('Project updated successfully.')),
            'deleted' => $this->projectDeletedMeanwhile(),
            'changed' => redirect()->route('admin.projects.edit', $id)->withErrors([
                'edit_version' => __('Another admin changed this project after you opened it, so your changes were not saved. The form now shows the latest version — please make your changes again.'),
            ]),
        };
    }

    public function destroy(int $id)
    {
        $project = $this->projects->find($id);

        if ($project) {
            collect(explode(',', $project['hero_images'] ?? ''))
                ->merge(explode(',', $project['environment_images'] ?? ''))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->each(fn ($path) => $this->deleteImageIfStored($path));
        }

        collect($this->highlights->forProject($id))->each(function ($highlight) {
            $this->deleteImageIfStored($highlight['image'] ?? null);
            $this->highlights->delete($highlight['id']);
        });

        $this->projects->delete($id);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', __('Project deleted. Any properties assigned to it are now unassigned.'));
    }

    /**
     * Diffs the submitted `highlights[<key>][label|id|image]` rows against
     * what's already stored: existing rows (real numeric id) get their
     * label updated, rows with no id are created (with an optional
     * newly-uploaded image), and any existing id that's no longer present
     * in the submission was removed by the admin and gets deleted.
     */
    private function syncHighlights(Request $request, int $projectId): void
    {
        $existing = collect($this->highlights->forProject($projectId));
        $submitted = collect($request->input('highlights', []));
        $keptIds = [];

        foreach ($submitted as $key => $row) {
            $label = trim($row['label'] ?? '');
            if ($label === '') {
                continue;
            }

            $labelAr = trim($row['label_ar'] ?? '') ?: null;
            $id = filled($row['id'] ?? null) ? (int) $row['id'] : null;

            if ($id && $existing->contains('id', $id)) {
                $keptIds[] = $id;
                $this->highlights->update($id, ['label' => $label, 'label_ar' => $labelAr]);

                continue;
            }

            $file = $request->file("highlights.{$key}.image");
            $newId = $this->highlights->create($projectId, [
                'label' => $label,
                'label_ar' => $labelAr,
                'image' => $file ? $this->storeImage($file) : null,
            ]);
            $keptIds[] = $newId;
        }

        $existing->whereNotIn('id', $keptIds)->each(function ($highlight) {
            $this->deleteImageIfStored($highlight['image'] ?? null);
            $this->highlights->delete($highlight['id']);
        });
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('nakheel_projects', 'slug')->ignore($ignoreId),
            ],
            'name' => ['required_without:name_ar', 'nullable', 'string', 'max:255'],
            'name_ar' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'type' => ['required_without:type_ar', 'nullable', 'string', 'max:255'],
            'type_ar' => ['required_without:type', 'nullable', 'string', 'max:255'],
            'location' => ['required_without:location_ar', 'nullable', 'string', 'max:255'],
            'location_ar' => ['required_without:location', 'nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'tagline_ar' => ['nullable', 'string', 'max:255'],
            'hero_images' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hero_images_remove' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'developer' => ['nullable', 'string', 'max:255'],
            'developer_ar' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'status_ar' => ['nullable', 'string', 'max:255'],
            'completion_year' => ['nullable', 'string', 'max:20'],
            'total_units' => ['nullable', 'integer', 'min:0'],
            'available_units' => ['nullable', 'integer', 'min:0'],
            'development_area' => ['nullable', 'string', 'max:255'],
            'development_area_ar' => ['nullable', 'string', 'max:255'],
            'property_types' => ['nullable', 'string', 'max:255'],
            'property_types_ar' => ['nullable', 'string', 'max:255'],
            'environment_images' => ['nullable', 'array'],
            'environment_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'environment_images_keep' => ['nullable', 'array'],
            'environment_images_keep.*' => ['string'],
            'highlights' => ['nullable', 'array'],
            'highlights.*.id' => ['nullable', 'integer'],
            'highlights.*.label' => ['nullable', 'string', 'max:255'],
            'highlights.*.label_ar' => ['nullable', 'string', 'max:255'],
            'highlights.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_role' => ['nullable', 'string', 'max:255'],
            'manager_role_ar' => ['nullable', 'string', 'max:255'],
            'manager_phone' => ['nullable', 'string', 'max:255'],
            'manager_whatsapp' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['nullable', 'string', 'max:255'],
            'office_name' => ['nullable', 'string', 'max:255'],
            'office_name_ar' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:255'],
            'office_address_ar' => ['nullable', 'string', 'max:255'],
            'office_hours' => ['nullable', 'string', 'max:255'],
            'office_hours_ar' => ['nullable', 'string', 'max:255'],
            'office_hours_from_day' => ['nullable', 'string', Rule::in(array_keys($this->officeHourDayOptions()))],
            'office_hours_to_day' => ['nullable', 'string', Rule::in(array_keys($this->officeHourDayOptions()))],
            'office_hours_from_time' => ['nullable', 'date_format:H:i'],
            'office_hours_to_time' => ['nullable', 'date_format:H:i'],
            'maps_url' => ['nullable', 'string', 'max:2048'],
            'maps_location_name' => ['nullable', 'string', 'max:255'],
            'maps_location_name_ar' => ['nullable', 'string', 'max:255'],
        ], $this->bilingualRequiredMessages([
            'name' => 'name_ar',
            'type' => 'type_ar',
            'location' => 'location_ar',
        ]));
    }
}
