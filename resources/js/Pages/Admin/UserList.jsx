import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '../../Components/Pagination';
import AppLayout from '../../Layouts/AppLayout';

export default function UserList({ users, filters }) {
    const { auth } = usePage().props;
    const [search, setSearch] = useState(filters.search);

    const find = (e) => {
        e.preventDefault();
        router.get('/admin/users', search ? { search } : {}, { preserveState: true });
    };
    const toggleRole = (u) => router.patch(`/admin/users/${u.id}/role`, { role: u.role === 'admin' ? 'member' : 'admin' }, { preserveScroll: true });
    const remove = (u) => {
        if (window.confirm(`Hapus pengguna ${u.name} beserta seluruh artikelnya?`)) router.delete(`/admin/users/${u.id}`, { preserveScroll: true });
    };

    return (
        <>
            <Head title="Pengguna" />
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 className="font-serif text-2xl font-bold">Kelola Pengguna</h1>
                <a href="/admin/posts/export" className="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50">Ekspor Semua Artikel</a>
            </div>
            <form onSubmit={find} className="mb-4 flex gap-2">
                <input type="search" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari nama / username…" className="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                <button type="submit" className="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white">Cari</button>
            </form>
            <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table className="w-full text-left text-sm">
                    <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr><th className="px-4 py-3">Nama</th><th className="px-4 py-3">Peran</th><th className="px-4 py-3">Artikel</th><th className="px-4 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {users.data.map((u) => (
                            <tr key={u.id}>
                                <td className="px-4 py-3"><div className="font-medium">{u.name}</div><div className="text-xs text-slate-500">@{u.username}</div></td>
                                <td className="px-4 py-3"><span className={`rounded-full px-2.5 py-0.5 text-xs font-medium ${u.role === 'admin' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700'}`}>{u.role}</span></td>
                                <td className="px-4 py-3">{u.published_posts_count} / {u.posts_count} terbit</td>
                                <td className="flex flex-wrap justify-end gap-3 px-4 py-3">
                                    <Link href={`/admin/users/${u.id}/posts`} className="text-indigo-600 hover:underline">Artikel</Link>
                                    <a href={`/admin/users/${u.id}/posts/export`} className="text-indigo-600 hover:underline">Ekspor</a>
                                    {u.id !== auth.user.id && (
                                        <>
                                            <button type="button" onClick={() => toggleRole(u)} className="text-amber-600 hover:underline">{u.role === 'admin' ? 'Jadikan member' : 'Jadikan admin'}</button>
                                            <button type="button" onClick={() => remove(u)} className="text-red-600 hover:underline">Hapus</button>
                                        </>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <Pagination paginator={users} />
        </>
    );
}

UserList.layout = (page) => <AppLayout>{page}</AppLayout>;
