@extends('layouts.app')

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
    <x-common.page-breadcrumb pageTitle="Detail Tiket" />

    @if (session('status'))
        <div class="mb-5"><x-ui.alert variant="success" :message="session('status')" /></div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-common.component-card :title="$ticket->ticket_code.' · '.$ticket->title">
                <div class="flex items-center gap-3">
                    <x-ui.badge :color="$statusColor" variant="light">{{ $ticket->statusLabel() }}</x-ui.badge>
                    <span class="text-sm text-gray-400">Prioritas: {{ $ticket->priorityLabel() }}</span>
                </div>

                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-400">Pelapor</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->reporter_name }} ({{ $ticket->reporter_phone ?: '-' }})</dd>
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
                        <dt class="text-gray-400">Tanggal Lapor</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Tanggal Mulai Dikerjakan</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->started_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Tanggal Selesai</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $ticket->resolved_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>

                <div>
                    <dt class="text-sm text-gray-400">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $ticket->description }}</dd>
                </div>

                @php $reportAttachments = $ticket->attachments->where('type', 'report'); @endphp
                @if ($reportAttachments->isNotEmpty())
                    <div>
                        <dt class="mb-2 text-sm text-gray-400">Lampiran Laporan</dt>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($reportAttachments as $attachment)
                                <a href="{{ asset('storage/'.$attachment->path) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$attachment->path) }}" alt="{{ $attachment->original_name }}" class="h-24 w-24 rounded-lg object-cover" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @php $completionAttachments = $ticket->attachments->where('type', 'completion'); @endphp
                @if ($completionAttachments->isNotEmpty())
                    <div>
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
            </x-common.component-card>

            <x-common.component-card title="Riwayat & Komentar">
                <ol class="space-y-4 border-l border-gray-200 pl-4 dark:border-gray-800">
                    @forelse ($ticket->logs as $log)
                        <li class="relative">
                            <span class="absolute -left-[21px] top-1 h-2.5 w-2.5 rounded-full bg-brand-500"></span>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log->message ?? 'Status diperbarui.' }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $log->user->name ?? 'Pelapor' }} · {{ $log->created_at->format('d/m/Y H:i') }}
                            </p>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400">Belum ada riwayat.</li>
                    @endforelse
                </ol>

                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-5 flex gap-2">
                    @csrf
                    <input type="text" name="message" required placeholder="Tulis komentar/progres..."
                        class="dark:bg-dark-900 h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
                    <x-ui.button type="submit">Kirim</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <div class="space-y-6">
            <x-common.component-card title="Ubah Status">
                <form method="POST" action="{{ route('tickets.status', $ticket) }}" enctype="multipart/form-data" class="space-y-3"
                    x-data="{ status: '{{ $ticket->status }}' }">
                    @csrf
                    @method('PATCH')
                    <select name="status" x-model="status" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                        @foreach (\App\Models\Ticket::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <div x-show="status === 'done'" x-cloak>
                        <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">
                            Bukti Dukung (foto hasil pekerjaan)
                            @if ($completionAttachments->isEmpty())
                                <span class="text-error-500">*wajib diisi</span>
                            @endif
                        </label>
                        <input type="file" name="completion_proof[]" accept="image/*" multiple
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-2.5 file:py-1 file:text-xs dark:border-gray-700 dark:text-white/90" />
                        @error('completion_proof')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-ui.button type="submit" className="w-full">Simpan Status</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Penugasan Teknisi">
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <select name="assigned_to" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                        <option value="">Belum ditugaskan</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician->id }}" @selected($ticket->assigned_to === $technician->id)>{{ $technician->name }}</option>
                        @endforeach
                    </select>
                    <x-ui.button type="submit" variant="outline" className="w-full">Simpan Penugasan</x-ui.button>
                </form>
            </x-common.component-card>
        </div>
    </div>
@endsection
