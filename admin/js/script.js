// Frozen Foods Admin Dashboard JavaScript

// Initialize Lucide icons
lucide.createIcons();

// Mobile sidebar toggle
const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const closeSidebar = document.getElementById("closeSidebar");
const overlay = document.getElementById("overlay");

menuToggle.addEventListener("click", () => {
  sidebar.classList.remove("-translate-x-full");
  overlay.classList.remove("hidden");
});

closeSidebar.addEventListener("click", () => {
  sidebar.classList.add("-translate-x-full");
  overlay.classList.add("hidden");
});

overlay.addEventListener("click", () => {
  sidebar.classList.add("-translate-x-full");
  overlay.classList.add("hidden");
});



// Utility functions
function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
        <div class="flex items-center">
            <i data-lucide="${
              type === "success"
                ? "check-circle"
                : type === "error"
                ? "x-circle"
                : "info"
            }" class="w-5 h-5 mr-2"></i>
            <span>${message}</span>
        </div>
    `;

  document.body.appendChild(notification);
  lucide.createIcons();

  setTimeout(() => {
    notification.classList.add("show");
  }, 100);

  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Search functionality
function initializeSearch() {
  const searchInput = document.querySelector('input[placeholder="Search..."]');
  if (searchInput) {
    searchInput.addEventListener("input", function (e) {
      const searchTerm = e.target.value.toLowerCase();
      // Implement search logic here
      console.log("Searching for:", searchTerm);
    });
  }
}

// Initialize search on page load
document.addEventListener("DOMContentLoaded", initializeSearch);

// Responsive table functionality
function makeTablesResponsive() {
  const tables = document.querySelectorAll("table");
  tables.forEach((table) => {
    if (!table.parentElement.classList.contains("table-responsive")) {
      const wrapper = document.createElement("div");
      wrapper.className = "table-responsive";
      table.parentNode.insertBefore(wrapper, table);
      wrapper.appendChild(table);
    }
  });
}

// Initialize auto-refresh
document.addEventListener("DOMContentLoaded", autoRefreshData);

// Format currency
function formatCurrency(amount) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(amount);
}

// Format date
function formatDate(date) {
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  }).format(new Date(date));
}

// Loading state management
function showLoading(element) {
  element.classList.add("loading");
}

function hideLoading(element) {
  element.classList.remove("loading");
}

// Export functions for use in other files
window.dashboardUtils = {
  showNotification,
  formatCurrency,
  formatDate,
  showLoading,
  hideLoading,
};


// User dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    const dropdownIcon = document.getElementById('dropdownIcon');
    let isDropdownOpen = false;

    // Toggle dropdown
    userDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleDropdown();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userDropdown.contains(e.target) && !userDropdownMenu.contains(e.target)) {
            closeDropdown();
        }
    });

    // Close dropdown on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isDropdownOpen) {
            closeDropdown();
        }
    });

    function toggleDropdown() {
        if (isDropdownOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    function openDropdown() {
        userDropdownMenu.classList.remove('opacity-0', 'invisible', 'scale-95');
        userDropdownMenu.classList.add('opacity-100', 'visible', 'scale-100');
        dropdownIcon.style.transform = 'rotate(180deg)';
        isDropdownOpen = true;
    }

    function closeDropdown() {
        userDropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
        userDropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
        dropdownIcon.style.transform = 'rotate(0deg)';
        isDropdownOpen = false;
    }
});

// Sign out modal functionality
document.addEventListener("DOMContentLoaded", function () {
    const signOutModal = document.getElementById("signOutModal");
    const cancelSignOut = document.getElementById("cancelSignOut");
    const confirmSignOut = document.getElementById("confirmSignOut");

    function handleSignOut() {
        // Show modal
        signOutModal.classList.remove("hidden");
    }

    cancelSignOut.addEventListener("click", function () {
        // Hide modal
        signOutModal.classList.add("hidden");
    });

    confirmSignOut.addEventListener("click", function () {
        // Redirect to login page
        signOutModal.classList.add("hidden");
        window.location.href = "logout.php";
    });

    // Export the function for use in other files
    window.handleSignOut = handleSignOut;
});