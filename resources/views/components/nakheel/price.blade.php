{{--
    Sitewide currency formatting — Libyan Dinar. One place to change if the
    format/currency ever changes again instead of three hardcoded `$` spots.
    Renders bare text (no wrapping element) so it drops straight into
    whatever <span>/<p> the caller already has.
--}}
@props(['amount'])

@php
    $formatted = number_format((float) $amount);
    $currency = app()->getLocale() === 'ar' ? 'ل.د.' : 'L.D.';
@endphp
{{ $formatted }} {{ $currency }}