@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengaturan Tampilan" />

    @if (session('status'))
        <div class="mb-5"><x-ui.alert variant="success" :message="session('status')" /></div>
    @endif
    @if ($errors->any())
        <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-common.component-card title="Identitas Aplikasi">
                <form method="POST" action="{{ route('pengaturan.tampilan.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Aplikasi</label>
                        <input type="text" name="app_name" value="{{ old('app_name', $setting->app_name) }}" placeholder="E-Tiket IT Rumah Sakit"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Logo (mode terang)</label>
                            <div class="mb-3 flex h-16 items-center rounded-lg border border-gray-200 bg-white px-4 dark:border-gray-800">
                                <img src="{{ $setting->logoLightUrl() }}" alt="Logo terang" class="max-h-10 max-w-full object-contain" />
                            </div>
                            <input type="file" name="logo_light" accept="image/svg+xml,image/png,image/jpeg,image/webp"
                                class="w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white dark:text-gray-400" />
                            @if ($setting->logo_light)
                                <button type="submit" form="reset-logo_light" class="mt-2 text-xs text-error-500 hover:underline">Kembalikan ke bawaan</button>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Logo (mode gelap)</label>
                            <div class="mb-3 flex h-16 items-center rounded-lg border border-gray-800 bg-gray-900 px-4">
                                <img src="{{ $setting->logoDarkUrl() }}" alt="Logo gelap" class="max-h-10 max-w-full object-contain" />
                            </div>
                            <input type="file" name="logo_dark" accept="image/svg+xml,image/png,image/jpeg,image/webp"
                                class="w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white dark:text-gray-400" />
                            @if ($setting->logo_dark)
                                <button type="submit" form="reset-logo_dark" class="mt-2 text-xs text-error-500 hover:underline">Kembalikan ke bawaan</button>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ikon (sidebar diciutkan)</label>
                            <div class="mb-3 flex h-16 items-center justify-center rounded-lg border border-gray-200 bg-white px-4 dark:border-gray-800">
                                <img src="{{ $setting->logoIconUrl() }}" alt="Ikon logo" class="max-h-10 max-w-10 object-contain" />
                            </div>
                            <input type="file" name="logo_icon" accept="image/svg+xml,image/png,image/jpeg,image/webp"
                                class="w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white dark:text-gray-400" />
                            @if ($setting->logo_icon)
                                <button type="submit" form="reset-logo_icon" class="mt-2 text-xs text-error-500 hover:underline">Kembalikan ke bawaan</button>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-gray-400">Format SVG, PNG, JPG, atau WEBP. Maksimal 1 MB per file.</p>

                    <x-ui.button type="submit">Simpan Pengaturan</x-ui.button>
                </form>

                @foreach (['logo_light', 'logo_dark', 'logo_icon'] as $field)
                    <form id="reset-{{ $field }}" method="POST" action="{{ route('pengaturan.tampilan.reset') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="field" value="{{ $field }}" />
                    </form>
                @endforeach
            </x-common.component-card>
        </div>

        <div>
            <x-common.component-card title="Pratinjau Sidebar">
                <div class="space-y-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800">
                        <p class="mb-2 text-xs text-gray-400">Sidebar mode terang</p>
                        <img src="{{ $setting->logoLightUrl() }}" alt="Pratinjau logo terang" class="h-10 object-contain" />
                    </div>
                    <div class="rounded-lg border border-gray-800 bg-gray-900 p-4">
                        <p class="mb-2 text-xs text-gray-400">Sidebar mode gelap</p>
                        <img src="{{ $setting->logoDarkUrl() }}" alt="Pratinjau logo gelap" class="h-10 object-contain" />
                    </div>
                    <div class="flex items-center justify-center rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800">
                        <img src="{{ $setting->logoIconUrl() }}" alt="Pratinjau ikon" class="h-10 w-10 object-contain" />
                    </div>
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
