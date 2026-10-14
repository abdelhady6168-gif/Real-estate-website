<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsConcurrentEdits;
use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\ValidatesBilingualFields;
use App\Services\NakheelProjectService;
use App\Services\NakheelPropertyAvailabilityService;
use App\Services\NakheelPropertyService;
use App\Services\NakheelPropertyStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD for Nakheel property listings. There is no admin "index" —
 * the public /properties page IS the listing screen; the Edit/Remove
 * controls live directly on each <x-nakheel.property-card> for admins
 * (see resources/views/components/nakheel/property-card.blade.php), and
 * every action below redirects back there.
 *
 * On the edit form, `images`/`floor_plans` are managed live via
 * AdminImageController (AJAX add/remove — see resources/js/images.js), so
 * update() leaves those two columns untouched; only create() (no id yet to
 * AJAX-upload against) still resolves them from the legacy form-submit path.
 */
class AdminPropertyController extends Controller
{
    use GuardsConcurrentEdits;
    use HandlesImageUploads;
    use ValidatesBilingualFields;

    public function __construct(
        private NakheelPropertyService $properties,
        private NakheelProjectService $projects,
        private NakheelPropertyStatusService $statuses,
        private NakheelPropertyAvailabilityService $availability,
    ) {}

    public function create()
    {
        return view('admin.properties.create', [
            'projectOptions' => $this->projects->options(),
            'statusOptions' => $this->statuses->names(),
            'statusLabels' => $this->statuses->namesWithLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['specs'] = $this->encodeSpecs($request);
        $data['features'] = $this->encodeFeatures($request);
        $data['images'] = $this->resolveImageList($request, 'images', null);
        $data['floor_plans'] = $this->resolveImageList($request, 'floor_plans', null);

        if ($data['featured_on_login']) {
            $this->properties->clearFeaturedOnLogin();
        }

        $this->properties->create($data);

        return redirect()
            ->route('properties.index')
            ->with('success', __('Property created successfully.'));
    }

    public function edit(int $id)
    {
        $property = $this->properties->find($id);
        abort_unless($property, 404);

        return view('admin.properties.edit', [
            'property' => $property,
            'editVersion' => $this->editVersion($this->propertySnapshot($id)),
            'projectOptions' => $this->projects->options(),
            'statusOptions' => $this->statuses->names(),
            'statusLabels' => $this->statuses->namesWithLabels(),
            'availabilityRanges' => $this->availability->forProperty($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        if (! $this->properties->find($id)) {
            return $this->propertyDeletedMeanwhile();
        }

        $data = $this->validated($request);
        $data['specs'] = $this->encodeSpecs($request);
        $data['features'] = $this->encodeFeatures($request);

        $result = $this->saveUnlessChanged('nakheel_properties', $id, $request->input('edit_version'),
            fn () => $this->propertySnapshot($id),
            function () use ($id, $data) {
                // Photos are managed live on the edit page (AdminImageController),
                // so keep them exactly as they are now — not as they were when
                // this form was opened, which could drop a photo added since.
                $current = $this->properties->find($id);
                $data['images'] = $current['images'];
                $data['floor_plans'] = $current['floor_plans'];

                if ($data['featured_on_login']) {
                    $this->properties->clearFeaturedOnLogin();
                }

                $this->properties->update($id, $data);
            });

        return match ($result) {
            'saved' => redirect()->route('properties.index')->with('success', __('Property updated successfully.')),
            'deleted' => $this->propertyDeletedMeanwhile(),
            'changed' => redirect()->route('admin.properties.edit', $id)->withErrors([
                'edit_version' => __('Another admin changed this property after you opened it, so your changes were not saved. The form now shows the latest version — please make your changes again.'),
            ]),
        };
    }

    /** What the edit form saves, for GuardsConcurrentEdits — photos/timestamps excluded (see the trait). */
    private function propertySnapshot(int $id): ?array
    {
        $row = $this->properties->find($id);

        return $row ? Arr::except($row, ['images', 'floor_plans', 'created_at', 'updated_at']) : null;
    }

    private function propertyDeletedMeanwhile()
    {
        return redirect()
            ->route('properties.index')
            ->with('error', __('This property was deleted by another admin, so your changes could not be saved.'));
    }

    public function destroy(int $id)
    {
        $property = $this->properties->find($id);

        collect(explode(',', $property['images'] ?? ''))
            ->merge(explode(',', $property['floor_plans'] ?? ''))
            ->map(fn ($v) => trim($v))
            ->filter()
            ->each(fn ($path) => $this->deleteImageIfStored($path));

        $this->properties->delete($id);

        return redirect()
            ->route('properties.index')
            ->with('success', __('Property deleted successfully.'));
    }

    /** Specs arrive as specs[<key>][label]/[label_ar]/[value]/[value_ar] from <x-nakheel.repeater mode="fields">; stored as a JSON array. */
    private function encodeSpecs(Request $request): string
    {
        $rows = collect($request->input('specs', []))
            ->map(fn ($row) => [
                'label' => trim($row['label'] ?? ''),
                'label_ar' => trim($row['label_ar'] ?? '') ?: null,
                'value' => trim($row['value'] ?? ''),
                'value_ar' => trim($row['value_ar'] ?? '') ?: null,
            ])
            ->filter(fn ($row) => $row['label'] !== '' || $row['value'] !== '' || $row['label_ar'] !== null || $row['value_ar'] !== null)
            ->values();

        return $rows->toJson();
    }

    /** Features arrive as a single JSON string (a hidden input synced by <x-nakheel.repeater mode="tags">'s bilingual chips); re-encoded to drop blank rows. */
    private function encodeFeatures(Request $request): string
    {
        $rows = collect(json_decode($request->input('features', '[]'), true) ?: [])
            ->map(fn ($row) => [
                'value' => trim($row['value'] ?? ''),
                'value_ar' => trim($row['value_ar'] ?? '') ?: null,
            ])
            ->filter(fn ($row) => $row['value'] !== '')
            ->values();

        return $rows->toJson();
    }

    /** Upper bounds for a spec value: counts (Bedrooms, Floors…) vs measurements (Area (m²)…). */
    private const SPEC_MAX_COUNT = 1000;
    private const SPEC_MAX_MEASUREMENT = 1000000;

    /**
     * Specification values are numbers, never free text: a whole number for
     * counts (Bedrooms, Bathrooms, Floors…), or — only when the label names a
     * unit in brackets, e.g. "Area (m²)" / "المساحة (م²)" — a decimal number
     * for that measurement. Labels are real words, not just digits. Every
     * create/update path goes through validated(), so the browser's own
     * checks in <x-nakheel.repeater> are a convenience, never the guard.
     */
    private function validateSpecRow(string $attribute, mixed $row, \Closure $fail): void
    {
        if (! is_array($row)) {
            $fail(__('Invalid specification.'));

            return;
        }

        $label = trim((string) ($row['label'] ?? ''));
        $labelAr = trim((string) ($row['label_ar'] ?? ''));
        $values = array_filter([
            trim((string) ($row['value'] ?? '')),
            trim((string) ($row['value_ar'] ?? '')),
        ], fn ($v) => $v !== '');
        $name = $label !== '' ? $label : ($labelAr !== '' ? $labelAr : __('a specification'));

        if ($label === '' && $labelAr === '' && $values === []) {
            return; // Blank row — dropped by encodeSpecs().
        }

        foreach ([$label, $labelAr] as $text) {
            if ($text !== '' && ! preg_match('/\p{L}/u', $text)) {
                $fail(__('Specification names must be words, not just numbers (":name").', ['name' => $text]));

                return;
            }
        }

        if ($label === '' && $labelAr === '') {
            $fail(__('Every specification value needs a name (e.g. Bedrooms).'));

            return;
        }

        if ($values === []) {
            $fail(__('":name" needs a value (e.g. 4).', ['name' => $name]));

            return;
        }

        $isMeasurement = (bool) preg_match('/\(.+\)/u', $label . ' ' . $labelAr);
        foreach ($values as $value) {
            if ($isMeasurement) {
                if (! preg_match('/^\d+(\.\d{1,2})?$/', $value) || (float) $value > self::SPEC_MAX_MEASUREMENT) {
                    $fail(__('":name" must be a number up to :max, with at most 2 decimal places (e.g. 240 or 240.5).', ['name' => $name, 'max' => number_format(self::SPEC_MAX_MEASUREMENT)]));

                    return;
                }
            } elseif (! preg_match('/^\d+$/', $value) || (int) $value > self::SPEC_MAX_COUNT) {
                $fail(__('":name" must be a whole number from 0 to :max (e.g. 4). For a measurement with decimals, put its unit in the name, e.g. "Area (m²)".', ['name' => $name, 'max' => number_format(self::SPEC_MAX_COUNT)]));

                return;
            }
        }
    }

    /**
     * Property type (English and Arabic): a category name made of letters,
     * e.g. "Villa", "Summer House", "شقة". There's no fixed list of types
     * in the app — admins name them freely, and a property may list several
     * separated by commas or slashes ("Villas, Apartments"), which the cards
     * split on — so each listed type must be a real name: letters (any
     * script) with single spaces, hyphens or apostrophes between words.
     * Digits and symbols are rejected ("1f12f", "123", "@@@").
     */
    private function validatePropertyType(string $attribute, mixed $value, \Closure $fail): void
    {
        $types = preg_split('/\s*[,،\/]\s*/u', trim((string) $value));
        $word = '[\p{L}\p{M}]+';

        foreach ($types as $type) {
            if (! preg_match("/^{$word}(?:[ '’-]{$word})*$/u", $type)) {
                $fail(__('Property type must be a name made of letters, e.g. Villa or Apartment (no numbers or symbols). Separate several types with commas.'));

                return;
            }
        }
    }

    /**
     * Features (the hidden JSON list from <x-nakheel.repeater mode="tags">):
     * descriptive names, so no digits anywhere in either language — any
     * Unicode digit, Western or Arabic-keyboard ("123", "Feature123",
     * "1f12f", "حديقة ٢"). Letters in any language, spaces and normal
     * punctuation stay allowed. Blank rows are still simply dropped by
     * encodeFeatures(), as before. A payload that isn't a valid list is
     * rejected outright rather than being treated as "no features", so a
     * malformed request can't wipe a property's saved features.
     */
    private function validateFeatures(string $attribute, mixed $value, \Closure $fail): void
    {
        $rows = json_decode((string) $value, true);
        if (! is_array($rows) || ! array_is_list($rows)) {
            $fail(__('Invalid features.'));

            return;
        }

        foreach ($rows as $row) {
            if (! is_array($row) || ! array_key_exists('value', $row)) {
                $fail(__('Invalid features.'));

                return;
            }

            foreach (['value', 'value_ar'] as $key) {
                $text = $row[$key] ?? null;
                if ($text !== null && ! is_string($text)) {
                    $fail(__('Invalid features.'));

                    return;
                }
                if ($text !== null && mb_strlen(trim($text)) > 255) {
                    $fail(__('Feature names may not be longer than 255 characters.'));

                    return;
                }
                if ($text !== null && preg_match('/\p{Nd}/u', $text)) {
                    $fail(__('Feature names can\'t contain numbers (":name").', ['name' => trim($text)]));

                    return;
                }
            }
        }
    }

    /**
     * Arabic-keyboard digits (٤, ۴) and the Arabic decimal separator (٫) are
     * the same numbers written differently — normalised to 0-9 and "." so an
     * admin typing in Arabic isn't rejected. Nothing else is altered: "four",
     * "3abc", "4.5" (for a count) still reach validation as typed and fail.
     */
    private function normalizeSpecDigits(Request $request): void
    {
        $specs = $request->input('specs');
        if (! is_array($specs)) {
            return;
        }

        $map = ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٫' => '.'];

        foreach ($specs as $key => $row) {
            if (! is_array($row)) {
                continue;
            }
            foreach (['value', 'value_ar'] as $field) {
                if (isset($row[$field]) && is_string($row[$field])) {
                    $specs[$key][$field] = strtr($row[$field], $map);
                }
            }
        }

        $request->merge(['specs' => $specs]);
    }

    private function validated(Request $request): array
    {
        $this->normalizeSpecDigits($request);

        $data = $request->validate([
            'nakheel_project_id' => ['nullable', 'integer', 'exists:nakheel_projects,id'],
            'title' => ['required_without:title_ar', 'nullable', 'string', 'max:255'],
            'title_ar' => ['required_without:title', 'nullable', 'string', 'max:255'],
            'type' => ['required_without:type_ar', 'nullable', 'string', 'max:255', fn (string $a, mixed $v, \Closure $fail) => $this->validatePropertyType($a, $v, $fail)],
            'type_ar' => ['required_without:type', 'nullable', 'string', 'max:255', fn (string $a, mixed $v, \Closure $fail) => $this->validatePropertyType($a, $v, $fail)],
            'status' => ['required', 'string', Rule::in($this->statuses->names())],
            'price' => ['required', 'integer', 'min:0'],
            'location' => ['required_without:location_ar', 'nullable', 'string', 'max:255'],
            'location_ar' => ['required_without:location', 'nullable', 'string', 'max:255'],
            'specs' => ['nullable', 'array'],
            'specs.*' => [fn (string $attribute, mixed $value, \Closure $fail) => $this->validateSpecRow($attribute, $value, $fail)],
            'specs.*.label' => ['nullable', 'string', 'max:255'],
            'specs.*.label_ar' => ['nullable', 'string', 'max:255'],
            'specs.*.value' => ['nullable', 'string', 'max:255'],
            'specs.*.value_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'features' => ['nullable', 'string', fn (string $a, mixed $v, \Closure $fail) => $this->validateFeatures($a, $v, $fail)],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images_keep' => ['nullable', 'array'],
            'images_keep.*' => ['string'],
            'floor_plans' => ['nullable', 'array'],
            'floor_plans.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'floor_plans_keep' => ['nullable', 'array'],
            'floor_plans_keep.*' => ['string'],
        ], $this->bilingualRequiredMessages([
            'title' => 'title_ar',
            'type' => 'type_ar',
            'location' => 'location_ar',
        ]));

        $data['featured_on_login'] = $request->boolean('featured_on_login');

        return $data;
    }
}
