@extends('layouts.admin')

@section('title', 'New Stat')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('New Stat') }}</h1>

    @include('admin.stats._form', ['action' => route('admin.stats.store'), 'method' => 'POST', 'stat' => null])
@endsection
