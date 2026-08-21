export const initTicketStatisticsChart = () => {
    const chartElement = document.querySelector('#ticketStatisticsChart');
    if (!chartElement) return;

    let labels = [];
    let incoming = [];
    let completed = [];
    try {
        labels = JSON.parse(chartElement.dataset.labels || '[]');
        incoming = JSON.parse(chartElement.dataset.incoming || '[]');
        completed = JSON.parse(chartElement.dataset.completed || '[]');
    } catch (e) {
        labels = [];
        incoming = [];
        completed = [];
    }

    const options = {
        series: [
            {
                name: 'Tiket Masuk',
                data: incoming,
            },
            {
                name: 'Tiket Selesai',
                data: completed,
            },
        ],
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
        },
        colors: ['#465FFF', '#12B76A'],
        chart: {
            fontFamily: 'Outfit, sans-serif',
            height: 310,
            type: 'area',
            toolbar: {
                show: false,
            },
        },
        fill: {
            gradient: {
                enabled: true,
                opacityFrom: 0.55,
                opacityTo: 0,
            },
        },
        stroke: {
            curve: 'straight',
            width: ['2', '2'],
        },
        markers: {
            size: 0,
        },
        grid: {
            xaxis: {
                lines: {
                    show: false,
                },
            },
            yaxis: {
                lines: {
                    show: true,
                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        tooltip: {
            y: {
                formatter: (val) => `${val} tiket`,
            },
        },
        xaxis: {
            type: 'category',
            categories: labels,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            tooltip: false,
        },
        yaxis: {
            title: {
                style: {
                    fontSize: '0px',
                },
            },
            forceNiceScale: true,
        },
    };

    const chart = new ApexCharts(chartElement, options);
    chart.render();

    return chart;
};

export default initTicketStatisticsChart;
