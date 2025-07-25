document?.addEventListener("DOMContentLoaded", function () {
  lucide.createIcons();
});

// Back button functionality
document.getElementById("backBtn").addEventListener("click", function () {
  this.style.transform = "scale(0.95)";
  setTimeout(() => {
    this.style.transform = "scale(1)";
    window.history.back();
  }, 150);
});
