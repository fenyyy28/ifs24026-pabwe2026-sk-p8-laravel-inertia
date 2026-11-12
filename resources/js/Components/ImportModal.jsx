import { useForm } from '@inertiajs/react';
import Modal from './Modal';

export default function ImportModal({ open, onClose }) {
    const form = useForm({ file: null });

    const submit = (e) => {
        e.preventDefault();
        form.post('/dashboard/posts/import', { forceFormData: true, onSuccess: () => { form.reset(); onClose(); } });
    };

    return (
        <Modal open={open} onClose={onClose} title="Impor Artikel dari Excel" formId="import-form" submitLabel="Impor" processing={form.processing}
            headerClass="bg-teal-700 text-white" footerClass="bg-teal-50 border-teal-200" submitClass="bg-teal-700 hover:bg-teal-800">
            <form id="import-form" onSubmit={submit} className="space-y-3">
                <p className="text-sm text-slate-600">Gunakan berkas <b>.xlsx</b> dengan kolom: <code>Judul, Kategori, Ringkasan, Konten, Status</code> (Status: Published / Draft). Tips: ekspor dulu untuk mendapatkan templatnya.</p>
                <input type="file" accept=".xlsx,.xls" onChange={(e) => form.setData('file', e.target.files[0] ?? null)} className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                {form.errors.file && <p className="text-xs text-rose-600">{form.errors.file}</p>}
            </form>
        </Modal>
    );
}
