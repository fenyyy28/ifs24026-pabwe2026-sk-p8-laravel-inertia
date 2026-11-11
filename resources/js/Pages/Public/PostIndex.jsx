import { Head, Link, usePage } from '@inertiajs/react';
import FilterBar from '../../Components/FilterBar';
import Pagination from '../../Components/Pagination';
import { fmtDate } from '../../Components/PostList';
import PublicLayout from '../../Layouts/PublicLayout';

export default function PostIndex({ posts, filters }) {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Beranda" />
            <section className="mb-10 rounded-2xl bg-gradient-to-br from-indigo-700 to-violet-600 px-8 py-12 text-white">
                <h1 className="font-serif text-4xl font-bold">Tulisan yang layak dibaca.</h1>
                <p className="mt-3 max-w-xl text-indigo-100">
                    BlogVerse Pro adalah platform publikasi artikel: baca bebas tanpa akun, atau bergabung sebagai penulis dan terbitkan gagasanmu.
                </p>
                <div className="mt-6 flex flex-wrap gap-3">
                    {auth.user ? (
                        <Link href={auth.user.role === 'admin' ? '/admin/users' : '/dashboard/posts'} className="rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Buka Dashboard</Link>
                    ) : (
                        <>
                            <Link href="/login" className="rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Masuk</Link>
                            <Link href="/register" className="rounded-lg border border-white/60 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Daftar Penulis</Link>
                        </>
                    )}
                </div>
            </section>

            <FilterBar url="/articles" filters={filters} />

            {posts.data.length === 0 ? (
                <p className="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">Belum ada artikel yang cocok.</p>
            ) : (
                <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {posts.data.map((p) => (
                        <Link key={p.id} href={`/articles/${p.slug}`} className="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                            {p.cover_url ? (
                                <img src={p.cover_url} alt="" className="h-44 w-full object-cover" />
                            ) : (
                                <div className="flex h-44 items-center justify-center bg-gradient-to-br from-indigo-100 to-violet-100 font-serif text-4xl text-indigo-300">{p.category[0]}</div>
                            )}
                            <div className="p-5">
                                <span className="text-xs font-semibold uppercase tracking-wide text-indigo-600">{p.category}</span>
                                <h2 className="mt-1 font-serif text-lg font-bold group-hover:text-indigo-700">{p.title}</h2>
                                <p className="mt-2 line-clamp-3 text-sm text-slate-600">{p.excerpt}</p>
                                <div className="mt-3 text-xs text-slate-400">{p.user?.name} · {fmtDate(p.published_at)}</div>
                            </div>
                        </Link>
                    ))}
                </div>
            )}
            <Pagination paginator={posts} />
        </>
    );
}

PostIndex.layout = (page) => <PublicLayout>{page}</PublicLayout>;
