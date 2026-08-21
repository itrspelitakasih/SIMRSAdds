@props([
    'rate' => 0,
    'openCount' => 0,
    'avgResolutionHours' => null,
    'rejectedThisMonth' => 0,
])

<div class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="shadow-default rounded-2xl bg-white px-5 pb-11 pt-5 dark:bg-gray-900 sm:px-6 sm:pt-6">
        <div class="flex justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Target Penyelesaian Bulanan
                </h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Persentase tiket selesai dari total tiket masuk bulan ini
                </p>
            </div>
        </div>
        <div class="relative max-h-[200px] overflow-hidden">
            <div id="ticketTargetChart" class="h-full" data-rate="{{ min(100, $rate) }}"></div>
            <span class="absolute left-1/2 top-[80%] -translate-x-1/2 -translate-y-1/2 rounded-full {{ $rate >= 100 ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500' : 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' }} px-3 py-1 text-xs font-medium">
                {{ $rate >= 100 ? 'Tercapai' : 'Dalam Proses' }}
            </span>
        </div>
        <p class="mx-auto mt-4 w-full max-w-[380px] text-center text-sm text-gray-500 sm:text-base">
            @if ($rate >= 100)
                Semua tiket bulan ini sudah terselesaikan. Kerja bagus!
            @else
                Sebagian tiket bulan ini masih dalam proses penyelesaian.
            @endif
        </p>
    </div>

    <div class="flex items-center justify-center gap-5 px-6 py-3.5 sm:gap-8 sm:py-5">
        <div>
            <p class="mb-1 text-center text-theme-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                Sedang Berjalan
            </p>
            <p class="flex items-center justify-center gap-1 text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg">
                {{ number_format($openCount) }}
            </p>
        </div>

        <div class="h-7 w-px bg-gray-200 dark:bg-gray-800"></div>

        <div>
            <p class="mb-1 text-center text-theme-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                Rata-rata Selesai
            </p>
            <p class="flex items-center justify-center gap-1 text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg">
                {{ $avgResolutionHours !== null ? $avgResolutionHours.' jam' : '-' }}
            </p>
        </div>

        <div class="h-7 w-px bg-gray-200 dark:bg-gray-800"></div>

        <div>
            <p class="mb-1 text-center text-theme-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                Ditolak Bulan Ini
            </p>
            <p class="flex items-center justify-center gap-1 text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg">
                {{ number_format($rejectedThisMonth) }}
            </p>
        </div>
    </div>
</div>
