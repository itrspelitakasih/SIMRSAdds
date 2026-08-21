@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengaturan WhatsApp" />

    @if (session('status'))
        <div class="mb-5"><x-ui.alert variant="success" :message="session('status')" /></div>
    @endif
    @if (session('error_status'))
        <div class="mb-5"><x-ui.alert variant="error" :message="session('error_status')" /></div>
    @endif
    @if ($errors->any())
        <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
    @endif
    @if (! $setting->is_active)
        <div class="mb-5"><x-ui.alert variant="warning" title="Notifikasi WhatsApp nonaktif" message="Centang 'Aktifkan notifikasi WhatsApp' di bawah lalu simpan, jika tidak seluruh pengiriman akan langsung gagal tanpa menghubungi server Gowa." /></div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-common.component-card title="Koneksi Gowa">
                <form method="POST" action="{{ route('pengaturan.whatsapp.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">URL Server</label>
                            <input type="text" name="base_url" value="{{ old('base_url', $setting->base_url) }}" placeholder="http://localhost atau https://gowa.domain-anda.id"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Port</label>
                            <input type="text" name="port" value="{{ old('port', $setting->port) }}" placeholder="kosongkan jika pakai domain"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                            <p class="mt-1 text-xs text-gray-400">Isi hanya jika mengakses Gowa langsung via IP:port (mis. server lokal). Kosongkan jika URL Server sudah domain publik/HTTPS di belakang reverse proxy.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Username</label>
                            <input type="text" name="username" value="{{ old('username', $setting->username) }}"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
                            <input type="password" name="password" placeholder="{{ $setting->password ? '••••••••' : '' }}"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Device ID</label>
                            <input type="text" name="device_id" value="{{ old('device_id', $setting->device_id) }}"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">No. WhatsApp Admin (notif tiket baru)</label>
                            <input type="text" name="notify_admin_number" value="{{ old('notify_admin_number', $setting->notify_admin_number) }}" placeholder="08xxxxxxxxxx"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $setting->is_active)) class="h-4 w-4 rounded border-gray-300" />
                        Aktifkan notifikasi WhatsApp
                    </label>

                    <x-ui.button type="submit">Simpan Pengaturan</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <div>
            <x-common.component-card title="Test Kirim Pesan">
                <form method="POST" action="{{ route('pengaturan.whatsapp.test') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">No. WhatsApp Tujuan</label>
                        <input type="text" name="test_phone" required placeholder="08xxxxxxxxxx"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                    </div>
                    <x-ui.button type="submit" variant="outline" className="w-full">Kirim Pesan Uji Coba</x-ui.button>
                </form>
            </x-common.component-card>

            <div class="mt-6">
                <x-common.component-card title="Log Notifikasi Terbaru">
                    <div class="space-y-3 text-sm">
                        @forelse (\App\Models\NotificationLog::latest()->limit(10)->get() as $log)
                            <div class="border-b border-gray-100 pb-2 last:border-0 dark:border-gray-800">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $log->phone }} · {{ $log->event }}</p>
                                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <x-ui.badge :color="$log->status === 'sent' ? 'success' : 'error'" variant="light" size="sm">{{ $log->status }}</x-ui.badge>
                                </div>
                                @if ($log->status === 'failed' && $log->response)
                                    <p class="mt-1 break-words text-xs text-error-500">{{ $log->response }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-400">Belum ada log notifikasi.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            </div>
        </div>
    </div>
@endsection
