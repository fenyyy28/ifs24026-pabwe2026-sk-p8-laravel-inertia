import { Head, Link, useForm } from '@inertiajs/react';
import { Spinner } from '../../Components/LoadingIndicator';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Login() {
    const form = useForm({ username: '', password: '', remember: false });

    const submit = (e) => {
        e.preventDefault();
        form.post('/login');
    };

    const input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2';

    return (
        <div className="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <Head title="Masuk" />
            <h1 className="font-serif text-2xl font-bold">Masuk</h1>
            <p className="mt-1 text-sm text-slate-500">Masuk untuk mengelola artikelmu.</p>
            <form onSubmit={submit} className="mt-6 space-y-4">
                <div>
                    <label htmlFor="login-username" className="text-sm font-medium">Username</label>
                    <input id="login-username" autoComplete="username" className={input} value={form.data.username} onChange={(e) => form.setData('username', e.target.value)} />
                    {form.errors.username && <p className="mt-1 text-xs text-rose-600">{form.errors.username}</p>}
                </div>
                <div>
                    <label htmlFor="login-password" className="text-sm font-medium">Password</label>
                    <input id="login-password" type="password" autoComplete="current-password" className={input} value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} />
                    {form.errors.password && <p className="mt-1 text-xs text-rose-600">{form.errors.password}</p>}
                </div>
                <label className="flex items-center gap-2 text-sm">
                    <input type="checkbox" checked={form.data.remember} onChange={(e) => form.setData('remember', e.target.checked)} /> Ingat saya
                </label>
                <button id="login-submit" type="submit" disabled={form.processing} className="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 py-2.5 font-medium text-white hover:bg-indigo-700 disabled:opacity-60">
                    {form.processing && <Spinner />} Masuk
                </button>
            </form>
            <p className="mt-6 text-center text-sm text-slate-500">Belum punya akun? <Link href="/register" className="font-medium text-indigo-600 hover:underline">Daftar</Link></p>
        </div>
    );
}

Login.layout = (page) => <PublicLayout>{page}</PublicLayout>;
