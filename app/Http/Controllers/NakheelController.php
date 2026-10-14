<?php

namespace App\Http\Controllers;

use App\Services\Concerns\ResolvesTranslations;
use App\Services\NakheelCategoryService;
use App\Services\NakheelPageContentService;
use App\Services\NakheelProjectHighlightService;
use App\Services\NakheelProjectService;
use App\Services\NakheelPropertyService;
use App\Services\NakheelPropertyStatusService;
use App\Services\NakheelSettingService;
use App\Services\NakheelStatService;
use App\Services\NakheelTimelineEntryService;
use App\Services\NakheelUpcomingProjectService;
use App\Services\NakheelWhyInvestService;
use Illuminate\Http\Request;

/**
 * Serves the "Nakheel Properties" site (Properties, Projects, About Us,
 * Future Projects). Inherits the base controller's `auth` middleware, so
 * every page here requires sign-in — a guest opening any link (e.g. a
 * shared /properties or /projects/... URL) lands on the Login page first,
 * and is sent on to the page they asked for after signing in.
 *
 * Every content block on these pages is database-backed and editable from
 * /admin.
 */
class NakheelController extends Controller
{
    use ResolvesTranslations;

    public function __construct(
        private NakheelPropertyService $properties,
        private NakheelProjectService $projects,
        private NakheelProjectHighlightService $highlights,
        private NakheelPropertyStatusService $statuses,
        private NakheelStatService $stats,
        private NakheelTimelineEntryService $timelineEntries,
        private NakheelCategoryService $categories,
        private NakheelUpcomingProjectService $upcomingProjects,
        private NakheelWhyInvestService $whyInvest,
        private NakheelPageContentService $pageContent,
        private NakheelSettingService $settings,
    ) {}

    /** Admins always see Sold properties; normal users only when the global setting is on. */
    private function includeSold(): bool
    {
        return (bool) auth()->user()?->is_admin || $this->settings->get('show_sold_properties');
    }

    public function properties(Request $request)
    {
        $includeSold = $this->includeSold();
        $all = collect($this->properties->allForDisplay($includeSold));

        $filtered = $all
            ->when($request->filled('type') && $request->get('type') !== 'All Types', fn ($q) => $q->where('type', $request->get('type')))
            ->when($request->filled('project') && $request->get('project') !== 'All Projects', fn ($q) => $q->where('project', $request->get('project')))
            ->when($request->filled('location') && $request->get('location') !== 'All Locations', fn ($q) => $q->where('location', $request->get('location')))
            ->when($request->filled('availability') && $request->get('availability') !== 'All', fn ($q) => $q->where('status', $request->get('availability')))
            ->when($request->filled('min'), fn ($q) => $q->where('price', '>=', (int) $request->get('min') * 1000))
            ->when($request->filled('max'), fn ($q) => $q->where('price', '<=', (int) $request->get('max') * 1000));

        $sort = $request->get('sort', 'newest');
        $filtered = match ($sort) {
            'price_asc' => $filtered->sortBy('price')->values(),
            'price_desc' => $filtered->sortByDesc('price')->values(),
            default => $filtered->values(),
        };

        $soldNames = $this->statuses->soldNames();
        $visibleStatusNames = $includeSold
            ? $this->statuses->names()
            : collect($this->statuses->names())->reject(fn ($name) => in_array($name, $soldNames, true))->values()->all();

        return view('nakheel.properties', [
            'properties' => $filtered,
            'types' => $all->pluck('type')->unique()->values(),
            'projects' => $all->pluck('project')->unique()->values(),
            'locations' => $all->pluck('location')->unique()->values(),
            'statuses' => $visibleStatusNames,
            'sort' => $sort,
            // Filter <option value=""> stays the canonical English value (so
            // filtering/bookmarked URLs keep working); these maps translate
            // only the visible option text.
            'typeLabels' => $all->pluck('typeLabel', 'type')->all(),
            'locationLabels' => $all->pluck('locationLabel', 'location')->all(),
            'projectLabels' => $all->pluck('projectLabel', 'project')->all(),
            'statusLabels' => $this->statuses->namesWithLabels(),
            'showSoldProperties' => $this->settings->get('show_sold_properties'),
            'canManageSoldVisibility' => (bool) auth()->user()?->hasPermission('properties', 'edit'),
        ]);
    }

    public function projectShow(?string $slug = null)
    {
        $project = $slug
            ? $this->projects->findBySlugForDisplay($slug)
            : $this->projects->firstForDisplay();

        abort_unless($project, 404);

        $project['highlights'] = collect($this->highlights->forProject($project['id']))
            ->map(fn ($highlight) => ['label' => $this->tr($highlight['label'], $highlight['label_ar'] ?? null), 'image' => $highlight['image']])
            ->all();

        return view('nakheel.project-show', [
            'project' => $project,
            'related' => $this->properties->forProject($project['id'], $this->includeSold()),
            ...$this->adjacentProjectSlugs($project['slug']),
        ]);
    }

    public function about()
    {
        return view('nakheel.about', [
            'stats' => $this->stats->all(),
            'timeline' => $this->timelineEntries->all(),
            'categories' => $this->categories->all(),
            'hero' => $this->pageContent->findForDisplay('about_hero'),
            'story' => $this->pageContent->findForDisplay('about_story'),
            'storyParagraphs' => $this->pageContent->paragraphs('about_story'),
            'futureVision' => $this->pageContent->findForDisplay('future_vision'),
            'futureVisionParagraphs' => $this->pageContent->paragraphs('future_vision'),
        ]);
    }

    public function futureProjects()
    {
        return view('nakheel.future-projects', [
            'upcoming' => $this->upcomingProjects->all(),
            'reasons' => $this->whyInvest->all(),
            'hero' => $this->pageContent->findForDisplay('future_projects_hero'),
            'officeContact' => $this->pageContent->findForDisplay('office_contact'),
        ]);
    }

    /** Previous/next project slugs (by name order, wrapping around) for the hero carousel's "different project" navigation. */
    private function adjacentProjectSlugs(string $currentSlug): array
    {
        $slugs = collect($this->projects->all())->pluck('slug')->values();
        $count = $slugs->count();

        if ($count < 2) {
            return ['previousProjectSlug' => null, 'nextProjectSlug' => null];
        }

        $index = $slugs->search($currentSlug);

        return [
            'previousProjectSlug' => $slugs[($index - 1 + $count) % $count],
            'nextProjectSlug' => $slugs[($index + 1) % $count],
        ];
    }
}
