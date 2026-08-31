import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import animate from 'tailwindcss-animate';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,jsx,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                border: '#e2e8f0',
                input: '#e2e8f0',
                ring: '#6366f1',
                background: '#ffffff',
                foreground: '#0f172a',
                primary: {
                    DEFAULT: 'hsl(var(--primary, 221.2 83.2% 53.3%))',
                    foreground: 'hsl(var(--primary-foreground, 210 40% 98%))',
                },
                secondary: {
                    DEFAULT: 'hsl(var(--secondary, 210 40% 96.1%))',
                    foreground: 'hsl(var(--secondary-foreground, 222.2 47.4% 11.2%))',
                },
                destructive: {
                    DEFAULT: 'hsl(var(--destructive, 0 84.2% 60.2%))',
                    foreground: 'hsl(var(--destructive-foreground, 210 40% 98%))',
                },
                muted: {
                    DEFAULT: 'hsl(var(--muted, 210 40% 96.1%))',
                    foreground: 'hsl(var(--muted-foreground, 215.4 16.3% 46.9%))',
                },
                accent: {
                    DEFAULT: 'hsl(var(--accent, 210 40% 96.1%))',
                    foreground: 'hsl(var(--accent-foreground, 222.2 47.4% 11.2%))',
                },
                popover: {
                    DEFAULT: 'hsl(var(--popover, 0 0% 100%))',
                    foreground: 'hsl(var(--popover-foreground, 222.2 84% 4.9%))',
                },
                card: {
                    DEFAULT: 'hsl(var(--card, 0 0% 100%))',
                    foreground: 'hsl(var(--card-foreground, 222.2 84% 4.9%))',
                },
            },
            borderRadius: {
                lg: 'var(--radius, 0.5rem)',
                md: 'calc(var(--radius, 0.5rem) - 2px)',
                sm: 'calc(var(--radius, 0.5rem) - 4px)',
            },
        },
    },
    plugins: [forms, animate],
};
