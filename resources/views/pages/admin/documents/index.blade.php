@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Reminder Dokumen" />

    @if (session('status'))
        <div class="mb-5"><x-ui.alert variant="success" :message="session('status')" /></div>
    @endif
    @if (session('error_status'))
        <div class="mb-5"><x-ui.alert variant="error" :message="session('error_status')" /></div>
    @endif
    @if ($errors->any())
        <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
    @endif

    <x-common.component-card title="Daftar Dokumen">
        <div class="flex justify-end">
            <a href="{{ route('documents.create') }}">
                <x-ui.button size="sm">Tambah Dokumen</x-ui.button>
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Dokumen</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jenis</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">No. Dokumen</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Berakhir</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Pengingat</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($documents as $document)
                            @php
                                $status = $document->expiryStatus();
                                $statusMap = [
                                    'expired' => ['label' => 'Sudah Berakhir', 'color' => 'error'],
                                    'critical' => ['label' => 'Segera Berakhir', 'color' => 'error'],
                                    'warning' => ['label' => 'Perlu Diperhatikan', 'color' => 'warning'],
                                    'ok' => ['label' => 'Berlaku', 'color' => 'success'],
                                    'none' => ['label' => 'Tanpa Tgl. Berakhir', 'color' => 'light'],
                                ];
                                $statusInfo = $statusMap[$status];
                                $lastLog = $document->reminderLogs->first();
                            @endphp
                            <tr>
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        @if ($document->file_path)
                                            <div class="flex shrink-0 items-center gap-2">
                                                <button type="button" @click="$dispatch('open-document-modal', { url: @js(route('documents.view', $document)), title: @js($document->title) })" title="Lihat Dokumen" aria-label="Lihat Dokumen" class="text-brand-500 hover:text-brand-600">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 5C6.90265 5 3.32805 8.09827 1.61515 10.1112C1.10861 10.7048 1.10861 11.2952 1.61515 11.8888C3.32805 13.9017 6.90265 17 12 17C17.0973 17 20.6719 13.9017 22.3848 11.8888C22.8914 11.2952 22.8914 10.7048 22.3848 10.1112C20.6719 8.09827 17.0973 5 12 5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M15 11C15 12.6569 13.6569 14 12 14C10.3431 14 9 12.6569 9 11C9 9.34315 10.3431 8 12 8C13.6569 8 15 9.34315 15 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                                <a href="{{ route('documents.download', $document) }}" title="Unduh" aria-label="Unduh" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 3.5V15.5M12 15.5L16.5 11M12 15.5L7.5 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M4 16.5V17.5C4 18.6046 4.89543 19.5 6 19.5H18C19.1046 19.5 20 18.6046 20 17.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $document->title }}</p>
                                            @if (! $document->file_path)
                                                <p class="text-theme-xs text-gray-400">Belum ada berkas</p>
                                            @endif
                                            @if (! $document->is_active)
                                                <p class="text-theme-xs text-gray-400">Pengingat nonaktif</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $document->documentType->name ?? '-' }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $document->document_number ?: '-' }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $document->expiry_date?->translatedFormat('d M Y') ?? '-' }}</p></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <x-ui.badge :color="$statusInfo['color']" variant="light" size="sm">{{ $statusInfo['label'] }}</x-ui.badge>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    @if ($lastLog)
                                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $lastLog->created_at->format('d/m/Y H:i') }}</p>
                                        <x-ui.badge :color="$lastLog->status === 'sent' ? 'success' : 'error'" variant="light" size="sm">{{ $lastLog->status }}</x-ui.badge>
                                    @else
                                        <p class="text-theme-xs text-gray-400">Belum pernah</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex justify-end relative">
                                        <x-common.table-dropdown>
                                            <x-slot name="button">
                                                <button type="button" class="text-gray-500 dark:text-gray-400">
                                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z" fill="currentColor" /></svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                <a href="{{ route('documents.edit', $document) }}" class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">Ubah</a>
                                                <form method="POST" action="{{ route('documents.test-reminder', $document) }}">
                                                    @csrf
                                                    <button type="submit" class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">Kirim Uji Coba WA</button>
                                                </form>
                                                <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Hapus dokumen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full px-3 py-2 font-medium text-left text-error-500 rounded-lg text-theme-xs hover:bg-gray-100 dark:hover:bg-white/5">Hapus</button>
                                                </form>
                                            </x-slot>
                                        </x-common.table-dropdown>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400">Belum ada dokumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-common.component-card>

    <div x-data="{ url: '', title: '' }" @open-document-modal.window="url = $event.detail.url; title = $event.detail.title">
        <x-ui.modal @open-document-modal.window="open = true" :isOpen="false" class="w-full max-w-3xl">
            <div class="flex h-[95vh] flex-col p-4 sm:p-6">
                <h4 class="mb-4 shrink-0 pr-10 text-lg font-semibold text-gray-800 dark:text-white/90" x-text="title"></h4>
                <div class="min-h-0 flex-1 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800">
                    <iframe :src="url" class="h-full w-full"></iframe>
                </div>
            </div>
        </x-ui.modal>
    </div>
@endsection
