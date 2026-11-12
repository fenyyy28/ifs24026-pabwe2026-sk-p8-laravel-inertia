import { Link } from '@inertiajs/react';

export default function Pagination({ paginator }) {
    if (!paginator || paginator.last_page <= 1) return null;

    const base = 'rounded-lg border px-3 py-1.5 text-sm';

    return (
        <nav className="mt-6 flex items-center justify-between" aria-label="Paginasi">
            {paginator.prev_page_url ? (
                <Link href={paginator.prev_page_url} className={`${base} border-slate-300 bg-white hover:bg-slate-50`}>← Sebelumnya</Link>
            ) : <span className={`${base} border-transparent text-slate-500`}>← Sebelumnya</span>}
            <span className="text-sm text-slate-600">Halaman {paginator.current_page} dari {paginator.last_page}</span>
            {paginator.next_page_url ? (
                <Link href={paginator.next_page_url} className={`${base} border-slate-300 bg-white hover:bg-slate-50`}>Berikutnya →</Link>
            ) : <span className={`${base} border-transparent text-slate-500`}>Berikutnya →</span>}
        </nav>
    );
}
