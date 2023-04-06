/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {
      screens: {
        'xs': '420px'
      },
      colors: {
        navy: {
          50: '#8fb3d7',
          100: '#78a4cf',
          200: '#6295c7',
          300: '#4b85bf',
          400: '#3576b7',
          500: '#1e67af',
          600: '#1b5d9e',
          700: '#18528c',
          800: '#15487a',
          900: '#123e69'
        }
      }
    },
  },
  plugins: [],
}
