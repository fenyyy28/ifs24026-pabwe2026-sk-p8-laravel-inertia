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
    esbuild: {
        drop: ['console', 'debugger'],
        legalComments: 'none',
    },
    build: {
        target: 'es2022',
        sourcemap: true,
        chunkSizeWarningLimit: 1500,
        cssMinify: true,
        modulePreload: {
            polyfill: false,
        },
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        // Hanya library dasar (Inertia + React) yang dimasukkan ke chunk core
                        if (
                            id.includes('/react/') ||
                            id.includes('/react-dom/') ||
                            id.includes('/scheduler/') ||
                            id.includes('/@inertiajs/')
                        ) {
                            return 'vendor-core';
                        }
                    }
                },
            },
        },
    },
    server: {
        watch: { ignored: ['**/storage/framework/views/**'] },
    },
});
