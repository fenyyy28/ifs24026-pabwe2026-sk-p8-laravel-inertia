import { Link } from '@inertiajs/react';
import KebabMenu from './KebabMenu';

export const fmtDate = (iso) =>
    iso ? new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';

export function StatusBadge({ published }) {
    return (
        <span className={`rounded-full px-2.5 py-0.5 text-xs font-medium ${published ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900'}`}>
            {published ? 'Terbit' : 'Draf'}
        </span>
    );
}

/** Tampilan List view (bukan card). */
export default function PostList({ posts, showAuthor = false, onEdit, onDelete, onToggle, detailHref }) {
    if (posts.data.length === 0) {
        return <div className="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">Belum ada artikel.</div>;
    }

    return (
        <ul className="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white shadow-sm">
            {posts.data.map((post) => (
                <li key={post.id} className="flex items-center gap-4 px-4 py-3">
                    {post.cover_url ? (
                        <img src={post.cover_url} alt="" className="h-12 w-16 rounded-md object-cover" />
                    ) : (
                        <div className="flex h-12 w-16 items-center justify-center rounded-md bg-slate-100 text-xs text-slate-600">No img</div>
                    )}
                    <div className="min-w-0 flex-1">
                        <div className="truncate font-medium text-slate-900">{post.title}</div>
                        <div className="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                            <span className="rounded bg-indigo-50 px-2 py-0.5 text-indigo-800">{post.category}</span>
                            <StatusBadge published={post.is_published} />
                            {showAuthor && post.user && <span>oleh {post.user.name}</span>}
                            <span>{fmtDate(post.created_at)}</span>
                        </div>
                    </div>
                    {onToggle && (
                        <button type="button" onClick={() => onToggle(post)} className="hidden rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 sm:block">
                            {post.is_published ? 'Jadikan Draf' : 'Publikasikan'}
                        </button>
                    )}
                    {onEdit ? (
                        <KebabMenu detailHref={detailHref(post)} onEdit={() => onEdit(post)} onDelete={() => onDelete(post)} />
                    ) : (
                        post.is_published && <Link href={`/articles/${post.slug}`} className="text-sm font-medium text-indigo-600 hover:underline">Lihat</Link>
                    )}
                </li>
            ))}
        </ul>
    );
}
