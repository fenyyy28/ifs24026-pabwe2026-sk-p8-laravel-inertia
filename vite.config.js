import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    build: {
        sourcemap: true,
        chunkSizeWarningLimit: 1500,
        rollupOptions: {
            output: {
                manualChunks: {
                    // React core — cached terpisah, jarang berubah
                    'vendor-react': ['react', 'react-dom'],
                    // Inertia
                    'vendor-inertia': ['@inertiajs/react'],
                    // Markdown editor (besar, lazy-loadable)
                    'vendor-md-editor': ['@uiw/react-md-editor', '@uiw/react-markdown-preview'],
                },
            },
        },
    },
    server: {
        watch: { ignored: ['**/storage/framework/views/**'] },
    },
});
