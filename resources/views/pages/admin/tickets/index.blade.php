@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tiket" />

    <x-common.component-card title="Daftar Tiket">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/judul/pelapor"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />

            <select name="status" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                <option value="">Semua Status</option>
                @foreach (\App\Models\Ticket::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="category_id" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="unit_id" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                <option value="">Semua Unit</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) request('unit_id') === (string) $unit->id)>{{ $unit->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <x-ui.button type="submit" className="flex-1">Filter</x-ui.button>
                <a href="{{ route('tickets.index') }}"><x-ui.button variant="outline" type="button">Reset</x-ui.button></a>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[1000px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kode</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Judul</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Pelapor / Unit</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kategori</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Prioritas</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Teknisi</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($tickets as $ticket)
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
                            <tr>
                                <td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ $ticket->ticket_code }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-700 text-theme-sm dark:text-gray-300">{{ $ticket->title }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->reporter_name }} · {{ $ticket->unit->name }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->category->name }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->priorityLabel() }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge :color="$statusColor" variant="light" size="sm">{{ $ticket->statusLabel() }}</x-ui.badge></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->assignee->name ?? '-' }}</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $ticket->created_at->format('d/m/Y H:i') }}</p></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-brand-500 text-theme-sm hover:text-brand-600">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-6 text-center text-gray-400">Belum ada tiket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </x-common.component-card>
@endsection
