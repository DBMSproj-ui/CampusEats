// Import the default Tailwind theme so we can extend it (e.g., fonts, spacing, etc.)
import defaultTheme from 'tailwindcss/defaultTheme';

// Import the Tailwind Forms plugin to style form inputs, checkboxes, etc.
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
// Export the Tailwind CSS configuration
export default {
    // Specify which files Tailwind should scan for class names
    // This helps purge unused CSS in production
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php', // Laravel's default pagination views
        './storage/framework/views/*.php', // Compiled Blade templates
        './resources/views/**/*.blade.php', // All Blade views in your resources folder
    ],

    theme: {
        // Extend the default Tailwind theme
        extend: {
            fontFamily: {
                // Customize the default sans-serif font stack to include 'Figtree' first
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    // Register Tailwind plugins
    plugins: [
        forms, // Adds a basic and consistent styling reset for form elements
    ],
};
