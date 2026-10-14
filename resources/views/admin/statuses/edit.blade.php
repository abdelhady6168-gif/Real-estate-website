@extends('layouts.admin')

@section('title', 'Edit ' . $status['name'])

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :name', ['name' => $status['name']]) }}</h1>

    @include('admin.statuses._form', ['action' => route('admin.statuses.update', $status['id']), 'method' => 'PUT'])
@endsection
