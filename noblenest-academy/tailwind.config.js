/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/View/**/*.php',
        './app/Http/**/*.php',
    ],
    plugins: [
        require('tailwindcss-rtl'),
    ],
};
