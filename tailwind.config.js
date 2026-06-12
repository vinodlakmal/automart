/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: '#1b5e20',
                    light: '#2e7d32',
                    dark: '#0d3d12',
                },
            },
        },
    },
    plugins: [],
};
