@extends('layouts.fullscreen-layout')

@section('content')
    <div class="min-h-screen bg-gray-50 px-4 dark:bg-gray-900">
        @include('pages.tiket._nav')

        <div class="mx-auto w-full max-w-lg pb-16">
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/15">
                    <svg class="h-7 w-7 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Laporan Berhasil Dikirim</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Catat kode tiket berikut untuk melacak status perbaikan Anda.
                </p>

                <div class="mt-5 rounded-lg border border-dashed border-brand-300 bg-brand-50 px-4 py-3 dark:border-brand-800 dark:bg-brand-500/10">
                    <span class="text-title-sm font-bold tracking-wide text-brand-600 dark:text-brand-400">{{ $ticket->ticket_code }}</span>
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('lacak.show', $ticket->ticket_code) }}"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                        Lihat Status Tiket
                    </a>
                    <a href="{{ route('lapor.create') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
                        Buat Tiket Lain
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
