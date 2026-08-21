@props([
    'labels' => [],
    'incoming' => [],
    'completed' => [],
])

<div class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
    <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:justify-between">
        <div class="w-full">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Statistik Tiket per Unit
            </h3>
            <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
                Perbandingan tiket masuk dan tiket selesai per unit
            </p>
        </div>
    </div>
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <div id="ticketStatisticsChart" class="-ml-4 min-w-[700px] pl-2 xl:min-w-full"
            data-labels="{{ json_encode($labels) }}"
            data-incoming="{{ json_encode($incoming) }}"
            data-completed="{{ json_encode($completed) }}"></div>
    </div>
</div>
