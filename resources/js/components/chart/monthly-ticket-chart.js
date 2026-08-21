export function initMonthlyTicketChart() {
  const chartEl = document.querySelector("#monthlyTicketChart");
  if (!chartEl) return;

  let labels = [];
  let series = [];
  try {
    labels = JSON.parse(chartEl.dataset.labels || "[]");
    series = JSON.parse(chartEl.dataset.series || "[]");
  } catch (e) {
    labels = [];
    series = [];
  }

  if (labels.length === 0) {
    chartEl.innerHTML =
      '<p class="py-10 text-center text-sm text-gray-400">Belum ada data.</p>';
    return;
  }

  const options = {
    series: [
      {
        name: "Tiket",
        data: series,
      },
    ],
    colors: ["#6366f1"],
    chart: {
      fontFamily: "Outfit, sans-serif",
      type: "bar",
      height: 260,
      width: "65%",
      toolbar: {
        show: false,
      },
      zoom: {
        enabled: false,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "25%",
        borderRadius: 4,
        borderRadiusApplication: "end",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: false,
    },
    xaxis: {
      categories: labels,
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    yaxis: {
      title: false,
      forceNiceScale: true,
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
    fill: {
      opacity: 1,
    },
    tooltip: {
      y: {
        formatter: (val) => `${val} tiket`,
      },
    },
  };

  const chart = new ApexCharts(chartEl, options);
  chart.render();

  return chart;
}

export default initMonthlyTicketChart;
