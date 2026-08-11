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
        // Teal from the SACS logo — the vibrant secondary.
        secondary: {
          DEFAULT: '#14B8A6',
          light: '#5EEAD4',
          dark: '#0D9488',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      backgroundImage: {
        'brand-gradient': 'linear-gradient(135deg, #1E3A5F 0%, #2563EB 55%, #14B8A6 100%)',
        'brand-gradient-r': 'linear-gradient(90deg, #3B82F6 0%, #14B8A6 100%)',
      },
      boxShadow: {
        card: '0 4px 20px -2px rgba(16, 24, 40, 0.08)',
        'card-hover': '0 16px 40px -6px rgba(30, 58, 95, 0.22)',
        glow: '0 10px 30px -6px rgba(59, 130, 246, 0.5)',
      },
      keyframes: {
        'fade-up': {
          '0%': { opacity: '0', transform: 'translateY(10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
      animation: {
        'fade-up': 'fade-up 0.5s ease-out both',
      },
    },
  },
  plugins: [],
};