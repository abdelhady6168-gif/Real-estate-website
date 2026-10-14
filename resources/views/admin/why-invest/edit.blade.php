@extends('layouts.admin')

@section('title', 'Edit ' . $item['title'])

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :title', ['title' => $item['title']]) }}</h1>

    @include('admin.why-invest._form', ['action' => route('admin.why-invest.update', $item['id']), 'method' => 'PUT'])
@endsection
