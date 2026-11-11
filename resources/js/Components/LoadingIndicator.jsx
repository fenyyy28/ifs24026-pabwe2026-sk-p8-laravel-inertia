import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function LoadingIndicator() {
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const off1 = router.on('start', () => setLoading(true));
        const off2 = router.on('finish', () => setLoading(false));
        return () => {
            off1();
            off2();
        };
    }, []);

    if (!loading) return null;

    return (
        <>
            <div className="fixed inset-x-0 top-0 z-[100] h-1 overflow-hidden bg-indigo-100">
                <div className="animate-top-progress h-full w-full bg-indigo-600" />
            </div>
            <div className="fixed bottom-5 right-5 z-[100] flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm text-white shadow-lg">
                <Spinner className="h-4 w-4" />
                Memproses tindakan...
            </div>
        </>
    );
}

export function Spinner({ className = 'h-4 w-4' }) {
    return (
        <svg className={`${className} animate-spin`} viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" className="opacity-25" />
            <path d="M4 12a8 8 0 018-8" stroke="currentColor" strokeWidth="4" strokeLinecap="round" />
        </svg>
    );
}
