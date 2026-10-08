import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#effbfa', 100: '#d8f5f2', 200: '#b1e8e4', 300: '#73d5d0',
                    400: '#39c3be', 500: '#20B8B8', 600: '#138080',
                    700: '#106868', 800: '#125353', 900: '#124546', 950: '#082a2c',
                },
            },
        },
    },

    plugins: [forms],
};
