@extends('layouts.admin')

@section('title', 'New Status')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('New Status') }}</h1>

    @include('admin.statuses._form', ['action' => route('admin.statuses.store'), 'method' => 'POST', 'status' => null])
@endsection
