<?php

namespace App\Http\Controllers\Concerns;

/**
 * Shared validation message for bilingual (English/Arabic) field pairs that
 * are "required" — meaning at least one of the two languages must be
 * filled in, not that English specifically is mandatory. Each such pair
 * uses matching `required_without:<other>` rules (see any `validated()`
 * method using this trait for the pattern), and this just keeps the
 * message text in one place instead of repeating it per field per
 * controller.
 */
trait ValidatesBilingualFields
{
    private function bilingualRequiredMessage(): string
    {
        return __('Fill in this field in English or Arabic — at least one is required.');
    }

    /** Builds the `.required_without` message-array entries for a list of bilingual pairs, e.g. ['title' => 'title_ar', 'type' => 'type_ar']. */
    private function bilingualRequiredMessages(array $pairs): array
    {
        $messages = [];

        foreach ($pairs as $en => $ar) {
            $messages["{$en}.required_without"] = $this->bilingualRequiredMessage();
            $messages["{$ar}.required_without"] = $this->bilingualRequiredMessage();
        }

        return $messages;
    }
}
