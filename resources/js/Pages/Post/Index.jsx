import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AiGeneratorModal from '../../Components/AiGeneratorModal';
import FilterBar from '../../Components/FilterBar';
import ImportModal from '../../Components/ImportModal';
import Pagination from '../../Components/Pagination';
import PostFormModal from '../../Components/PostFormModal';
import PostList from '../../Components/PostList';
import StatCards from '../../Components/StatCards';
import AppLayout from '../../Layouts/AppLayout';

export default function Index({ posts, stats, filters, maxAi }) {
    const [modal, setModal] = useState(null); // 'create' | 'ai' | 'import' | {edit: post}

    const close = () => setModal(null);
    const remove = (post) => {
        if (window.confirm(`Hapus artikel "${post.title}"?`)) router.delete(`/dashboard/posts/${post.id}`, { preserveScroll: true });
    };
    const toggle = (post) => router.patch(`/dashboard/posts/${post.id}/publish`, {}, { preserveScroll: true });

    const btn = 'rounded-lg px-4 py-2 text-sm font-medium';

    return (
        <>
            <Head title="Artikel Saya" />
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 className="font-serif text-2xl font-bold">Artikel Saya</h1>
                <div className="flex flex-wrap gap-2">
                    <button type="button" onClick={() => setModal('create')} className={`${btn} bg-indigo-600 text-white hover:bg-indigo-700`}>+ Tulis Artikel</button>
                    <button type="button" onClick={() => setModal('ai')} className={`${btn} bg-violet-700 text-white hover:bg-violet-800`}>✨ AI Generator</button>
                    <button type="button" onClick={() => setModal('import')} className={`${btn} border border-slate-300 bg-white hover:bg-slate-50`}>Impor Excel</button>
                    <a href="/dashboard/posts/export" className={`${btn} border border-slate-300 bg-white hover:bg-slate-50`}>Ekspor Excel</a>
                </div>
            </div>

            <StatCards stats={stats} />
            <FilterBar url="/dashboard/posts" filters={filters} withStatus />
            <PostList posts={posts} detailHref={(p) => `/dashboard/posts/${p.id}`} onEdit={(p) => setModal({ edit: p })} onDelete={remove} onToggle={toggle} />
            <Pagination paginator={posts} />

            <PostFormModal key="create" open={modal === 'create'} onClose={close} />
            {modal?.edit && <PostFormModal key={`edit-${modal.edit.id}`} open onClose={close} post={modal.edit} />}
            <AiGeneratorModal open={modal === 'ai'} onClose={close} maxAi={maxAi} />
            <ImportModal open={modal === 'import'} onClose={close} />
        </>
    );
}

Index.layout = (page) => <AppLayout>{page}</AppLayout>;
