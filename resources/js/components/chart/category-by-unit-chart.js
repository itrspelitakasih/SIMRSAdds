const PALETTE = [
  "#465fff",
  "#f97316",
  "#22c55e",
  "#a855f7",
  "#ec4899",
  "#06b6d4",
  "#eab308",
  "#ef4444",
  "#14b8a6",
  "#6366f1",
];

export function initCategoryByUnitChart() {
  const chartEl = document.querySelector("#categoryByUnitChart");
  if (!chartEl) return;

  let units = [];
  let series = [];
  try {
    units = JSON.parse(chartEl.dataset.units || "[]");
    series = JSON.parse(chartEl.dataset.series || "[]");
  } catch (e) {
    units = [];
    series = [];
  }

  if (units.length === 0 || series.length === 0) {
    chartEl.innerHTML =
      '<p class="py-10 text-center text-sm text-gray-400">Belum ada data.</p>';
    return;
  }

  const options = {
    series,
    colors: PALETTE.slice(0, series.length),
    chart: {
      fontFamily: "Outfit, sans-serif",
      type: "bar",
      height: 230,
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
        columnWidth: "55%",
        borderRadius: 4,
        borderRadiusApplication: "end",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 2,
      colors: ["transparent"],
    },
    xaxis: {
      categories: units,
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "left",
      fontFamily: "Outfit",
      fontSize: "13px",
      markers: {
        size: 5,
        shape: "circle",
        strokeWidth: 0,
      },
      itemMargin: {
        horizontal: 8,
        vertical: 4,
      },
    },
    yaxis: {
      title: false,
      forceNiceScale: true,
    },
    grid: {
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

export default initCategoryByUnitChart;
