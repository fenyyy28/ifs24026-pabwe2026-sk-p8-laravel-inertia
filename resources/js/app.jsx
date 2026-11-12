import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

// Halaman dimuat eager (satu berkas, tanpa rantai request) agar render pertama cepat.
// Hanya editor/viewer Markdown yang dimuat terpisah (lihat Components/Markdown*.jsx).
const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });

createInertiaApp({
    title: (title) => (title ? `${title} · BlogVerse Pro` : 'BlogVerse Pro'),
    resolve: (name) => {
        const page = pages[`./Pages/${name}.jsx`];
        if (!page) {
            throw new Error(`Halaman Inertia tidak ditemukan: ${name}`);
        }
        return page;
    },
    setup({ el, App, props }) {
        document.getElementById('boot')?.remove();
        createRoot(el).render(<App {...props} />);
    },
    progress: false,
});
