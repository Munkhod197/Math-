/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        navy: '#0F2044',
        brand: '#2563EB',
        gold: '#F59E0B',
        ruby: '#E11D48',
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'sans-serif'],
      },
    },
  },
  safelist: [
    { pattern: /^(bg|text|border)-(navy|brand|gold|ruby|slate|white|amber)(?:-(?:50|100|200|300|400|500|600|700|800|900))?(?:\/\d+)?$/ },
  ],
  plugins: [],
}

