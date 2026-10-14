@extends('layouts.admin')

@section('title', 'New Project')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('New Project') }}</h1>

    @include('admin.projects._form', ['action' => route('admin.projects.store'), 'method' => 'POST'])
@endsection
