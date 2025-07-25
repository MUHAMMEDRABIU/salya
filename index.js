// Mobile Navigation Toggle
document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuBtn = document.getElementById("mobile-menu-btn");
  const mobileMenu = document.getElementById("mobile-menu");

  mobileMenuBtn.addEventListener("click", function () {
    mobileMenu.classList.toggle("hidden");
  });

  // Close mobile menu when clicking on a link
  document.querySelectorAll("#mobile-menu a").forEach((link) => {
    link.addEventListener("click", function () {
      mobileMenu.classList.add("hidden");
    });
  });
});

// Testimonials Slider
document.addEventListener("DOMContentLoaded", function () {
  const testimonialTrack = document.getElementById("testimonial-track");
  const dots = document.querySelectorAll(".testimonial-dot");
  let currentSlide = 0;
  const totalSlides = 3;

  function showSlide(slideIndex) {
    const translateX = -slideIndex * 100;
    testimonialTrack.style.transform = `translateX(${translateX}%)`;

    // Update dots
    dots.forEach((dot, index) => {
      if (index === slideIndex) {
        dot.classList.remove("bg-gray-400");
        dot.classList.add("bg-accent");
      } else {
        dot.classList.remove("bg-accent");
        dot.classList.add("bg-gray-400");
      }
    });

    currentSlide = slideIndex;
  }

  // Dot navigation
  dots.forEach((dot, index) => {
    dot.addEventListener("click", function () {
      showSlide(index);
    });
  });

  // Auto-slide testimonials
  setInterval(function () {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
  }, 5000);
});

// Smooth scrolling for navigation links
document.addEventListener("DOMContentLoaded", function () {
  const navLinks = document.querySelectorAll('a[href^="#"]');

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      const targetSection = document.getElementById(targetId);

      if (targetSection) {
        const offsetTop = targetSection.offsetTop - 80;
        window.scrollTo({
          top: offsetTop,
          behavior: "smooth",
        });
      }
    });
  });
});

// Add scroll effect to navbar
window.addEventListener("scroll", function () {
  const navbar = document.querySelector("nav");
  if (window.scrollY > 50) {
    navbar.classList.add("bg-white/95", "backdrop-blur-sm");
    navbar.classList.remove("bg-white");
  } else {
    navbar.classList.add("bg-white");
    navbar.classList.remove("bg-white/95", "backdrop-blur-sm");
  }
});

// Add intersection observer for animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver(function (entries) {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = "1";
      entry.target.style.transform = "translateY(0)";
    }
  });
}, observerOptions);

// Observe elements for animation
document.addEventListener("DOMContentLoaded", function () {
  const animateElements = document.querySelectorAll(".category-card");

  animateElements.forEach((element) => {
    element.style.opacity = "0";
    element.style.transform = "translateY(20px)";
    element.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(element);
  });
});
