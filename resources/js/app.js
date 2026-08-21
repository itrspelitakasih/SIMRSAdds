import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
// FullCalendar
import { Calendar } from '@fullcalendar/core';



window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports
    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then(module => module.initChartThirteen());
    }
    if (document.querySelector('#monthlyTicketChart')) {
        import('./components/chart/monthly-ticket-chart').then(module => module.initMonthlyTicketChart());
    }
    if (document.querySelector('#categoryByUnitChart')) {
        import('./components/chart/category-by-unit-chart').then(module => module.initCategoryByUnitChart());
    }
    if (document.querySelector('#ticketTargetChart')) {
        import('./components/chart/ticket-target-chart').then(module => module.initTicketTargetChart());
    }
    if (document.querySelector('#ticketStatisticsChart')) {
        import('./components/chart/ticket-statistics-chart').then(module => module.initTicketStatisticsChart());
    }

    // Calendar init
    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }
    if (document.querySelector('#dashboardCalendar')) {
        import('./components/dashboard-calendar-init').then(module => module.dashboardCalendarInit());
    }

    // The main content area animates its width when the sidebar collapses/expands
    // or hovers open. ApexCharts measures its container once at render time, so
    // charts rendered mid-transition end up sized for the wrong width. Re-dispatch
    // a resize event whenever the content area's width actually settles so every
    // chart on the page (they all listen for window resize) re-syncs to it.
    const mainContent = document.querySelector('#mainContent');
    if (mainContent) {
        let lastWidth = mainContent.clientWidth;
        const resizeObserver = new ResizeObserver(() => {
            if (mainContent.clientWidth !== lastWidth) {
                lastWidth = mainContent.clientWidth;
                window.dispatchEvent(new Event('resize'));
            }
        });
        resizeObserver.observe(mainContent);
    }

    // Also re-sync once everything (fonts, images) has finished loading, in case
    // a chart's very first render happened before layout/fonts had settled.
    window.addEventListener('load', () => window.dispatchEvent(new Event('resize')));
});
