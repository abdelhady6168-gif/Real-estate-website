<?php

namespace App\Services\Concerns;

/**
 * Shared English/Arabic fallback for Nakheel content Services. English and
 * Arabic values always live in separate columns — this never merges them,
 * it only picks which one to show for the current request's locale.
 *
 * Admin forms only require one of the two languages to be filled in (see
 * bilingual-field.blade.php / the "required_without" rules that pair each
 * *_ar column with its base column), so a value written in only one
 * language must still show up on BOTH site locales — falling back to
 * whichever language is filled in, not just blanking out the other.
 */
trait ResolvesTranslations
{
    protected function tr(?string $en, ?string $ar): ?string
    {
        if (app()->getLocale() === 'ar') {
            return filled($ar) ? $ar : $en;
        }

        return filled($en) ? $en : $ar;
    }
}
