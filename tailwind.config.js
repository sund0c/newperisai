/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
safelist: [
        'bg-red-100', 'text-red-700',
        'bg-orange-100', 'text-orange-700',
        'bg-yellow-100', 'text-yellow-700',
        'bg-green-100', 'text-green-700',
        'bg-blue-100', 'text-blue-700',
        'bg-amber-100', 'text-amber-700',
        'bg-purple-100', 'text-purple-700',
        'bg-indigo-100', 'text-indigo-700',
        'bg-gray-100', 'text-gray-400',
        'bg-indigo-100', 'text-indigo-700',
  'bg-indigo-600', 'text-indigo-600',
        'bg-purple-600', 'hover:bg-purple-700',
    'bg-green-600',  'hover:bg-green-700',
    'bg-red-50',     'hover:bg-red-100',    'text-red-700',   'border-red-200',
    'bg-yellow-50',  'hover:bg-yellow-100', 'text-yellow-700','border-yellow-200',
    'bg-indigo-600', 'hover:bg-indigo-700',
    'bg-indigo-50',  'hover:bg-indigo-100', 'text-indigo-700','border-indigo-200',
    ],

}
