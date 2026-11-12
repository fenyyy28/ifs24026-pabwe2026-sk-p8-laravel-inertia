import { Head, Link } from '@inertiajs/react';
import FilterBar from '../../Components/FilterBar';
import Pagination from '../../Components/Pagination';
import PostList from '../../Components/PostList';
import StatCards from '../../Components/StatCards';
import AppLayout from '../../Layouts/AppLayout';

export default function UserPostList({ author, posts, stats, filters }) {
    return (
        <>
            <Head title={`Artikel ${author.name}`} />
            <Link href="/admin/users" className="text-sm text-indigo-600 hover:underline">← Pengguna</Link>
            <div className="mb-6 mt-2 flex flex-wrap items-center justify-between gap-3">
                <h1 className="font-serif text-2xl font-bold">Artikel {author.name} <span className="text-base font-normal text-slate-600">@{author.username}</span></h1>
                <a href={`/admin/users/${author.id}/posts/export`} className="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50">Ekspor Excel</a>
            </div>
            <StatCards stats={stats} />
            <FilterBar url={`/admin/users/${author.id}/posts`} filters={filters} withStatus />
            <PostList posts={posts} />
            <Pagination paginator={posts} />
        </>
    );
}

UserPostList.layout = (page) => <AppLayout>{page}</AppLayout>;
