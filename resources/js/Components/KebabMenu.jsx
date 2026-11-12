import { Link } from '@inertiajs/react';
import { useState } from 'react';

const Eye = () => (
    <svg className="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" /><circle cx="12" cy="12" r="3" /></svg>
);
const Pencil = () => (
    <svg className="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z" /></svg>
);
const Trash = () => (
    <svg className="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 6h18M8 6V4h8v2m-9 0l1 14h8l1-14" /></svg>
);

/** Menu tindakan ⋮ : Detail (primary), Ubah (primary), Hapus (merah). */
export default function KebabMenu({ detailHref, onEdit, onDelete }) {
    const [open, setOpen] = useState(false);
    const item = 'flex w-full items-center gap-2 px-4 py-2 text-left text-sm hover:bg-slate-50';

    return (
        <div className="relative">
            <button type="button" aria-label="Menu tindakan" onClick={() => setOpen(!open)} className="rounded-full p-2 text-xl leading-none text-slate-600 hover:bg-slate-100">
                ⋮
            </button>
            {open && (
                <>
                    <div className="fixed inset-0 z-10" onClick={() => setOpen(false)} />
                    <div className="absolute right-0 z-20 mt-1 w-40 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                        <Link href={detailHref} className={`${item} text-indigo-600`}><Eye /> Detail</Link>
                        <button type="button" onClick={() => { setOpen(false); onEdit(); }} className={`${item} text-indigo-600`}><Pencil /> Ubah</button>
                        <button type="button" onClick={() => { setOpen(false); onDelete(); }} className={`${item} text-red-600`}><Trash /> Hapus</button>
                    </div>
                </>
            )}
        </div>
    );
}
