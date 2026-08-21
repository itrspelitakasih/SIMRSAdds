@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Ubah Dokumen" />

    @if ($errors->any())
        <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-common.component-card title="Form Dokumen">
                <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('pages.admin.documents._form')
                    <div class="flex gap-3">
                        <x-ui.button type="submit">Simpan</x-ui.button>
                        <a href="{{ route('documents.index') }}"><x-ui.button variant="outline" type="button">Batal</x-ui.button></a>
                    </div>
                </form>
            </x-common.component-card>
        </div>

        <div>
            <x-common.component-card title="Riwayat Pengingat">
                <div class="space-y-3 text-sm">
                    @forelse ($document->reminderLogs->take(10) as $log)
                        <div class="border-b border-gray-100 pb-2 last:border-0 dark:border-gray-800">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $log->phone }} · H-{{ $log->days_before }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <x-ui.badge :color="$log->status === 'sent' ? 'success' : 'error'" variant="light" size="sm">{{ $log->status }}</x-ui.badge>
                            </div>
                            @if ($log->status === 'failed' && $log->response)
                                <p class="mt-1 break-words text-xs text-error-500">{{ $log->response }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400">Belum ada pengingat terkirim.</p>
                    @endforelse
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
