import MarkdownPreview from '@uiw/react-markdown-preview';

export default function MarkdownViewer({ value }) {
    return (
        <div data-color-mode="light">
            <MarkdownPreview source={value || ''} />
        </div>
    );
}
