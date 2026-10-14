<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Optimistic locking for admin edit forms, so two admins editing the same
 * record can't silently overwrite each other.
 *
 * The edit form carries a fingerprint ("edit_version") of the record as it
 * was when the form was opened. On save, the record is locked, fingerprinted
 * again, and only written if nothing changed in between — otherwise the save
 * is refused and the admin is told, instead of the later save quietly
 * reverting the earlier one.
 *
 * The fingerprint covers only what the form itself edits — not timestamps
 * (two saves in the same second would share one) and not the photo fields
 * that upload live via AJAX on the same page (an admin's own upload must
 * not make their own save look like someone else's change).
 */
trait GuardsConcurrentEdits
{
    /** Fingerprint of a snapshot of the editable fields (see each controller's snapshot method). */
    protected function editVersion(?array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Runs $write only if the record still matches the version the form was
     * opened with — checked and written as one locked step, so nothing can
     * slip in between the check and the write.
     *
     * Returns 'saved', 'changed' (someone else saved first — nothing
     * written), or 'deleted' (the record no longer exists).
     */
    protected function saveUnlessChanged(string $table, int $id, ?string $submittedVersion, \Closure $snapshot, \Closure $write): string
    {
        return DB::transaction(function () use ($table, $id, $submittedVersion, $snapshot, $write) {
            if (! DB::table($table)->where('id', $id)->lockForUpdate()->exists()) {
                return 'deleted';
            }

            if (! is_string($submittedVersion) || ! hash_equals($this->editVersion($snapshot()), $submittedVersion)) {
                return 'changed';
            }

            $write();

            return 'saved';
        }, 5);
    }
}
