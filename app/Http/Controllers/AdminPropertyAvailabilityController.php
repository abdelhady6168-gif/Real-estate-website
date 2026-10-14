<?php

namespace App\Http\Controllers;

use App\Services\NakheelPropertyAvailabilityService;
use App\Services\NakheelPropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * AJAX CRUD for one property's availability date ranges, called from
 * <x-nakheel.availability-manager> on the property edit form (see
 * resources/js/availability.js) — same "no full page save" pattern as
 * AdminImageController.
 *
 * Errors are returned as JSON explicitly (rather than via $request->validate()/
 * abort(), which redirect/render HTML here) because this app's exception
 * handler only auto-renders JSON for `api/*` routes — see
 * shouldRenderJsonWhen() in bootstrap/app.php. These endpoints are called
 * via fetch() from an admin form, so they need a JSON body on failure too.
 */
class AdminPropertyAvailabilityController extends Controller
{
    public function __construct(
        private NakheelPropertyAvailabilityService $availability,
        private NakheelPropertyService $properties,
    ) {}

    public function store(Request $request, int $property)
    {
        if (!$this->properties->find($property)) {
            return $this->jsonError(404, __('Property not found.'));
        }

        $error = $this->availability->exclusivelyForProperty($property, function () use ($request, $property) {
            $data = $this->validated($request, $property);
            if ($data instanceof JsonResponse) {
                return $data;
            }

            $this->availability->create($property, $data);

            return null;
        });

        if ($error) {
            return $error;
        }

        return response()->json($this->latest($property), 201);
    }

    public function update(Request $request, int $property, int $range)
    {
        $existing = $this->availability->find($range);
        if (!$existing || (int) $existing['nakheel_property_id'] !== $property) {
            return $this->jsonError(404, __('Availability range not found.'));
        }

        $error = $this->availability->exclusivelyForProperty($property, function () use ($request, $property, $range) {
            $data = $this->validated($request, $property, $range);
            if ($data instanceof JsonResponse) {
                return $data;
            }

            $this->availability->update($range, $data);

            return null;
        });

        if ($error) {
            return $error;
        }

        return response()->json($this->latest($property));
    }

    public function destroy(int $property, int $range)
    {
        $existing = $this->availability->find($range);
        if (!$existing || (int) $existing['nakheel_property_id'] !== $property) {
            return $this->jsonError(404, __('Availability range not found.'));
        }

        $this->availability->delete($range);

        return response()->noContent();
    }

    /** @return array|JsonResponse Validated data, or a JSON error response to return as-is. */
    private function validated(Request $request, int $property, ?int $exceptId = null)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        if ($validator->fails()) {
            return $this->jsonError(422, $validator->errors()->first(), $validator->errors()->toArray());
        }

        $data = $validator->validated();

        if ($this->availability->overlaps($property, $data['start_date'], $data['end_date'], $exceptId)) {
            return $this->jsonError(422, __('This date range overlaps an existing one for this property.'));
        }

        return $data;
    }

    private function jsonError(int $status, string $message, array $errors = []): JsonResponse
    {
        return response()->json(['message' => $message, 'errors' => $errors], $status);
    }

    /** The freshly-saved list, shaped for the JS to re-render the manager without a page reload. */
    private function latest(int $property): array
    {
        return ['ranges' => $this->availability->forProperty($property)];
    }
}
