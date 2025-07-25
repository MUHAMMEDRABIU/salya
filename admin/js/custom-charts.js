// Initialize Charts
document.addEventListener("DOMContentLoaded", function () {
  // Sales Chart
  const salesCtx = document.getElementById("salesChart").getContext("2d");
  new Chart(salesCtx, {
    type: "line",
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
      datasets: [
        {
          label: "Sales",
          data: [12000, 19000, 15000, 25000, 22000, 30000],
          borderColor: "#F97316",
          backgroundColor: "rgba(249, 115, 22, 0.1)",
          borderWidth: 3,
          fill: true,
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: "rgba(0, 0, 0, 0.1)",
          },
          ticks: {
            callback: function (value) {
              return "$" + value.toLocaleString();
            },
          },
        },
        x: {
          grid: {
            display: false,
          },
        },
      },
    },
  });

  // Orders Chart
  const ordersCtx = document.getElementById("ordersChart").getContext("2d");
  new Chart(ordersCtx, {
    type: "doughnut",
    data: {
      labels: ["Chicken", "Fish", "Turkey"],
      datasets: [
        {
          data: [45, 35, 20],
          backgroundColor: ["#F97316", "#3B82F6", "#EF4444"],
          borderWidth: 0,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            usePointStyle: true,
            padding: 20,
          },
        },
      },
    },
  });
});
