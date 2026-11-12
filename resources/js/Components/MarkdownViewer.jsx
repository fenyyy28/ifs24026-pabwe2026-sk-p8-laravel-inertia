import { lazy, Suspense } from 'react';

const MarkdownPreview = lazy(() => import('@uiw/react-markdown-preview'));

export default function MarkdownViewer({ value }) {
    return (
        <div data-color-mode="light">
            <Suspense
                fallback={
                    <div className="min-h-16 text-sm text-slate-400">
                        Memuat konten…
                    </div>
                }
            >
                <MarkdownPreview source={value || ''} />
            </Suspense>
        </div>
    );
}
