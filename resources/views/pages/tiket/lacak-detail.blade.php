@extends('layouts.fullscreen-layout')

@php
    $statusColor = match ($ticket->status) {
        'open' => 'info',
        'in_progress' => 'warning',
        'waiting_confirmation' => 'primary',
        'done' => 'success',
        'rejected' => 'error',
        default => 'light',
    };
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 px-4 dark:bg-gray-900">
        @include('pages.tiket._nav')

        <div class="mx-auto w-full max-w-2xl pb-16">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs sm:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <span class="text-xs font-medium text-gray-400">{{ $ticket->ticket_code }}</span>
                        <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ $ticket->title }}</h1>
                    </div>
                    <x-ui.badge :color="$statusColor" variant="light">{{ $ticket->statusLabel() }}</x-ui.badge>
                </div>

                <dl class="mt-6 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-400">Pelapor</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->reporter_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Unit</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->unit->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Kategori</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Prioritas</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->priorityLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Ditangani oleh</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->assignee->name ?? 'Belum ditugaskan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Dilaporkan</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->created_at->translatedFormat('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Mulai Dikerjakan</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->started_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Selesai</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->resolved_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <dt class="text-sm text-gray-400">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $ticket->description }}</dd>
                </div>

                @php $completionAttachments = $ticket->attachments->where('type', 'completion'); @endphp
                @if ($completionAttachments->isNotEmpty())
                    <div class="mt-6">
                        <dt class="mb-2 text-sm text-gray-400">Bukti Dukung Penyelesaian</dt>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($completionAttachments as $attachment)
                                <a href="{{ asset('storage/'.$attachment->path) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$attachment->path) }}" alt="{{ $attachment->original_name }}" class="h-24 w-24 rounded-lg border-2 border-success-500 object-cover" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-8">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Riwayat Progres</h2>
                    <ol class="mt-3 space-y-4 border-l border-gray-200 pl-4 dark:border-gray-800">
                        @forelse ($ticket->logs as $log)
                            <li class="relative">
                                <span class="absolute -left-[21px] top-1 h-2.5 w-2.5 rounded-full bg-brand-500"></span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $log->message ?? 'Status diperbarui.' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $log->created_at->translatedFormat('d M Y H:i') }}</p>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400">Belum ada riwayat.</li>
                        @endforelse
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection
