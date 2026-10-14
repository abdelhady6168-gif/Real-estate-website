@extends('layouts.admin')

@section('title', 'New Category')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('New Category') }}</h1>

    @include('admin.categories._form', ['action' => route('admin.categories.store'), 'method' => 'POST', 'category' => null])
@endsection
