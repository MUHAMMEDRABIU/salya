// Loading Overlay System
function createLoadingOverlay(message = "Processing...", timeout = 30000) {
  // Remove existing overlay
  const existingOverlay = document.querySelector(".loading-overlay");
  if (existingOverlay) {
    existingOverlay.remove();
  }

  // Create overlay
  const overlay = document.createElement("div");
  overlay.className =
    "loading-overlay fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center";
  overlay.style.opacity = "0";

  overlay.innerHTML = `
        <div class="loading-content bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center transform scale-95 transition-all duration-300">
            <div class="loading-spinner w-12 h-12 border-4 border-orange-200 border-t-orange-500 rounded-full animate-spin mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Please Wait</h3>
            <p class="text-gray-600 loading-message">${message}</p>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="loading-progress bg-orange-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    `;

  // Add to document
  document.body.appendChild(overlay);

  // Animate in
  setTimeout(() => {
    overlay.style.opacity = "1";
    const content = overlay.querySelector(".loading-content");
    content.classList.remove("scale-95");
    content.classList.add("scale-100");
  }, 10);

  // Start progress animation
  const progressBar = overlay.querySelector(".loading-progress");
  let progress = 0;
  const progressInterval = setInterval(() => {
    progress += Math.random() * 15;
    if (progress > 90) progress = 90; // Don't complete until manually closed
    progressBar.style.width = progress + "%";
  }, 200);

  // Auto-hide after timeout
  const timeoutId = setTimeout(() => {
    hideLoadingOverlay();
    showToasted("Operation timed out. Please try again.", "error");
  }, timeout);

  // Return control object
  return {
    updateMessage: (newMessage) => {
      const messageEl = overlay.querySelector(".loading-message");
      if (messageEl) {
        messageEl.textContent = newMessage;
      }
    },
    hide: () => {
      clearTimeout(timeoutId);
      clearInterval(progressInterval);
      hideLoadingOverlay();
    },
  };

  function hideLoadingOverlay() {
    const overlay = document.querySelector(".loading-overlay");
    if (overlay) {
      overlay.style.opacity = "0";
      const content = overlay.querySelector(".loading-content");
      content.classList.remove("scale-100");
      content.classList.add("scale-95");

      setTimeout(() => {
        if (overlay.parentNode) {
          overlay.parentNode.removeChild(overlay);
        }
      }, 300);
    }
  }
}

// Global function to hide loading overlay
function hideLoadingOverlay() {
  const overlay = document.querySelector(".loading-overlay");
  if (overlay) {
    overlay.style.opacity = "0";
    const content = overlay.querySelector(".loading-content");
    if (content) {
      content.classList.remove("scale-100");
      content.classList.add("scale-95");
    }

    setTimeout(() => {
      if (overlay.parentNode) {
        overlay.parentNode.removeChild(overlay);
      }
    }, 300);
  }
}
