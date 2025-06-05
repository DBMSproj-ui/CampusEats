// Import the defineConfig helper from Vite
// This provides type hints and better editor support for the config
import { defineConfig } from 'vite';

// Import the Laravel Vite plugin
// This helps Vite work seamlessly with Laravel, especially for asset bundling and hot reloading
import laravel from 'laravel-vite-plugin';

// Export the Vite configuration
export default defineConfig({
    // Vite development server settings
    server: {
        hmr: {
            // Set the host for Hot Module Replacement (HMR)
            // This ensures real-time updates work correctly during development
            host: 'localhost',
        },
    },
    // Define plugins used by Vite
    plugins: [
        laravel({
            // Specify the entry points for Vite to compile
            input: ['resources/css/app.css', 'resources/js/app.js'],
            
            // Enable full-page refresh when Blade or other files change
            refresh: true,
        }),
    ],
});
