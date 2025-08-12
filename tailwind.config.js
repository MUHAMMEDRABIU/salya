/** @type {import('tailwindcss').Config} */
module.exports = {
  // Using per-bundle --content globs from your npm scripts
  theme: {
    extend: {
      fontFamily: {
        dm: ["DM Sans", "system-ui", "sans-serif"],
        sans: ["DM Sans", "system-ui", "sans-serif"],
      },
      colors: {
        accent: "#F97316",
        secondary: "#ff7272",
        dark: "#201f20",
        gray: "#f6f7fc",
        "custom-gray": "#f6f7fc",
        "custom-dark": "#201f20",
      },
      boxShadow: {
        soft: "0 2px 15px rgba(0, 0, 0, 0.08)",
        medium: "0 4px 25px rgba(0, 0, 0, 0.1)",
        large: "0 8px 35px rgba(0, 0, 0, 0.12)",
        accent: "0 4px 20px rgba(249, 115, 22, 0.15)",
        glow: "0 0 30px rgba(249, 115, 22, 0.3)",
      },
      keyframes: {
        fadeIn: { "0%": { opacity: "0" }, "100%": { opacity: "1" } },
        slideUp: {
          "0%": { transform: "translateY(20px)", opacity: "0" },
          "100%": { transform: "translateY(0)", opacity: "1" },
        },
        slideDown: {
          "0%": { transform: "translateY(-20px)", opacity: "0" },
          "100%": { transform: "translateY(0)", opacity: "1" },
        },
        slideRight: {
          "0%": { transform: "translateX(-100%)", opacity: "0" },
          "100%": { transform: "translateX(0)", opacity: "1" },
        },
        slideLeft: {
          "0%": { transform: "translateX(100%)", opacity: "0" },
          "100%": { transform: "translateX(0)", opacity: "1" },
        },
        scaleIn: {
          "0%": { transform: "scale(0.9)", opacity: "0" },
          "100%": { transform: "scale(1)", opacity: "1" },
        },
        bounceGentle: {
          "0%, 20%, 50%, 80%, 100%": { transform: "translateY(0)" },
          "40%": { transform: "translateY(-10px)" },
          "60%": { transform: "translateY(-5px)" },
        },
        pulseRing: {
          "0%": { transform: "scale(.33)" },
          "80%,100%": { transform: "scale(2.33)", opacity: "0" },
        },
        float: {
          "0%,100%": { transform: "translateY(0px)" },
          "50%": { transform: "translateY(-10px)" },
        },
        wiggle: {
          "0%,100%": { transform: "rotate(-3deg)" },
          "50%": { transform: "rotate(3deg)" },
        },
        shimmer: {
          "0%": { backgroundPosition: "-200% 0" },
          "100%": { backgroundPosition: "200% 0" },
        },
        // alias names used directly in classes like 'fade-in', 'slide-up', etc.
        "fade-in": { "0%": { opacity: "0" }, "100%": { opacity: "1" } },
        "slide-up": {
          "0%": { transform: "translateY(20px)", opacity: "0" },
          "100%": { transform: "translateY(0)", opacity: "1" },
        },
        "slide-down": {
          "0%": { transform: "translateY(-20px)", opacity: "0" },
          "100%": { transform: "translateY(0)", opacity: "1" },
        },
        "scale-in": {
          "0%": { transform: "scale(0.95)", opacity: "0" },
          "100%": { transform: "scale(1)", opacity: "1" },
        },
        "bounce-gentle": {
          "0%,100%": { transform: "translateY(0)" },
          "50%": { transform: "translateY(-4px)" },
        },
      },
      animation: {
        "fade-in": "fade-in 0.4s ease-out",
        "slide-up": "slide-up 0.3s ease-out",
        "slide-down": "slide-down 0.3s ease-out",
        "slide-right": "slideRight 0.4s ease-out",
        "slide-left": "slideLeft 0.4s ease-out",
        "scale-in": "scale-in 0.2s ease-out",
        "bounce-gentle": "bounce-gentle 0.6s ease-in-out",
        "pulse-ring":
          "pulseRing 2s cubic-bezier(0.455,0.03,0.515,0.955) infinite",
        float: "float 3s ease-in-out infinite",
        wiggle: "wiggle 1s ease-in-out infinite",
        shimmer: "shimmer 2s linear infinite",
      },
      backdropBlur: { xs: "2px" },
    },
  },
  plugins: [],
};
