/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                navy: {
                    light: "#1e3a8a",
                    DEFAULT: "#1e40af",
                    dark: "#1e3a8a",
                },
                dark: "#0f172a",
            },
        },
    },
    plugins: [],
};
