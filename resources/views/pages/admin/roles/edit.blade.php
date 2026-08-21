@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Ubah Role" />

    <x-common.component-card title="Form Role">
        <form method="POST" action="{{ route('roles.update', $role) }}" class="max-w-md space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Role</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Akses Menu</label>
                <div class="space-y-4 rounded-lg border border-gray-300 p-4 dark:border-gray-700">
                    @foreach ($permissionGroups as $group => $permissions)
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ $group }}</p>
                            <div class="space-y-2">
                                @foreach ($permissions as $slug => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="permissions[]" value="{{ $slug }}"
                                            @checked(in_array($slug, old('permissions', $currentPermissions)))
                                            class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900" />
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <a href="{{ route('roles.index') }}"><x-ui.button variant="outline" type="button">Batal</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
@endsection
