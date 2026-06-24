module.exports = {
  content: [
    "./app/views/**/*.php",
    "./public/js/**/*.js",
    "./login.html",
    "./routes/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#0d47a1",
        secondary: "#1565c0",
        accent: "#ff6f00",
        danger: "#d32f2f",
        success: "#388e3c",
        warning: "#f57f17",
        info: "#0097a7",
      },
      boxShadow: {
        card: "0 4px 20px rgba(0, 0, 0, 0.1)",
        "card-hover": "0 8px 40px rgba(0, 0, 0, 0.15)",
      },
      fontFamily: {
        sans: ["Segoe UI", "Tahoma", "Geneva", "Verdana", "sans-serif"],
      },
    },
  },
  plugins: [],
};
