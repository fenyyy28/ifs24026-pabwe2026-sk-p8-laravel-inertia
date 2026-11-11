import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Flash() {
    const { flash } = usePage().props;
    const [msg, setMsg] = useState(null);

    useEffect(() => {
        if (flash?.success) setMsg({ type: 'success', text: flash.success });
        else if (flash?.error) setMsg({ type: 'error', text: flash.error });
        else return;
        const t = setTimeout(() => setMsg(null), 4500);
        return () => clearTimeout(t);
    }, [flash?.success, flash?.error]);

    if (!msg) return null;

    const color = msg.type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200';

    return (
        <div role="status" className={`mb-4 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm ${color}`}>
            <span>{msg.text}</span>
            <button type="button" onClick={() => setMsg(null)} aria-label="Tutup" className="font-bold">×</button>
        </div>
    );
}
