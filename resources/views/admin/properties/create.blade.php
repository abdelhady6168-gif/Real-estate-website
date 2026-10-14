@extends('layouts.admin')

@section('title', 'New Property')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('New Property') }}</h1>

    @include('admin.properties._form', ['action' => route('admin.properties.store'), 'method' => 'POST', 'property' => null])
@endsection
