import { Spinner } from './LoadingIndicator';

/**
 * Modal Full Screen dengan header & footer berwarna kontras.
 * Tombol aksi (Batal & Simpan) berada di sebelah kanan footer.
 */
export default function Modal({
    open,
    onClose,
    title,
    children,
    formId,
    submitLabel = 'Simpan',
    processing = false,
    headerClass = 'bg-indigo-700 text-white',
    footerClass = 'bg-indigo-50 border-indigo-200',
    submitClass = 'bg-indigo-600 hover:bg-indigo-700',
    hideSubmit = false,
}) {
    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex flex-col bg-white" role="dialog" aria-modal="true">
            <header className={`flex items-center justify-between px-6 py-4 ${headerClass}`}>
                <h2 className="text-lg font-semibold">{title}</h2>
                <button type="button" onClick={onClose} aria-label="Tutup" className="text-2xl leading-none">×</button>
            </header>
            <div className="flex-1 overflow-y-auto p-6">
                <div className="mx-auto max-w-4xl">{children}</div>
            </div>
            <footer className={`flex items-center justify-end gap-3 border-t px-6 py-4 ${footerClass}`}>
                <button type="button" onClick={onClose} className="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                {!hideSubmit && (
                    <button
                        type="submit"
                        form={formId}
                        disabled={processing}
                        className={`inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white disabled:opacity-60 ${submitClass}`}
                    >
                        {processing && <Spinner />}
                        {submitLabel}
                    </button>
                )}
            </footer>
        </div>
    );
}
