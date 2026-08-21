@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Laporan" />

    <x-common.component-card title="Filter Laporan">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="mb-1.5 block text-xs text-gray-400">Bulan</label>
                <input type="month" name="month" value="{{ $filters['month'] }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-1.5 block text-xs text-gray-400">Kategori</label>
                <select name="category_id" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs text-gray-400">Unit</label>
                <select name="unit_id" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                    <option value="">Semua</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected((string) $filters['unit_id'] === (string) $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs text-gray-400">Status</label>
                <select name="status" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Ticket::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <x-ui.button type="submit" className="w-full">Terapkan</x-ui.button>
            </div>
        </form>

        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('laporan.export', array_merge($filters, ['format' => 'excel'])) }}">
                <x-ui.button type="button" variant="outline">Export Excel</x-ui.button>
            </a>
            <a href="{{ route('laporan.export', array_merge($filters, ['format' => 'pdf'])) }}">
                <x-ui.button type="button" variant="outline">Export PDF</x-ui.button>
            </a>
        </div>
    </x-common.component-card>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <x-common.component-card title="Ringkasan per Kategori">
                <div class="space-y-3">
                    @forelse ($summaryByCategory as $categoryName => $summary)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-300">{{ $categoryName }}</span>
                            <span class="text-gray-400">{{ $summary['done'] }}/{{ $summary['total'] }} selesai</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Tidak ada data pada periode ini.</p>
                    @endforelse
                </div>
            </x-common.component-card>
        </div>

        <div class="lg:col-span-2">
            <x-common.component-card :title="'Rincian Tiket ('.$tickets->count().')'">
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[1000px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kode</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kategori</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Unit</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Lapor</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Mulai</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Selesai</p></th>
                                    <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Bukti Dukung</p></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ $ticket->ticket_code }}</p></td>
                                        <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->category->name }}</p></td>
                                        <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->unit->name }}</p></td>
                                        <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->statusLabel() }}</p></td>
                                        <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->created_at->format('d/m/Y') }}</p></td>
                                        <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->started_at?->format('d/m/Y') ?? '-' }}</p></td>
                                        <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->resolved_at?->format('d/m/Y') ?? '-' }}</p></td>
                                        <td class="px-5 py-4 sm:px-6">
                                            @if ($ticket->completionAttachments->isNotEmpty())
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($ticket->completionAttachments as $attachment)
                                                        <a href="{{ asset('storage/'.$attachment->path) }}" target="_blank" class="text-brand-500 hover:text-brand-600" title="{{ $attachment->original_name }}">
                                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.33301 10C3.33301 6.31811 6.31778 3.33334 9.99967 3.33334C13.6816 3.33334 16.6663 6.31811 16.6663 10C16.6663 13.6819 13.6816 16.6667 9.99967 16.6667C6.31778 16.6667 3.33301 13.6819 3.33301 10Z" stroke="currentColor" stroke-width="1.5"/><path d="M7.5 10L9.16667 11.6667L12.5 8.33334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-400 text-theme-sm">-</p>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="px-5 py-6 text-center text-gray-400">Tidak ada tiket.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
