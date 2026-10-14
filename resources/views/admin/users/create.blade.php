@extends('layouts.admin')

@section('title', 'Create Sub Admin')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Create Sub Admin') }}</h1>

    @include('admin.users._form', ['action' => route('admin.users.store'), 'method' => 'POST', 'admin' => null])
@endsection
