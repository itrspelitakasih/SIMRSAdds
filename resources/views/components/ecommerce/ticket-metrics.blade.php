@props([
    'inProgressCount' => 0,
    'rejectedCount' => 0,
    'rejectedChange' => 0,
    'completedCount' => 0,
    'completedChange' => 0,
    'incomingCount' => 0,
    'incomingChange' => 0,
])

@php
    $badge = function (float $change, bool $downIsGood = false) {
        $isUp = $change >= 0;
        $isGood = $downIsGood ? ! $isUp : $isUp;
        $color = $isGood
            ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
            : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500';

        return ['isUp' => $isUp, 'color' => $color];
    };

    $rejectedBadge = $badge($rejectedChange, downIsGood: true);
    $completedBadge = $badge($completedChange);
    $incomingBadge = $badge($incomingChange);

    $arrow = fn (bool $isUp) => '<svg class="fill-current '.($isUp ? '' : 'rotate-180').'" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" fill=""/></svg>';
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 lg:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 6.25C12.4142 6.25 12.75 6.58579 12.75 7V11.6893L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L11.4697 12.5303C11.329 12.3896 11.25 12.1989 11.25 12V7C11.25 6.58579 11.5858 6.25 12 6.25Z" fill=""/>
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Tiket Diproses</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ number_format($inProgressCount) }}</h4>
            </div>

            <span class="flex items-center gap-1 rounded-full py-0.5 pl-2 pr-2.5 text-sm font-medium bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                Berjalan
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM8.96966 8.96966C9.26256 8.67677 9.73744 8.67677 10.0303 8.96966L12 10.9393L13.9697 8.96966C14.2626 8.67677 14.7374 8.67677 15.0303 8.96966C15.3232 9.26256 15.3232 9.73744 15.0303 10.0303L13.0607 12L15.0303 13.9697C15.3232 14.2626 15.3232 14.7374 15.0303 15.0303C14.7374 15.3232 14.2626 15.3232 13.9697 15.0303L12 13.0607L10.0303 15.0303C9.73744 15.3232 9.26256 15.3232 8.96966 15.0303C8.67677 14.7374 8.67677 14.2626 8.96966 13.9697L10.9393 12L8.96966 10.0303C8.67677 9.73744 8.67677 9.26256 8.96966 8.96966Z" fill=""/>
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Tiket Ditolak</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ number_format($rejectedCount) }}</h4>
            </div>

            <span class="flex items-center gap-1 rounded-full py-0.5 pl-2 pr-2.5 text-sm font-medium {{ $rejectedBadge['color'] }}">
                {!! $arrow($rejectedBadge['isUp']) !!}
                {{ abs($rejectedChange) }}%
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM16.6109 9.14615C16.9007 9.43769 16.9007 9.90992 16.6092 10.2007L11.1274 15.6659C10.8367 15.9559 10.3656 15.9564 10.0742 15.6672L7.39018 13.0132C7.09863 12.7239 7.09677 12.2517 7.38603 11.9601C7.6753 11.6686 8.14753 11.6667 8.43907 11.956L10.5993 14.0961L15.5547 9.14453C15.8462 8.85379 16.3184 8.85462 16.6109 9.14615Z" fill=""/>
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Tiket Selesai</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ number_format($completedCount) }}</h4>
            </div>

            <span class="flex items-center gap-1 rounded-full py-0.5 pl-2 pr-2.5 text-sm font-medium {{ $completedBadge['color'] }}">
                {!! $arrow($completedBadge['isUp']) !!}
                {{ abs($completedChange) }}%
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.25 4.25C4.14543 4.25 3.25 5.14543 3.25 6.25V12.4756L7.02039 12.4749C7.6027 12.4749 8.13376 12.8073 8.38924 13.3309L9.42747 15.4547C9.48191 15.5658 9.59474 15.6362 9.7184 15.6362H14.2816C14.4053 15.6362 14.5181 15.5658 14.5725 15.4547L15.6108 13.3309C15.8662 12.8073 16.3973 12.4749 16.9796 12.4749L20.75 12.4756V6.25C20.75 5.14543 19.8546 4.25 18.75 4.25H5.25ZM20.75 13.9756L16.9796 13.9749C16.9584 13.9749 16.939 13.9868 16.9294 14.0064L15.8912 16.1302C15.5836 16.7605 14.9436 17.1362 14.2816 17.1362H9.7184C9.0564 17.1362 8.41641 16.7605 8.10883 16.1302L7.07061 14.0064C7.06099 13.9868 7.04162 13.9749 7.02039 13.9749L3.25 13.9756V17.75C3.25 18.8546 4.14543 19.75 5.25 19.75H18.75C19.8546 19.75 20.75 18.8546 20.75 17.75V13.9756ZM1.75 6.25C1.75 4.31701 3.31701 2.75 5.25 2.75H18.75C20.683 2.75 22.25 4.31701 22.25 6.25V17.75C22.25 19.683 20.683 21.25 18.75 21.25H5.25C3.31701 21.25 1.75 19.683 1.75 17.75V6.25Z" fill=""/>
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Tiket Masuk</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ number_format($incomingCount) }}</h4>
            </div>

            <span class="flex items-center gap-1 rounded-full py-0.5 pl-2 pr-2.5 text-sm font-medium {{ $incomingBadge['color'] }}">
                {!! $arrow($incomingBadge['isUp']) !!}
                {{ abs($incomingChange) }}%
            </span>
        </div>
    </div>
</div>
