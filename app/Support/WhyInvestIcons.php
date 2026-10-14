<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Icon choices for "Why Invest in Nakheel" reasons. Two layers, by design:
 *
 *  - `all()` is the full TRUSTED catalog — every key/label/SVG path here is
 *    hardcoded in this file (never accepted from a request), either lifted
 *    verbatim from an icon already used elsewhere on the site or hand-built
 *    in the same 20x20 solid-fill style. This is the only place icon SVG
 *    markup is ever defined, which is what makes it safe: an admin can only
 *    ever select a KEY from this list, never supply markup of their own.
 *
 *  - `available()` (backed by the `nakheel_why_invest_icons` table) is the
 *    subset of that catalog the Main Admin has chosen to expose in the
 *    Why-Invest icon picker. "Add Icon" inserts a row referencing a catalog
 *    key here; it never stores new markup. Removing a key from this table
 *    only hides it from the picker for *future* selections — icon()/paths()
 *    still resolve from the full catalog, so existing reasons can never end
 *    up with a broken icon (see WhyInvestIconController::destroy(), which
 *    additionally blocks removal while a reason is still using the icon).
 *
 * `default` is what renders when a reason has no icon chosen — the same
 * checkmark already used as the "no image set" fallback on the project
 * highlights section.
 */
class WhyInvestIcons
{
    public static function all(): array
    {
        return [
            'location' => [
                'label' => 'Location',
                'paths' => [
                    '<path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" />',
                ],
            ],
            'security' => [
                'label' => 'Security',
                'paths' => [
                    '<path d="M10 1.5 16 4.5V10c0 5.2-2.9 7.9-6 8.5C6.9 17.9 4 15.2 4 10V4.5L10 1.5Z" />',
                ],
            ],
            'investment' => [
                'label' => 'Investment / Value',
                'paths' => [
                    '<path d="M12.577 4.878a.75.75 0 0 1 .919-.53l4.78 1.281a.75.75 0 0 1 .531.919l-1.281 4.78a.75.75 0 1 1-1.449-.387l.81-3.022a19.407 19.407 0 0 0-5.594 5.203.75.75 0 0 1-1.139.093L7.5 10.06l-4.72 4.72a.75.75 0 0 1-1.06-1.061l5.25-5.25a.75.75 0 0 1 1.06 0l3.074 3.073a20.923 20.923 0 0 1 5.545-4.931l-3.042-.815a.75.75 0 0 1-.53-.919Z" />',
                ],
            ],
            'quality' => [
                'label' => 'Quality',
                'paths' => [
                    '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 0 0-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.538-1.118l1.286-3.957a1 1 0 0 0-.363-1.118l-3.367-2.447c-.784-.57-.38-1.81.588-1.81h4.162a1 1 0 0 0 .95-.69l1.286-3.958Z" />',
                ],
            ],
            'buildings' => [
                'label' => 'Buildings / Properties',
                'paths' => [
                    '<path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 0 1 0-1.5h12.5a.75.75 0 0 1 0 1.5H16v13h.25a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75v-2.5a2 2 0 0 0-4 0v2.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5H4Zm2-11.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1ZM6 8.5A.5.5 0 0 1 6.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm5.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z" clip-rule="evenodd" />',
                ],
            ],
            'construction' => [
                'label' => 'Construction',
                'paths' => [
                    '<rect x="2.5" y="12.5" width="6.5" height="4.5" rx="0.75" />',
                    '<rect x="11" y="12.5" width="6.5" height="4.5" rx="0.75" />',
                    '<rect x="6.75" y="7" width="6.5" height="4.5" rx="0.75" />',
                ],
            ],
            'community' => [
                'label' => 'Community',
                'paths' => [
                    '<path d="m10 3.22-.61-.6a5.5 5.5 0 0 0-7.78 7.77L10 18.78l8.39-8.4a5.5 5.5 0 0 0-7.78-7.77l-.61.61Z" />',
                ],
            ],
            'lifestyle' => [
                'label' => 'Lifestyle',
                'paths' => [
                    '<path d="M10 2 17 9.5 3 9.5Z" />',
                    '<rect x="5" y="9" width="10" height="8.5" rx="0.5" />',
                ],
            ],
            'growth' => [
                'label' => 'Growth',
                'paths' => [
                    '<path d="M15.5 2A1.5 1.5 0 0 0 14 3.5v13a1.5 1.5 0 0 0 1.5 1.5h1a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 16.5 2h-1ZM9.5 6A1.5 1.5 0 0 0 8 7.5v9A1.5 1.5 0 0 0 9.5 18h1a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 10.5 6h-1ZM3.5 10A1.5 1.5 0 0 0 2 11.5v5A1.5 1.5 0 0 0 3.5 18h1A1.5 1.5 0 0 0 6 16.5v-5A1.5 1.5 0 0 0 4.5 10h-1Z" />',
                ],
            ],
            'reliability' => [
                'label' => 'Reliability',
                'paths' => [
                    '<path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" />',
                ],
            ],
            'star' => [
                'label' => 'Rating / Excellence',
                'paths' => [
                    '<path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.45 1.405 1.008L10 15.591l4.069 2.473c.713.442 1.598-.195 1.405-1.008l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.651l-4.753-.381-1.83-4.401Z" />',
                ],
            ],
            'clock' => [
                'label' => 'Timely Delivery',
                'paths' => [
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />',
                ],
            ],
            'phone' => [
                'label' => 'Support',
                'paths' => [
                    '<path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-.826 1.68l-1.293.646a11.037 11.037 0 0 0 6.586 6.586l.646-1.293a1.5 1.5 0 0 1 1.68-.826l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd" />',
                ],
            ],
            'envelope' => [
                'label' => 'Communication',
                'paths' => [
                    '<path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z" />',
                    '<path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z" />',
                ],
            ],
            'key' => [
                'label' => 'Access / Ownership',
                'paths' => [
                    '<circle cx="5.75" cy="10" r="3.5" />',
                    '<rect x="8.75" y="9.25" width="8" height="1.5" rx="0.75" />',
                    '<rect x="14" y="10.75" width="1.5" height="2.5" rx="0.4" />',
                    '<rect x="16" y="10.75" width="1.5" height="1.8" rx="0.4" />',
                ],
            ],
            'calendar' => [
                'label' => 'Scheduling',
                'paths' => [
                    '<rect x="3" y="4.5" width="14" height="12" rx="1.5" />',
                    '<rect x="5.75" y="2.25" width="1.5" height="3.5" rx="0.75" />',
                    '<rect x="12.75" y="2.25" width="1.5" height="3.5" rx="0.75" />',
                    '<rect x="3" y="7.5" width="14" height="1.75" opacity="0.45" />',
                ],
            ],
            'value' => [
                'label' => 'Returns',
                'paths' => [
                    '<circle cx="7" cy="13" r="4.5" />',
                    '<circle cx="13" cy="7" r="4.5" opacity="0.55" />',
                ],
            ],
            'flag' => [
                'label' => 'Milestone',
                'paths' => [
                    '<rect x="4" y="2" width="1.25" height="16" rx="0.6" />',
                    '<path d="M5.25 3h9.5c.9 0 1.35 1.08.72 1.72L12.5 7.5l2.97 2.78c.63.64.18 1.72-.72 1.72h-9.5V3Z" />',
                ],
            ],
            'target' => [
                'label' => 'Precision',
                'paths' => [
                    '<path fill-rule="evenodd" d="M10 17.25a7.25 7.25 0 1 0 0-14.5 7.25 7.25 0 0 0 0 14.5Zm0-1.5a5.75 5.75 0 1 1 0-11.5 5.75 5.75 0 0 1 0 11.5Z" clip-rule="evenodd" />',
                    '<path fill-rule="evenodd" d="M10 13.25a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Zm0-1.5a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z" clip-rule="evenodd" />',
                    '<circle cx="10" cy="10" r="1" />',
                ],
            ],
            'leaf' => [
                'label' => 'Sustainability',
                'paths' => [
                    '<path d="M4 16c6-1 10-6 12-12-7 1-12 6-12 12Z" />',
                ],
            ],
            'team' => [
                'label' => 'Partnership',
                'paths' => [
                    '<circle cx="7" cy="7" r="2.75" />',
                    '<path d="M1.5 16.5c0-3.2 2.46-5.5 5.5-5.5s5.5 2.3 5.5 5.5v.5h-11v-.5Z" />',
                    '<circle cx="14.25" cy="8" r="2.25" opacity="0.6" />',
                    '<path d="M11.75 16.5c0-2.19.83-4.02 2.14-5.16A4.2 4.2 0 0 1 14.5 11.25c2.9 0 4.75 2.4 4.75 5.25v.5h-7.5v-.5Z" opacity="0.6" />',
                ],
            ],
        ];
    }

