import { Markdown } from '@uiw/react-md-editor';

export default function MarkdownViewer({ value }) {
    return (
        <div data-color-mode="light">
            <Markdown source={value || ''} />
        </div>
    );
}
