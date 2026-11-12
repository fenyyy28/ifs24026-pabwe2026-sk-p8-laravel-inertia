import { router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import Modal from './Modal';
import { Spinner } from './LoadingIndicator';

export default function AiGeneratorModal({ open, onClose, maxAi }) {
    const { flash } = usePage().props;
    const form = useForm({ topic: '', count: 1 });
    const [items, setItems] = useState([]);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (flash?.ai_items) setItems(flash.ai_items);
    }, [flash?.ai_items]);

    const generate = (e) => {
        e.preventDefault();
        form.post('/dashboard/posts/ai-generate', { preserveScroll: true });
    };

    const saveAll = () => {
        router.post('/dashboard/posts/ai-save', { items }, {
            preserveScroll: true,
            onStart: () => setSaving(true),
            onFinish: () => setSaving(false),
            onSuccess: () => { setItems([]); onClose(); },
        });
    };

    return (
        <Modal
            open={open}
            onClose={onClose}
            title="AI Generator Artikel"
            formId="ai-form"
            submitLabel="Hasilkan"
            processing={form.processing}
            headerClass="bg-violet-700 text-white"
            footerClass="bg-violet-50 border-violet-200"
            submitClass="bg-violet-700 hover:bg-violet-800"
        >
            <form id="ai-form" onSubmit={generate} className="grid gap-4 sm:grid-cols-[1fr_8rem]">
                <div>
                    <label htmlFor="ai-topic" className="text-sm font-medium">Topik</label>
                    <input id="ai-topic" className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value={form.data.topic} onChange={(e) => form.setData('topic', e.target.value)} placeholder="mis. Tips belajar Laravel untuk pemula" />
                    {form.errors.topic && <p className="mt-1 text-xs text-rose-600">{form.errors.topic}</p>}
                </div>
                <div>
                    <label htmlFor="ai-count" className="text-sm font-medium">Jumlah</label>
                    <input id="ai-count" type="number" min={1} max={maxAi} className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value={form.data.count} onChange={(e) => form.setData('count', e.target.value)} />
                    {form.errors.count && <p className="mt-1 text-xs text-rose-600">{form.errors.count}</p>}
                </div>
            </form>

            {items.length > 0 && (
                <div className="mt-8">
                    <div className="mb-3 flex items-center justify-between">
                        <h3 className="font-semibold">Hasil ({items.length} artikel)</h3>
                        <button type="button" onClick={saveAll} disabled={saving} className="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-60">
                            {saving && <Spinner />} Simpan Semua sebagai Draf
                        </button>
                    </div>
                    <ul className="space-y-3">
                        {items.map((it, i) => (
                            <li key={i} className="rounded-lg border border-slate-200 p-4">
                                <div className="font-medium">{it.title}</div>
                                <div className="text-xs text-violet-800">{it.category}</div>
                                <p className="mt-1 text-sm text-slate-600">{it.excerpt}</p>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </Modal>
    );
}
