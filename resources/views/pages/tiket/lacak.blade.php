@extends('layouts.fullscreen-layout')

@section('content')
    <div class="min-h-screen bg-gray-50 px-4 dark:bg-gray-900">
        @include('pages.tiket._nav')

        <div class="mx-auto w-full max-w-lg pb-16">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs sm:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Lacak Status Tiket</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Masukkan kode tiket yang Anda terima saat membuat laporan.
                </p>

                @if ($errors->any())
                    <div class="mt-4"><x-ui.alert variant="error" :message="$errors->first()" /></div>
                @endif

                <form method="POST" action="{{ route('lacak.lookup') }}" class="mt-5 space-y-5">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Kode Tiket<span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="ticket_code" value="{{ old('ticket_code') }}" required placeholder="TIK-20260820-0001"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <x-ui.button type="submit" className="w-full">Cek Status</x-ui.button>
                </form>
            </div>
        </div>
    </div>
@endsection
