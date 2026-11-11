import { useForm, usePage } from '@inertiajs/react';
import MarkdownEditor from './MarkdownEditor';
import Modal from './Modal';

export default function PostFormModal({ open, onClose, post = null }) {
    const { categories } = usePage().props;
    const editing = Boolean(post);

    const form = useForm({
        title: post?.title ?? '',
        category: post?.category ?? 'Umum',
        excerpt: post?.excerpt ?? '',
        content: post?.content ?? '',
        is_published: post?.is_published ?? false,
        cover: null,
    });

    const submit = (e) => {
        e.preventDefault();
        const options = { forceFormData: true, preserveScroll: true, onSuccess: () => { form.reset(); onClose(); } };
        if (editing) {
            form.transform((d) => ({ ...d, _method: 'put' })).post(`/dashboard/posts/${post.id}`, options);
        } else {
            form.transform((d) => d).post('/dashboard/posts', options);
        }
    };

    const field = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';
    const err = (k) => form.errors[k] && <p className="mt-1 text-xs text-rose-600">{form.errors[k]}</p>;

    return (
        <Modal
            open={open}
            onClose={onClose}
            title={editing ? 'Ubah Artikel' : 'Tulis Artikel Baru'}
            formId="post-form"
            processing={form.processing}
            submitLabel="Simpan"
            headerClass={editing ? 'bg-amber-600 text-white' : 'bg-indigo-700 text-white'}
            footerClass={editing ? 'bg-amber-50 border-amber-200' : 'bg-indigo-50 border-indigo-200'}
            submitClass={editing ? 'bg-amber-600 hover:bg-amber-700' : 'bg-indigo-600 hover:bg-indigo-700'}
        >
            <form id="post-form" onSubmit={submit} className="space-y-4">
                <div>
                    <label className="text-sm font-medium" htmlFor="pf-title">Judul</label>
                    <input id="pf-title" className={field} value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                    {err('title')}
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label className="text-sm font-medium" htmlFor="pf-category">Kategori</label>
                        <select id="pf-category" className={field} value={form.data.category} onChange={(e) => form.setData('category', e.target.value)}>
                            {categories.map((c) => <option key={c}>{c}</option>)}
                        </select>
                        {err('category')}
                    </div>
                    <div>
                        <label className="text-sm font-medium" htmlFor="pf-cover">Foto Sampul</label>
                        <input id="pf-cover" type="file" accept="image/*" className={field} onChange={(e) => form.setData('cover', e.target.files[0] ?? null)} />
                        {err('cover')}
                    </div>
                </div>
                <div>
                    <label className="text-sm font-medium" htmlFor="pf-excerpt">Ringkasan (opsional)</label>
                    <textarea id="pf-excerpt" rows={2} className={field} value={form.data.excerpt} onChange={(e) => form.setData('excerpt', e.target.value)} />
                    {err('excerpt')}
                </div>
                <div>
                    <span className="text-sm font-medium">Isi Artikel (Markdown)</span>
                    <div className="mt-1"><MarkdownEditor value={form.data.content} onChange={(v) => form.setData('content', v)} /></div>
                    {err('content')}
                </div>
                <label className="flex items-center gap-2 text-sm">
                    <input type="checkbox" checked={form.data.is_published} onChange={(e) => form.setData('is_published', e.target.checked)} />
                    Publikasikan ke publik
                </label>
            </form>
        </Modal>
    );
}
