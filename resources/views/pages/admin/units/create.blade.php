@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Unit" />

    <x-common.component-card title="Form Unit">
        <form method="POST" action="{{ route('units.store') }}" class="max-w-md space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Unit</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div class="flex gap-3">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <a href="{{ route('units.index') }}"><x-ui.button variant="outline" type="button">Batal</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
@endsection
