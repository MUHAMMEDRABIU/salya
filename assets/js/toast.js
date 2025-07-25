// Toast notification system
function showToasted(message, type = "info", duration = 5000) {
  // Remove any existing toast
  const existingToast = document.querySelector(".toast-notification");
  if (existingToast) {
    existingToast.remove();
  }

  // Create toast container
  const toast = document.createElement("div");
toast.className = `toast-notification fixed top-4 right-4 z-[9999] bg-white rounded-lg shadow-lg border-l-4 transform translate-x-full transition-all duration-300 ease-in-out`;
  // Set border color based on type
  const borderColors = {
    success: "border-green-500",
    error: "border-red-500",
    warning: "border-yellow-500",
    info: "border-blue-500",
  };

  const iconColors = {
    success: "text-green-500",
    error: "text-red-500",
    warning: "text-yellow-500",
    info: "text-blue-500",
  };

  const icons = {
    success: "check-circle",
    error: "x-circle",
    warning: "alert-triangle",
    info: "info",
  };

  toast.classList.add(borderColors[type] || borderColors.info);

  // Create toast content
  toast.innerHTML = `
        <div class="flex items-start p-4">
            <div class="flex-shrink-0">
                <i data-lucide="${icons[type] || icons.info}" class="w-5 h-5 ${
    iconColors[type] || iconColors.info
  }"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button class="toast-close inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="toast-progress absolute bottom-0 left-0 h-1 bg-gray-200 w-full">
            <div class="toast-progress-bar h-full ${
              type === "success"
                ? "bg-green-500"
                : type === "error"
                ? "bg-red-500"
                : type === "warning"
                ? "bg-yellow-500"
                : "bg-blue-500"
            } transition-all duration-${duration} ease-linear" style="width: 100%;"></div>
        </div>
    `;

  // Add to document
  document.body.appendChild(toast);

  // Initialize Lucide icons for the toast
  if (typeof lucide !== "undefined") {
    lucide.createIcons();
  }

  // Animate in
  setTimeout(() => {
    toast.classList.remove("translate-x-full");
    toast.classList.add("translate-x-0");
  }, 100);

  // Start progress bar animation
  const progressBar = toast.querySelector(".toast-progress-bar");
  setTimeout(() => {
    progressBar.style.width = "0%";
  }, 100);

  // Auto remove after duration
  const autoRemoveTimer = setTimeout(() => {
    removeToast(toast);
  }, duration);

  // Close button functionality
  const closeBtn = toast.querySelector(".toast-close");
  closeBtn.addEventListener("click", () => {
    clearTimeout(autoRemoveTimer);
    removeToast(toast);
  });

  // Remove toast function
  function removeToast(toastElement) {
    toastElement.classList.remove("translate-x-0");
    toastElement.classList.add("translate-x-full");
    setTimeout(() => {
      if (toastElement.parentNode) {
        toastElement.parentNode.removeChild(toastElement);
      }
    }, 300);
  }

  return toast;
}

// Push notification function (you can customize this based on your notification system)
function pushNotification(title, message, type = "info") {
  // Check if browser supports notifications
  if ("Notification" in window) {
    // Request permission if not granted
    if (Notification.permission === "default") {
      Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
          createNotification(title, message, type);
        }
      });
    } else if (Notification.permission === "granted") {
      createNotification(title, message, type);
    }
  }
}

function createNotification(title, message, type) {
  const notification = new Notification(title, {
    body: message,
    icon:
      type === "success"
        ? "/assets/icons/success.png"
        : type === "error"
        ? "/assets/icons/error.png"
        : "/assets/icons/info.png",
    badge: "/assets/icons/badge.png",
  });

  // Auto close after 5 seconds
  setTimeout(() => {
    notification.close();
  }, 5000);

  // Handle click
  notification.onclick = function () {
    window.focus();
    notification.close();
  };
}
