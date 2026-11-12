import { useEffect, useRef } from 'react';

/** Viewer dimuat dinamis supaya library editor tidak ikut bundle awal. */
export default function MarkdownViewer({ value }) {
    const el = useRef(null);

    useEffect(() => {
        let viewer;
        let cancelled = false;

        Promise.all([
            import('@toast-ui/editor/dist/toastui-editor-viewer'),
            import('@toast-ui/editor/dist/toastui-editor-viewer.css'),
        ]).then(([mod]) => {
            if (cancelled || !el.current) return;
            const Viewer = mod.default;
            viewer = new Viewer({ el: el.current, initialValue: value || '' });
        });

        return () => {
            cancelled = true;
            viewer?.destroy();
        };
    }, [value]);

    return <div ref={el} className="min-h-24" />;
}
