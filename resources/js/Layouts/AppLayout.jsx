import { Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Flash from '../Components/Flash';
import LoadingIndicator from '../Components/LoadingIndicator';

export default function AppLayout({ children }) {
    const { auth, appName } = usePage().props;
    const { url } = usePage();
    const user = auth.user;
    const [open, setOpen] = useState(false);

    const links = user.role === 'admin'
        ? [['/admin/users', 'Pengguna'], ['/admin/posts', 'Semua Artikel'], ['/profile', 'Profil']]
        : [['/dashboard/posts', 'Artikel Saya'], ['/profile', 'Profil']];

    return (
        <div className="min-h-screen md:flex">
            <LoadingIndicator />
            <aside className={`${open ? 'block' : 'hidden'} w-full bg-slate-900 text-slate-200 md:block md:w-60`}>
                <div className="px-5 py-5 font-serif text-xl font-bold text-white">{appName}</div>
                <nav className="space-y-1 px-3">
                    {links.map(([href, label]) => (
                        <Link key={href} href={href} className={`block rounded-lg px-3 py-2 text-sm ${url.startsWith(href) ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800'}`}>{label}</Link>
                    ))}
                    <Link href="/" className="block rounded-lg px-3 py-2 text-sm hover:bg-slate-800">← Lihat Blog</Link>
                </nav>
                <div className="mt-6 border-t border-slate-800 px-5 py-4 text-sm">
                    <div className="font-medium text-white">{user.name}</div>
                    <div className="text-xs text-slate-400">@{user.username} · {user.role}</div>
                    <button type="button" onClick={() => router.post('/logout')} className="mt-3 text-rose-300 hover:text-rose-200">Keluar</button>
                </div>
            </aside>
            <div className="flex-1">
                <div className="flex items-center justify-between border-b bg-white px-4 py-3 md:hidden">
                    <span className="font-serif font-bold text-indigo-700">{appName}</span>
                    <button type="button" onClick={() => setOpen(!open)} className="rounded border px-3 py-1 text-sm">Menu</button>
                </div>
                <main className="mx-auto max-w-5xl p-4 md:p-8">
                    <Flash />
                    {children}
                </main>
            </div>
        </div>
    );
}
