import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import MarkdownViewer from '../../Components/MarkdownViewer';
import PostFormModal from '../../Components/PostFormModal';
import { StatusBadge, fmtDate } from '../../Components/PostList';
import AppLayout from '../../Layouts/AppLayout';

export default function Show({ post }) {
    const [editing, setEditing] = useState(false);

    const removeCover = () => router.delete(`/dashboard/posts/${post.id}/cover`, { preserveScroll: true });
    const uploadCover = (e) => {
        const file = e.target.files[0];
        if (file) router.post(`/dashboard/posts/${post.id}/cover`, { cover: file }, { forceFormData: true, preserveScroll: true });
    };

    return (
        <>
            <Head title={post.title} />
            <Link href="/dashboard/posts" className="text-sm text-indigo-600 hover:underline">← Kembali</Link>
            <div className="mt-3 flex flex-wrap items-start justify-between gap-3">
                <h1 className="font-serif text-3xl font-bold">{post.title}</h1>
                <button type="button" onClick={() => setEditing(true)} className="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Ubah</button>
            </div>
            <div className="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <span className="rounded bg-indigo-50 px-2 py-0.5 text-indigo-700">{post.category}</span>
                <StatusBadge published={post.is_published} />
                <span>Dibuat {fmtDate(post.created_at)}</span>
                {post.is_published && <Link href={`/articles/${post.slug}`} className="text-indigo-600 hover:underline">Lihat di publik</Link>}
            </div>

            <div className="mt-6">
                {post.cover_url && <img src={post.cover_url} alt="" className="max-h-80 w-full rounded-xl object-cover" />}
                <div className="mt-3 flex items-center gap-3 text-sm">
                    <label className="cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-1.5 hover:bg-slate-50">
                        {post.cover_url ? 'Ganti sampul' : 'Unggah sampul'}
                        <input type="file" accept="image/*" className="hidden" onChange={uploadCover} />
                    </label>
                    {post.cover_url && <button type="button" onClick={removeCover} className="text-red-600 hover:underline">Hapus sampul</button>}
                </div>
            </div>

            <div className="mt-8 rounded-xl border border-slate-200 bg-white p-6"><MarkdownViewer value={post.content} /></div>
            {editing && <PostFormModal open onClose={() => setEditing(false)} post={post} />}
        </>
    );
}

Show.layout = (page) => <AppLayout>{page}</AppLayout>;
