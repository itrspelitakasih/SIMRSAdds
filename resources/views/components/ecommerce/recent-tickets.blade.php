@props(['tickets' => []])

@php
    $getStatusClasses = function ($status) {
        $baseClasses = 'rounded-full px-2 py-0.5 text-theme-xs font-medium';

        return match ($status) {
            'open' => $baseClasses.' bg-blue-light-50 text-blue-light-600 dark:bg-blue-light-500/15 dark:text-blue-light-400',
            'in_progress' => $baseClasses.' bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400',
            'waiting_confirmation' => $baseClasses.' bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
            'done' => $baseClasses.' bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            'rejected' => $baseClasses.' bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            default => $baseClasses.' bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400',
        };
    };
@endphp

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tiket Terbaru</h3>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                Lihat Semua
            </a>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="min-w-full">
            <thead>
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <th class="py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tiket</p></th>
                    <th class="py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kategori</p></th>
                    <th class="py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Unit</p></th>
                    <th class="py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td class="py-3 whitespace-nowrap">
                            <a href="{{ route('tickets.show', $ticket) }}" class="block">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                    {{ $ticket->ticket_code }}
                                </p>
                                <span class="max-w-[220px] truncate text-gray-500 text-theme-xs dark:text-gray-400">
                                    {{ $ticket->title }}
                                </span>
                            </a>
                        </td>
                        <td class="py-3 whitespace-nowrap">
                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->category->name }}</p>
                        </td>
                        <td class="py-3 whitespace-nowrap">
                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->unit->name }}</p>
                        </td>
                        <td class="py-3 whitespace-nowrap">
                            <span class="{{ $getStatusClasses($ticket->status) }}">
                                {{ $ticket->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-sm text-gray-400">Belum ada tiket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
