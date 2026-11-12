import { lazy, Suspense } from 'react';

const MDEditor = lazy(() => import('@uiw/react-md-editor'));

export default function MarkdownEditor({ value, onChange, height = 420 }) {
    const heightNum = typeof height === 'string' ? parseInt(height, 10) || 420 : height;

    return (
        <div data-color-mode="light">
            <Suspense
                fallback={
                    <div
                        style={{ minHeight: heightNum }}
                        className="flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-400"
                    >
                        Memuat editor…
                    </div>
                }
            >
                <MDEditor
                    value={value || ''}
                    onChange={(v) => onChange(v ?? '')}
                    height={heightNum}
                    preview="live"
                />
            </Suspense>
        </div>
    );
}