    /** @return array<int, string> raw <path>/<rect>/<circle> markup for one icon key, falling back to the default checkmark. */
    public static function paths(?string $key): array
    {
        return self::all()[$key]['paths'] ?? self::all()['reliability']['paths'];
    }

    /**
     * The subset of the trusted catalog the Main Admin has enabled for use
     * in the Why-Invest icon picker, in catalog order.
     *
     * @return array<string, array{label: string, paths: array<int, string>}>
     */
    public static function available(): array
    {
        $catalog = self::all();
        $enabledKeys = DB::table('nakheel_why_invest_icons')->pluck('key')->all();

        return collect($catalog)
            ->only($enabledKeys)
            ->all();
    }

    public static function isAvailable(string $key): bool
    {
        return DB::table('nakheel_why_invest_icons')->where('key', $key)->exists();
    }

    /** Adds a catalog key to the available library. Silently ignored if the key isn't a real catalog entry or is already available. */
    public static function addAvailable(string $key): void
    {
        if (! array_key_exists($key, self::all())) {
            return;
        }

        DB::table('nakheel_why_invest_icons')->insertOrIgnore([
            'key' => $key,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function removeAvailable(string $key): void
    {
        DB::table('nakheel_why_invest_icons')->where('key', $key)->delete();
    }

    /** Whether any existing Why Invest reason currently uses this icon key. */
    public static function isInUse(string $key): bool
    {
        return DB::table('nakheel_why_invest_items')->where('icon', $key)->exists();
    }

    /** How many existing Why Invest reasons use each icon key — for the management UI. */
    public static function usageCounts(): array
    {
        return DB::table('nakheel_why_invest_items')
            ->whereNotNull('icon')
            ->select('icon', DB::raw('count(*) as total'))
            ->groupBy('icon')
            ->pluck('total', 'icon')
            ->all();
    }
}
