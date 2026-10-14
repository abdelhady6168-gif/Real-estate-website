@extends('layouts.admin')

@section('title', 'Edit ' . $category['title'])

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :title', ['title' => $category['title']]) }}</h1>

    @include('admin.categories._form', ['action' => route('admin.categories.update', $category['id']), 'method' => 'PUT'])
@endsection
