@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kelola Role" />

    @if (session('status'))
        <div class="mb-5"><x-ui.alert variant="success" :message="session('status')" /></div>
    @endif
    @if ($errors->any())
        <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
    @endif

    <x-common.component-card title="Daftar Role">
        <div class="flex justify-end">
            <a href="{{ route('roles.create') }}">
                <x-ui.button size="sm">Tambah Role</x-ui.button>
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[700px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nama Role</p>
                            </th>
                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jumlah User</p>
                            </th>
                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Akses Menu</p>
                            </th>
                            <th class="px-5 py-3 text-left sm:px-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($roles as $role)
                            <tr>
                                <td class="px-5 py-4 sm:px-6">
                                    <p class="text-gray-700 text-theme-sm dark:text-gray-300">{{ $role->name }}</p>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $role->users_count }}</p>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    @if ($role->name === 'Admin')
                                        <x-ui.badge color="primary" size="sm">Akses Penuh</x-ui.badge>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($role->permissions as $permission)
                                                <x-ui.badge color="light" size="sm">{{ \App\Helpers\MenuHelper::permissionLabels()[$permission->name] ?? $permission->name }}</x-ui.badge>
                                            @empty
                                                <span class="text-gray-400 text-theme-xs">Belum ada akses menu</span>
                                            @endforelse
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    @if ($role->name !== 'Admin')
                                        <div class="flex justify-end relative">
                                            <x-common.table-dropdown>
                                                <x-slot name="button">
                                                    <button type="button" class="text-gray-500 dark:text-gray-400">
                                                        <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z" fill="currentColor" /></svg>
                                                    </button>
                                                </x-slot>
                                                <x-slot name="content">
                                                    <a href="{{ route('roles.edit', $role) }}" class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">Ubah</a>
                                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex w-full px-3 py-2 font-medium text-left text-error-500 rounded-lg text-theme-xs hover:bg-gray-100 dark:hover:bg-white/5">Hapus</button>
                                                    </form>
                                                </x-slot>
                                            </x-common.table-dropdown>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Belum ada role.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-common.component-card>
@endsection
