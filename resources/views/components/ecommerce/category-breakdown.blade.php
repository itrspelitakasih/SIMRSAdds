@props(['categories' => []])

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
    <div class="flex justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Tiket per Kategori
            </h3>
            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                Kategori tiket dengan jumlah terbanyak
            </p>
        </div>
    </div>

    <div class="mt-6 space-y-5">
        @forelse ($categories as $category)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-50 text-theme-xs font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                        {{ strtoupper(substr($category['name'], 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                            {{ $category['name'] }}
                        </p>
                        <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
                            {{ number_format($category['count']) }} Tiket
                        </span>
                    </div>
                </div>

                <div class="flex w-full max-w-[140px] items-center gap-3">
                    <div class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800">
                        <div class="absolute left-0 top-0 flex h-full items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white"
                            style="width: {{ $category['percentage'] }}%"></div>
                    </div>
                    <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $category['percentage'] }}%
                    </p>
                </div>
            </div>
        @empty
            <p class="py-6 text-center text-sm text-gray-400">Belum ada data kategori.</p>
        @endforelse
    </div>
</div>
