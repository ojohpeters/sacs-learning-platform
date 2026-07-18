import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1E3A5F',
          50: '#E8EDF5',
          100: '#C5D1E5',
          200: '#9DB3CF',
          300: '#7595B9',
          400: '#4D77A3',
          500: '#2563EB',
          600: '#1E3A5F',
          700: '#172D4A',
          800: '#102035',
          900: '#0A1420',
        },
        accent: {
          DEFAULT: '#3B82F6',
          light: '#93C5FD',
          dark: '#1D4ED8',
        },
        success: {
          DEFAULT: '#059669',
          light: '#D1FAE5',
          dark: '#047857',
        },
        warning: {
          DEFAULT: '#D97706',
          light: '#FEF3C7',
          dark: '#B45309',
        },
        danger: {
          DEFAULT: '#DC2626',
          light: '#FEE2E2',
          dark: '#B91C1C',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
    },
  },
  plugins: [],
};