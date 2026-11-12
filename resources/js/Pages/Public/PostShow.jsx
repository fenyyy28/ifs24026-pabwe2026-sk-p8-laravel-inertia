import { Head, Link, usePage } from '@inertiajs/react';
import MarkdownViewer from '../../Components/MarkdownViewer';
import { fmtDate } from '../../Components/PostList';
import PublicLayout from '../../Layouts/PublicLayout';

export default function PostShow({ post }) {
    const { auth } = usePage().props;

    return (
        <article className="mx-auto max-w-3xl">
            <Head title={post.title} />
            <Link href="/articles" className="text-sm text-indigo-600 hover:underline">← Semua artikel</Link>
            <span className="mt-4 block text-xs font-semibold uppercase tracking-wide text-indigo-600">{post.category}</span>
            <h1 className="mt-1 font-serif text-4xl font-bold leading-tight">{post.title}</h1>
            <div className="mt-3 text-sm text-slate-600">Oleh {post.user?.name} · {fmtDate(post.published_at)}</div>
            {post.cover_url && (
                <img
                    src={post.cover_url}
                    alt={post.title}
                    fetchPriority="high"
                    loading="eager"
                    className="mt-6 max-h-96 w-full rounded-xl object-cover"
                />
            )}
            <div className="mt-8"><MarkdownViewer value={post.content} /></div>

            <aside className="mt-12 rounded-2xl bg-indigo-50 p-8 text-center">
                <h3 className="font-serif text-xl font-bold text-indigo-900">Bergabung dengan komunitas penulis</h3>
                <p className="mt-2 text-sm text-indigo-800">Simpan tulisanmu, terbitkan, dan jangkau pembaca baru.</p>
                <div className="mt-4 flex justify-center gap-3">
                    {auth.user ? (
                        <Link href="/dashboard/posts" className="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Buka Dashboard</Link>
                    ) : (
                        <>
                            <Link href="/login" className="rounded-lg border border-indigo-300 bg-white px-5 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Halaman Login</Link>
                            <Link href="/register" className="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">Halaman Register</Link>
                        </>
                    )}
                </div>
            </aside>
        </article>
    );
}

PostShow.layout = (page) => <PublicLayout>{page}</PublicLayout>;
