// Analytics Dashboard JavaScript

document.addEventListener("DOMContentLoaded", function () {
  // Initialize all charts
  initializeSalesChart();
  initializeCategoryChart();
  initializeRevenueChart();

  // Add event listeners for filters
  document
    .getElementById("dateRange")
    .addEventListener("change", handleDateRangeChange);
  document
    .getElementById("categoryFilter")
    .addEventListener("change", handleCategoryFilterChange);
});

// Sales Overview Chart
function initializeSalesChart() {
  const ctx = document.getElementById("salesChart").getContext("2d");

  new Chart(ctx, {
    type: "line",
    data: {
      labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
      datasets: [
        {
          label: "Sales",
          data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
          borderColor: "#F97316",
          backgroundColor: "rgba(249, 115, 22, 0.1)",
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: "#F97316",
          pointBorderColor: "#ffffff",
          pointBorderWidth: 2,
          pointRadius: 6,
          pointHoverRadius: 8,
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
            color: "rgba(0, 0, 0, 0.05)",
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
      elements: {
        point: {
          hoverBackgroundColor: "#F97316",
        },
      },
    },
  });
}

// Orders by Category Chart
function initializeCategoryChart() {
  const ctx = document.getElementById("categoryChart").getContext("2d");

  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Chicken", "Seafood", "Beef", "Vegetables", "Others"],
      datasets: [
        {
          data: [35, 25, 20, 15, 5],
          backgroundColor: [
            "#F97316",
            "#3B82F6",
            "#10B981",
            "#8B5CF6",
            "#FF7272",
          ],
          borderWidth: 0,
          hoverOffset: 10,
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
            padding: 20,
            usePointStyle: true,
            pointStyle: "circle",
          },
        },
      },
      cutout: "60%",
    },
  });
}

// Revenue Trends Chart
function initializeRevenueChart() {
  const ctx = document.getElementById("revenueChart").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "Revenue",
          data: [
            12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 32000,
            40000, 38000, 45000,
          ],
          backgroundColor: "#F97316",
          borderRadius: 6,
          borderSkipped: false,
        },
        {
          label: "Orders",
          data: [
            480, 760, 600, 1000, 880, 1200, 1120, 1400, 1280, 1600, 1520, 1800,
          ],
          backgroundColor: "#3B82F6",
          borderRadius: 6,
          borderSkipped: false,
          yAxisID: "y1",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: "index",
        intersect: false,
      },
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          type: "linear",
          display: true,
          position: "left",
          grid: {
            color: "rgba(0, 0, 0, 0.05)",
          },
          ticks: {
            callback: function (value) {
              return "$" + value / 1000 + "k";
            },
          },
        },
        y1: {
          type: "linear",
          display: true,
          position: "right",
          grid: {
            drawOnChartArea: false,
          },
          ticks: {
            callback: function (value) {
              return value.toLocaleString();
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
}

// Handle date range filter change
function handleDateRangeChange(event) {
  const selectedRange = event.target.value;
  console.log("Date range changed to:", selectedRange);

  // Show loading state
  showLoadingState();

  // Simulate API call delay
  setTimeout(() => {
    updateChartsWithNewData(selectedRange);
    hideLoadingState();
  }, 1000);
}

// Handle category filter change
function handleCategoryFilterChange(event) {
  const selectedCategory = event.target.value;
  console.log("Category filter changed to:", selectedCategory);

  // Show loading state
  showLoadingState();

  // Simulate API call delay
  setTimeout(() => {
    updateChartsWithCategoryFilter(selectedCategory);
    hideLoadingState();
  }, 800);
}

// Show loading state
function showLoadingState() {
  const charts = document.querySelectorAll("canvas");
  charts.forEach((chart) => {
    chart.style.opacity = "0.5";
  });
}

// Hide loading state
function hideLoadingState() {
  const charts = document.querySelectorAll("canvas");
  charts.forEach((chart) => {
    chart.style.opacity = "1";
  });
}

// Update charts with new data based on date range
function updateChartsWithNewData(dateRange) {
  // This would typically make an API call to fetch new data
  // For demo purposes, we'll just log the change
  console.log("Charts updated for date range:", dateRange);

  // You would update the chart data here
  // Example: salesChart.data.datasets[0].data = newData;
  // salesChart.update();
}

// Update charts with category filter
function updateChartsWithCategoryFilter(category) {
  // This would typically filter the data based on category
  // For demo purposes, we'll just log the change
  console.log("Charts filtered by category:", category);

  // You would filter and update the chart data here
}

// Export functionality
document.addEventListener("click", function (event) {
  if (
    event.target.closest("button") &&
    event.target.closest("button").textContent.includes("Export Report")
  ) {
    handleExportReport();
  }
});

function handleExportReport() {
  // Show export modal or trigger download
  console.log("Exporting analytics report...");

  // Simulate export process
  const button = document.querySelector('button:has([data-lucide="download"])');
  const originalText = button.innerHTML;

  button.innerHTML =
    '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Exporting...';
  button.disabled = true;

  setTimeout(() => {
    button.innerHTML = originalText;
    button.disabled = false;

    // Re-initialize Lucide icons
    lucide.createIcons();

    // Show success message
    showNotification("Report exported successfully!", "success");
  }, 2000);
}

// Notification system
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

  const bgColor =
    type === "success"
      ? "bg-green-500"
      : type === "error"
      ? "bg-red-500"
      : "bg-blue-500";
  notification.className += ` ${bgColor} text-white`;

  notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i data-lucide="${
              type === "success"
                ? "check-circle"
                : type === "error"
                ? "x-circle"
                : "info"
            }" class="w-5 h-5"></i>
            <span>${message}</span>
        </div>
    `;

  document.body.appendChild(notification);
  lucide.createIcons();

  // Animate in
  setTimeout(() => {
    notification.classList.remove("translate-x-full");
  }, 100);

  // Animate out and remove
  setTimeout(() => {
    notification.classList.add("translate-x-full");
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Real-time updates simulation
function simulateRealTimeUpdates() {
  setInterval(() => {
    // Update KPI cards with new values
    updateKPICards();
  }, 30000); // Update every 30 seconds
}

function updateKPICards() {
  const kpiCards = document.querySelectorAll(".grid .bg-white");

  kpiCards.forEach((card, index) => {
    const valueElement = card.querySelector(".text-2xl");
    if (valueElement) {
      // Simulate small changes in values
      const currentValue = valueElement.textContent;
      // Add subtle animation or value updates here
    }
  });
}

// Initialize real-time updates
simulateRealTimeUpdates();

