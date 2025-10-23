import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Add our custom fonts
                primary: ['"Public Sans"', ...defaultTheme.fontFamily.sans],
                bengali: ['"Noto Sans Bengali"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Add our custom brand color palette
                'brand-primary': '#d32f2f',
                'brand-secondary': '#388e3c',
                'brand-dark': '#333333',
                'brand-light': '#f8f9fa',
                'brand-border': '#e0e0e0',
                'brand-info': '#00796b',
                'brand-warning': '#ffb300',
            },
        },
    },

    plugins: [forms],
};