@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard" />

    <div class="grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12">
            <x-ecommerce.ticket-metrics
                :inProgressCount="$openCount"
                :rejectedCount="$rejectedThisMonth"
                :rejectedChange="$rejectedChange"
                :completedCount="$doneThisMonth"
                :completedChange="$doneChange"
                :incomingCount="$totalThisMonth"
                :incomingChange="$incomingChange"
            />
        </div>

        <div class="col-span-12 xl:col-span-7">
            <x-common.component-card title="Tiket per Bulan" desc="Jumlah tiket masuk setiap bulan ({{ now()->year }})">
                <div id="monthlyTicketChart" class="flex justify-center"
                    data-labels="{{ json_encode($monthlyTicketChart['labels']) }}"
                    data-series="{{ json_encode($monthlyTicketChart['series']) }}"></div>
            </x-common.component-card>
        </div>

        <div class="col-span-12 xl:col-span-5">
            <x-ecommerce.ticket-target
                :rate="$resolutionRate"
                :openCount="$openCount"
                :avgResolutionHours="$avgResolutionHours"
                :rejectedThisMonth="$rejectedThisMonth"
            />
        </div>

        <div class="col-span-12">
            <x-ecommerce.ticket-statistics-chart
                :labels="$ticketStatisticsChart['labels']"
                :incoming="$ticketStatisticsChart['incoming']"
                :completed="$ticketStatisticsChart['completed']"
            />
        </div>

        <div class="col-span-12 xl:col-span-5">
            <x-ecommerce.category-breakdown :categories="$categoryBreakdown" />
        </div>

        <div class="col-span-12 xl:col-span-7">
            <x-ecommerce.recent-tickets :tickets="$recentTickets" />
        </div>

        <div class="col-span-12 xl:col-span-5">
            <x-common.component-card title="Reminder Dokumen">
                <div class="custom-calendar dashboard-calendar-compact">
                    <div id="dashboardCalendar" data-document-events="{{ json_encode($documentReminderEvents) }}"></div>
                </div>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-brand-500"></span>Aktif</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-orange-500"></span>Segera Jatuh Tempo</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-success-500"></span>Aman</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-error-500"></span>Kadaluarsa</span>
                </div>
            </x-common.component-card>
        </div>

        <div class="col-span-12 xl:col-span-7">
            <x-common.component-card title="Tiket per Kategori per Unit">
                <div id="categoryByUnitChart"
                    data-units="{{ json_encode($categoryByUnitChart['units']) }}"
                    data-series="{{ json_encode($categoryByUnitChart['series']) }}"></div>
            </x-common.component-card>
        </div>
    </div>
@endsection
