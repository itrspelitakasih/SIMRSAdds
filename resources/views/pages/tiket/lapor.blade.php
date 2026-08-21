@extends('layouts.fullscreen-layout')

@section('content')
    <div class="min-h-screen bg-gray-50 px-4 dark:bg-gray-900">
        @include('pages.tiket._nav')

        <div class="mx-auto w-full max-w-2xl pb-16">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs sm:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6">
                    <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Buat Tiket Laporan</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Laporkan kerusakan hardware, software, atau permintaan fitur SIMRS baru. Tidak perlu login.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-5"><x-ui.alert variant="error" :message="$errors->first()" /></div>
                @endif

                <form method="POST" action="{{ route('lapor.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Nama Pelapor<span class="text-error-500">*</span>
                            </label>
                            <input type="text" name="reporter_name" value="{{ old('reporter_name') }}" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                No. WhatsApp
                            </label>
                            <input type="text" name="reporter_phone" value="{{ old('reporter_phone') }}" placeholder="08xxxxxxxxxx"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            <p class="mt-1 text-xs text-gray-400">Diisi bila ingin menerima notifikasi WhatsApp status tiket.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Unit / Ruangan<span class="text-error-500">*</span>
                            </label>
                            <select name="unit_id" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Pilih unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Kategori<span class="text-error-500">*</span>
                            </label>
                            <select name="category_id" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Prioritas<span class="text-error-500">*</span>
                        </label>
                        <select name="priority" required
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            @foreach (\App\Models\Ticket::PRIORITIES as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'medium') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Judul Laporan<span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Printer kasir tidak bisa mencetak"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Deskripsi<span class="text-error-500">*</span>
                        </label>
                        <textarea name="description" rows="4" required placeholder="Jelaskan detail kerusakan/permintaan"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Foto (opsional)
                        </label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-1.5 file:text-sm dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <x-ui.button type="submit" className="w-full">Kirim Laporan</x-ui.button>
                </form>
            </div>
        </div>
    </div>
@endsection
