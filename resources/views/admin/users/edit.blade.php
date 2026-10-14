@extends('layouts.admin')

@section('title', 'Edit ' . $admin->email)

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :email', ['email' => $admin->email]) }}</h1>

    @include('admin.users._form', ['action' => route('admin.users.update', $admin), 'method' => 'PUT'])
@endsection
