import MDEditor from '@uiw/react-md-editor';

export default function MarkdownEditor({ value, onChange, height = 420 }) {
    const heightNum = typeof height === 'string' ? parseInt(height, 10) || 420 : height;

    return (
        <div data-color-mode="light">
            <MDEditor
                value={value || ''}
                onChange={(v) => onChange(v ?? '')}
                height={heightNum}
                preview="live"
            />
        </div>
    );
}
