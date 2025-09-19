/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx}",
    "./pages/**/*.{js,ts,jsx,tsx}",
    "./components/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        background: "#122117",
        foreground: "#ffffff",
        success: "#38E07A",
        secondary: "#38E07A",
        accent: "#96C4A8",
        dark: "#1C3024",
        darker: "#264533",
      },
      fontFamily: {
        sans: ["Spline Sans", "Arial", "Helvetica", "sans-serif"],
      },
    },
  },
  plugins: [],
};
