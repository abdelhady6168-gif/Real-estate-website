@extends('layouts.admin')

@section('title', 'New Timeline Entry')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('New Timeline Entry') }}</h1>

    @include('admin.timeline._form', ['action' => route('admin.timeline.store'), 'method' => 'POST', 'entry' => null])
@endsection
