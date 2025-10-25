/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.php",
    "./public/**/*.php",
    "./public/**/*.html",
    "./public/**/*.js",
    "./src/**/*.php",
    "./src/**/*.js",
  ],
  safelist: [
    // Classes toggled only via JS should be safelisted so they are present in the build
    'border-gray-300',
    'text-gray-700',
    'hover:bg-gray-50',
    'border-[#ff6347]',
    'text-[#ff6347]',
    'bg-[#ff6347]/10',
    // Arbitrary values and states we use across pages
    'rounded-[15px]',
    'hover:bg-[#e5573e]',
    'hover:bg-[#ff6347]/10',
    'active:translate-y-px',
    'hover:underline',
    'focus:ring-2',
    'focus:ring-[#ff6347]/40',
    'opacity-0',
    'opacity-100',
    'translate-y-2',
    'translate-y-0',
    'pointer-events-none',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};