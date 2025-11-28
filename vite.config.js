import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    build: {
        // Code splitting for better performance
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'vue',
                        '@inertiajs/vue3',
                    ],
                    'heroicons': ['@heroicons/vue/24/outline', '@heroicons/vue/24/solid'],
                },
            },
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Use esbuild for faster minification
        minify: 'esbuild',
        // Source maps for debugging (disable in production)
        sourcemap: false,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: [
            'vue',
            '@inertiajs/vue3',
            '@heroicons/vue/24/outline',
            '@heroicons/vue/24/solid',
        ],
    },
});
