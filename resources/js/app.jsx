import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

// Halaman dimuat per-rute (code splitting) agar bundle awal kecil.
const pages = import.meta.glob('./Pages/**/*.jsx');

createInertiaApp({
    title: (title) => (title ? `${title} · BlogVerse Pro` : 'BlogVerse Pro'),
    resolve: async (name) => {
        const loader = pages[`./Pages/${name}.jsx`];
        if (!loader) {
            throw new Error(`Halaman Inertia tidak ditemukan: ${name}`);
        }
        return loader();
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: false,
});
