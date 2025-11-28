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
                sans: ['Inter var', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Deep Ocean Blues - Primary brand color
                ocean: {
                    50: '#e6f3ff',
                    100: '#cce7ff',
                    200: '#99cfff',
                    300: '#66b7ff',
                    400: '#339fff',
                    500: '#0087ff', // Primary ocean blue
                    600: '#006ccc',
                    700: '#005199',
                    800: '#003666',
                    900: '#001b33',
                    950: '#000d1a',
                },
                // Sky Gradients - Secondary peaceful tones
                sky: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#b9e5fe',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                // Growth Greens - Success & aspiration
                growth: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981', // Primary growth green
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                },
                // Sunrise Warmth - CTAs & energy
                sunrise: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316', // Primary sunrise orange
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                // Gold Accents - Premium & achievement
                gold: {
                    50: '#fefce8',
                    100: '#fef9c3',
                    200: '#fef08a',
                    300: '#fde047',
                    400: '#facc15',
                    500: '#eab308', // Primary gold
                    600: '#ca8a04',
                    700: '#a16207',
                    800: '#854d0e',
                    900: '#713f12',
                },
                // Cultural Accent - Bangladesh heritage
                heritage: {
                    50: '#fdf2f8',
                    100: '#fce7f3',
                    200: '#fbcfe8',
                    300: '#f9a8d4',
                    400: '#f472b6',
                    500: '#ec4899',
                    600: '#db2777', // Deep Bangladesh red tone
                    700: '#be185d',
                    800: '#9f1239',
                    900: '#831843',
                },
            },
            spacing: {
                // Rhythmic spacing scale (base 4px for visual beat)
                'rhythm-xs': '0.25rem',    // 4px
                'rhythm-sm': '0.5rem',     // 8px
                'rhythm-md': '1rem',       // 16px
                'rhythm-lg': '1.5rem',     // 24px
                'rhythm-xl': '2rem',       // 32px
                'rhythm-2xl': '3rem',      // 48px
                'rhythm-3xl': '4rem',      // 64px
                'rhythm-4xl': '6rem',      // 96px
                'rhythm-5xl': '8rem',      // 128px
            },
            fontSize: {
                // Typography with vertical rhythm
                'display-2xl': ['4.5rem', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '700' }],
                'display-xl': ['3.75rem', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '700' }],
                'display-lg': ['3rem', { lineHeight: '1.2', letterSpacing: '-0.01em', fontWeight: '700' }],
                'display-md': ['2.25rem', { lineHeight: '1.3', letterSpacing: '-0.01em', fontWeight: '600' }],
                'display-sm': ['1.875rem', { lineHeight: '1.4', fontWeight: '600' }],
            },
            lineHeight: {
                'rhythm': '1.75',  // Golden ratio for reading rhythm
            },
            borderRadius: {
                'rhythm-sm': '0.5rem',   // 8px
                'rhythm-md': '0.75rem',  // 12px
                'rhythm-lg': '1rem',     // 16px
                'rhythm-xl': '1.5rem',   // 24px
                'rhythm-2xl': '2rem',    // 32px
            },
            boxShadow: {
                // Rhythmic shadows for depth
                'rhythm-sm': '0 2px 8px rgba(0, 0, 0, 0.05)',
                'rhythm': '0 4px 16px rgba(0, 0, 0, 0.08)',
                'rhythm-lg': '0 8px 24px rgba(0, 0, 0, 0.12)',
                'rhythm-xl': '0 12px 32px rgba(0, 0, 0, 0.15)',
                'glow-ocean': '0 0 24px rgba(0, 135, 255, 0.3)',
                'glow-growth': '0 0 24px rgba(16, 185, 129, 0.3)',
                'glow-sunrise': '0 0 24px rgba(249, 115, 22, 0.3)',
            },
            animation: {
                // Smooth, rhythmic animations
                'fade-in': 'fadeIn 0.6s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'fade-in-down': 'fadeInDown 0.6s ease-out',
                'slide-in-right': 'slideInRight 0.5s ease-out',
                'slide-in-left': 'slideInLeft 0.5s ease-out',
                'scale-in': 'scaleIn 0.4s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'wave': 'wave 2.5s ease-in-out infinite',
                'float': 'float 3s ease-in-out infinite',
                'blob': 'blob 7s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(-20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                wave: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                blob: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                },
            },
            backdropBlur: {
                xs: '2px',
            },
            transitionDuration: {
                '400': '400ms',
            },
            transitionTimingFunction: {
                'rhythm': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
        },
    },

    plugins: [
        forms,
        // Custom plugin for rhythm utilities
        function({ addUtilities }) {
            const rhythmUtilities = {
                '.text-rhythm': {
                    lineHeight: '1.75',
                    letterSpacing: '0.01em',
                },
                '.section-rhythm': {
                    paddingTop: '4rem',
                    paddingBottom: '4rem',
                },
                '.card-rhythm': {
                    padding: '1.5rem',
                    borderRadius: '1rem',
                    boxShadow: '0 4px 16px rgba(0, 0, 0, 0.08)',
                    transition: 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
                },
                '.gradient-ocean': {
                    background: 'linear-gradient(135deg, #0087ff 0%, #006ccc 100%)',
                },
                '.gradient-sky': {
                    background: 'linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%)',
                },
                '.gradient-growth': {
                    background: 'linear-gradient(135deg, #10b981 0%, #047857 100%)',
                },
                '.gradient-sunrise': {
                    background: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                },
                '.gradient-twilight': {
                    background: 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
                },
            };
            addUtilities(rhythmUtilities);
        },
    ],
};
