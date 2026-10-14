@extends('layouts.admin')

@section('title', 'Edit ' . $stat['label'])

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :label', ['label' => $stat['label']]) }}</h1>

    @include('admin.stats._form', ['action' => route('admin.stats.update', $stat['id']), 'method' => 'PUT'])
@endsection
