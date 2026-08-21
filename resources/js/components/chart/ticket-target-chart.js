export const initTicketTargetChart = () => {
    const chartElement = document.querySelector('#ticketTargetChart');
    if (!chartElement) return;

    const rate = parseFloat(chartElement.dataset.rate || '0');

    const options = {
        series: [rate],
        colors: ['#465FFF'],
        chart: {
            fontFamily: 'Outfit, sans-serif',
            type: 'radialBar',
            height: 200,
            sparkline: {
                enabled: true,
            },
        },
        plotOptions: {
            radialBar: {
                startAngle: -90,
                endAngle: 90,
                hollow: {
                    size: '80%',
                },
                track: {
                    background: '#E4E7EC',
                    strokeWidth: '100%',
                    margin: 5,
                },
                dataLabels: {
                    name: {
                        show: false,
                    },
                    value: {
                        fontSize: '36px',
                        fontWeight: '600',
                        offsetY: -30,
                        color: '#1D2939',
                        formatter: function (val) {
                            return val + '%';
                        },
                    },
                },
            },
        },
        fill: {
            type: 'solid',
            colors: ['#465FFF'],
        },
        stroke: {
            lineCap: 'round',
        },
        labels: ['Tercapai'],
    };

    const chart = new ApexCharts(chartElement, options);
    chart.render();

    return chart;
};

export default initTicketTargetChart;
