@extends('layouts.admin')

@section('title', 'Edit ' . $entry['title'])

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :title', ['title' => $entry['title']]) }}</h1>

    @include('admin.timeline._form', ['action' => route('admin.timeline.update', $entry['id']), 'method' => 'PUT'])
@endsection
