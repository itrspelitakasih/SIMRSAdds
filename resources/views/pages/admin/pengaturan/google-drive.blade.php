@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengaturan Google Drive" />

    @if (session('status'))
        <div class="mb-5"><x-ui.alert variant="success" :message="session('status')" /></div>
    @endif
    @if (session('error_status'))
        <div class="mb-5"><x-ui.alert variant="error" :message="session('error_status')" /></div>
    @endif
    @if ($errors->any())
        <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
    @endif
    @if ($setting->is_active && ! $setting->isConnected())
        <div class="mb-5"><x-ui.alert variant="warning" title="Belum terhubung ke Google" message="Upload lampiran dokumen ke Google Drive diaktifkan, tapi akun Google belum dihubungkan. Klik 'Hubungkan Akun Google' di bawah." /></div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-common.component-card title="Kredensial Google Drive">
                <div class="mb-5 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-white/5 dark:text-gray-400">
                    <p class="mb-2 font-medium text-gray-700 dark:text-gray-300">Cara menghubungkan:</p>
                    <ol class="list-decimal space-y-1 pl-5">
                        <li>Buat project & OAuth Client di <span class="font-medium">Google Cloud Console</span> (jenis: Web application), lalu aktifkan <span class="font-medium">Google Drive API</span>.</li>
                        <li>Tempel <span class="font-medium">Redirect URI</span> di bawah ke daftar "Authorized redirect URIs" pada OAuth Client tersebut.</li>
                        <li>Isi Client ID & Client Secret dari OAuth Client di form ini, lalu simpan.</li>
                        <li>Klik "Hubungkan Akun Google" pada panel di samping dan setujui aksesnya.</li>
                    </ol>
                </div>

                <form method="POST" action="{{ route('pengaturan.google-drive.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Redirect URI</label>
                        <input type="text" readonly onclick="this.select()" value="{{ $redirectUri }}"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-600 focus:outline-hidden dark:border-gray-700 dark:text-gray-400" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client ID</label>
                            <input type="text" name="client_id" value="{{ old('client_id', $setting->client_id) }}"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client Secret</label>
                            <input type="password" name="client_secret" placeholder="{{ $setting->client_secret ? '••••••••' : '' }}"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Folder ID (opsional)</label>
                            <input type="text" name="folder_id" value="{{ old('folder_id', $setting->folder_id) }}" placeholder="Kosongkan untuk upload ke folder utama Drive"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                            <p class="mt-1 text-xs text-gray-400">Ambil dari URL folder Google Drive: drive.google.com/drive/folders/<span class="font-medium">ID_FOLDER_DI_SINI</span></p>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $setting->is_active)) class="h-4 w-4 rounded border-gray-300" />
                        Aktifkan upload lampiran dokumen ke Google Drive
                    </label>

                    <x-ui.button type="submit">Simpan Pengaturan</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <div>
            <x-common.component-card title="Status Koneksi">
                <div class="space-y-4">
                    <div>
                        @if ($setting->isConnected())
                            <x-ui.badge color="success" variant="light" size="sm">Terhubung</x-ui.badge>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Akun: {{ $setting->connected_email ?: '-' }}</p>
                        @else
                            <x-ui.badge color="light" variant="light" size="sm">Belum Terhubung</x-ui.badge>
                        @endif
                    </div>

                    @if ($setting->client_id && $setting->client_secret)
                        <a href="{{ route('pengaturan.google-drive.connect') }}">
                            <x-ui.button type="button" className="w-full">
                                {{ $setting->isConnected() ? 'Hubungkan Ulang' : 'Hubungkan Akun Google' }}
                            </x-ui.button>
                        </a>
                    @else
                        <p class="text-xs text-gray-400">Isi & simpan Client ID dan Client Secret terlebih dahulu untuk menghubungkan akun.</p>
                    @endif

                    @if ($setting->isConnected())
                        <form method="POST" action="{{ route('pengaturan.google-drive.test') }}">
                            @csrf
                            <x-ui.button type="submit" variant="outline" className="w-full">Test Koneksi</x-ui.button>
                        </form>

                        <form method="POST" action="{{ route('pengaturan.google-drive.disconnect') }}" onsubmit="return confirm('Putuskan koneksi Google Drive?')">
                            @csrf
                            <button type="submit" class="w-full text-sm text-error-500 hover:underline">Putuskan Koneksi</button>
                        </form>
                    @endif
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
