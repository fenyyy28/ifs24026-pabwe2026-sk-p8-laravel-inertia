import { useEffect, useRef } from 'react';

/** Editor dimuat dinamis; hanya diunduh saat modal tulis/ubah dibuka. */
export default function MarkdownEditor({ value, onChange, height = '420px' }) {
    const el = useRef(null);
    const onChangeRef = useRef(onChange);
    onChangeRef.current = onChange;

    useEffect(() => {
        let editor;
        let cancelled = false;

        Promise.all([
            import('@toast-ui/editor'),
            import('@toast-ui/editor/dist/toastui-editor.css'),
        ]).then(([mod]) => {
            if (cancelled || !el.current) return;
            const Editor = mod.default;
            editor = new Editor({
                el: el.current,
                height,
                initialEditType: 'markdown',
                previewStyle: 'vertical',
                initialValue: value || '',
                events: { change: () => onChangeRef.current(editor.getMarkdown()) },
            });
        });

        return () => {
            cancelled = true;
            editor?.destroy();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return <div ref={el} className="min-h-24" />;
}
