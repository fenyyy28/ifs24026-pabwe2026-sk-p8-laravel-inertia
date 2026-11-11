import { Head } from '@inertiajs/react';
import FilterBar from '../../Components/FilterBar';
import Pagination from '../../Components/Pagination';
import PostList from '../../Components/PostList';
import AppLayout from '../../Layouts/AppLayout';

export default function AdminPostList({ posts, filters }) {
    return (
        <>
            <Head title="Semua Artikel" />
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 className="font-serif text-2xl font-bold">Semua Artikel (read-only)</h1>
                <a href="/admin/posts/export" className="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50">Ekspor Excel</a>
            </div>
            <FilterBar url="/admin/posts" filters={filters} withStatus />
            <PostList posts={posts} showAuthor />
            <Pagination paginator={posts} />
        </>
    );
}

AdminPostList.layout = (page) => <AppLayout>{page}</AppLayout>;
