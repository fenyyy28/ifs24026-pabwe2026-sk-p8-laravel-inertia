import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function FilterBar({ url, filters, withStatus = false }) {
    const { categories } = usePage().props;
    const [values, setValues] = useState(filters);

    const submit = (next) => {
        const query = Object.fromEntries(Object.entries(next).filter(([, v]) => v));
        router.get(url, query, { preserveState: true, preserveScroll: true });
    };
    const change = (key, v) => {
        const next = { ...values, [key]: v };
        setValues(next);
        if (key !== 'search') submit(next);
    };
    const input = 'rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm';

    return (
        <form onSubmit={(e) => { e.preventDefault(); submit(values); }} className="mb-4 flex flex-wrap gap-2">
            <input type="search" placeholder="Cari judul / isi artikel…" value={values.search ?? ''} onChange={(e) => change('search', e.target.value)} className={`${input} min-w-56 flex-1`} />
            <select value={values.category ?? ''} onChange={(e) => change('category', e.target.value)} className={input} aria-label="Kategori">
                <option value="">Semua kategori</option>
                {categories.map((c) => <option key={c} value={c}>{c}</option>)}
            </select>
            {withStatus && (
                <select value={values.status ?? ''} onChange={(e) => change('status', e.target.value)} className={input} aria-label="Status">
                    <option value="">Semua status</option>
                    <option value="published">Terbit</option>
                    <option value="draft">Draf</option>
                </select>
            )}
            <button type="submit" className="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900">Cari</button>
        </form>
    );
}
