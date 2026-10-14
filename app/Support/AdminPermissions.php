<?php

namespace App\Support;

/**
 * Single source of truth for what a Sub Admin can be granted access to.
 * Every section here maps to real, currently-existing admin routes — see
 * routes/web.php for how each one is wired to `permission:<section>`
 * middleware. To add a new grantable section later: add one entry here,
 * apply the middleware to its route(s), done — no other plumbing changes.
 */
class AdminPermissions
{
    /** @return array<string, array{label: string, actions: array<int, string>}> */
    public static function sections(): array
    {
        return [
            'properties' => ['label' => 'Properties', 'actions' => ['create', 'edit', 'delete']],
            'property_availability' => ['label' => 'Manage Property Availability', 'actions' => ['create', 'edit', 'delete']],
            'projects' => ['label' => 'Projects', 'actions' => ['view', 'create', 'edit', 'delete']],
            'statuses' => ['label' => 'Property Statuses', 'actions' => ['view', 'create', 'edit', 'delete']],
            'future_projects' => ['label' => 'Future Projects', 'actions' => ['create', 'edit', 'delete']],
            'about_us' => ['label' => 'About Us', 'actions' => ['create', 'edit', 'delete']],
            'why_invest' => ['label' => 'Why Invest With Nakheel', 'actions' => ['create', 'edit', 'delete']],
            'why_invest_icons' => ['label' => 'Manage Why Invest Icons', 'actions' => ['view', 'create', 'delete']],
            'contact_info' => ['label' => 'Contact Information', 'actions' => ['edit']],
        ];
    }

    /** Maps a controller method (index/create/store/edit/update/destroy) to the permission action it requires. */
    public static function actionForRouteMethod(string $method): string
    {
        return match ($method) {
            'index' => 'view',
            'create', 'store' => 'create',
            'edit', 'update' => 'edit',
            'destroy' => 'delete',
            default => 'view',
        };
    }

    /** Which section a nakheel_page_content `key` belongs to. */
    public static function sectionForPageContentKey(string $key): ?string
    {
        return match ($key) {
            'about_story', 'about_hero', 'future_vision' => 'about_us',
            'office_contact' => 'contact_info',
            'future_projects_hero' => 'future_projects',
            default => null,
        };
    }

    /** Which section the shared AdminImageController's {resource} param belongs to. */
    public static function sectionForImageResource(string $resource, ?string $id): ?string
    {
        return match ($resource) {
            'properties' => 'properties',
            'projects', 'highlights' => 'projects',
            'why-invest' => 'why_invest',
            'categories', 'timeline' => 'about_us',
            'upcoming' => 'future_projects',
            'page-content' => $id ? self::sectionForPageContentKey($id) : null,
            default => null,
        };
    }
}
