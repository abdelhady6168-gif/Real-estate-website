<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The edit_version an admin edit form carries right now (see
     * GuardsConcurrentEdits) — read off the real edit page, exactly as a
     * browser submitting that form would send it. Call after actingAs().
     */
    protected function editVersion(string $editUrl): string
    {
        preg_match('/name="edit_version" value="([^"]*)"/', $this->get($editUrl)->getContent(), $m);

        return $m[1] ?? '';
    }
}
