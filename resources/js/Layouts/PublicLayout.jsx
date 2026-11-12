import { Link, router, usePage } from '@inertiajs/react';
import Flash from '../Components/Flash';
import LoadingIndicator from '../Components/LoadingIndicator';

export default function PublicLayout({ children }) {
    const { auth, appName } = usePage().props;
    const user = auth.user;
    const btn = 'rounded-lg px-4 py-2 text-sm font-medium';

    return (
        <div className="flex min-h-screen flex-col">
            <LoadingIndicator />
            <nav className="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
                    <Link href="/" className="font-serif text-xl font-bold text-indigo-700">{appName}</Link>
                    <div className="flex items-center gap-2">
                        <Link href="/articles" className="hidden px-3 text-sm text-slate-600 hover:text-indigo-600 sm:block">Artikel</Link>
                        {user ? (
                            <>
                                <Link href={user.role === 'admin' ? '/admin/users' : '/dashboard/posts'} className={`${btn} bg-indigo-600 text-white hover:bg-indigo-700`}>
                                    {user.role === 'admin' ? 'Panel Admin' : 'Dashboard Penulis'}
                                </Link>
                                <span className="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-indigo-100 text-sm font-bold text-indigo-700" title={user.name}>
                                    {user.photo_url ? <img src={user.photo_url} alt="" className="h-full w-full object-cover" /> : user.name[0]}
                                </span>
                                <button type="button" onClick={() => router.post('/logout')} className="px-2 text-sm text-slate-600 hover:text-rose-700">Keluar</button>
                            </>
                        ) : (
                            <>
                                <Link href="/login" className={`${btn} border border-slate-300 text-slate-700 hover:bg-slate-50`}>🔑 Masuk</Link>
                                <Link href="/register" className={`${btn} bg-indigo-600 text-white hover:bg-indigo-700`}>✍️ Daftar</Link>
                            </>
                        )}
                    </div>
                </div>
            </nav>

            <main className="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
                <Flash />
                {children}
            </main>

            <footer className="border-t border-slate-200 bg-white">
                <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-6 text-sm text-slate-600 sm:flex-row">
                    <span className="font-serif text-slate-700">{appName} — ruang menulis & membaca.</span>
                    <div className="flex gap-5">
                        <Link href="/" className="hover:text-indigo-600">Beranda Publik</Link>
                        <Link href="/login" className="hover:text-indigo-600">Halaman Login</Link>
                        <Link href="/register" className="hover:text-indigo-600">Halaman Register</Link>
                    </div>
                </div>
            </footer>
        </div>
    );
}
