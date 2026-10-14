{{--
    Shared form for admin/users/create.blade.php and admin/users/edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $admin (User|null — null on create).
    Email/password only collected on create; editing an existing Sub Admin
    only touches their permissions here (password reset is its own page —
    see admin/users/password.blade.php).
--}}
@php
    $actionLabels = [
        'view' => __('View'),
        'create' => __('Create'),
        'edit' => __('Edit'),
        'delete' => __('Delete'),
    ];
    $granted = fn (string $section, string $action) => $admin && in_array($action, $admin->permissions[$section] ?? [], true);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @unless ($admin)
        <div class="border border-border rounded-lg bg-surface p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                </div>
                @include('admin.users._password-fields', ['passwordLabel' => 'Password', 'confirmLabel' => 'Confirm Password'])
            </div>
        </div>
    @else
        <div class="border border-border rounded-lg bg-surface p-6">
            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('SUB ADMIN') }}</p>
            <p class="font-serif text-lg font-semibold text-ink">{{ $admin->email }}</p>
        </div>
    @endunless

    <div class="border border-border rounded-lg bg-surface p-6">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('PERMISSIONS / LICENSES') }}</p>
        <div class="divide-y divide-border">
            @foreach (\App\Support\AdminPermissions::sections() as $key => $definition)
                <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                    <p class="text-sm font-semibold text-ink sm:w-48 shrink-0">{{ __($definition['label']) }}</p>
                    <div class="flex flex-wrap gap-x-5 gap-y-2">
                        @foreach ($definition['actions'] as $action)
                            <label class="flex items-center gap-1.5 text-sm text-ink/70">
                                <input type="checkbox" name="permissions[{{ $key }}][]" value="{{ $action }}"
                                       {{ $granted($key, $action) ? 'checked' : '' }}
                                       class="rounded border-border text-gold focus:ring-0 focus:ring-offset-0">
                                {{ $actionLabels[$action] }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
            {{ $admin ? __('SAVE PERMISSIONS') : __('CREATE SUB ADMIN') }}
        </button>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
