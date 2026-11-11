import { Head, Link, useForm } from '@inertiajs/react';
import { Spinner } from '../../Components/LoadingIndicator';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Register() {
    const form = useForm({ name: '', username: '', password: '', password_confirmation: '' });

    const submit = (e) => {
        e.preventDefault();
        form.post('/register');
    };

    const input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2';
    const fields = [
        ['name', 'Nama lengkap', 'text'],
        ['username', 'Username', 'text'],
        ['password', 'Password', 'password'],
        ['password_confirmation', 'Konfirmasi password', 'password'],
    ];

    return (
        <div className="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <Head title="Daftar" />
            <h1 className="font-serif text-2xl font-bold">Daftar Penulis</h1>
            <form onSubmit={submit} className="mt-6 space-y-4">
                {fields.map(([key, label, type]) => (
                    <div key={key}>
                        <label htmlFor={`reg-${key}`} className="text-sm font-medium">{label}</label>
                        <input id={`reg-${key}`} type={type} className={input} value={form.data[key]} onChange={(e) => form.setData(key, e.target.value)} />
                        {form.errors[key] && <p className="mt-1 text-xs text-rose-600">{form.errors[key]}</p>}
                    </div>
                ))}
                <button id="register-submit" type="submit" disabled={form.processing} className="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 py-2.5 font-medium text-white hover:bg-indigo-700 disabled:opacity-60">
                    {form.processing && <Spinner />} Daftar
                </button>
            </form>
            <p className="mt-6 text-center text-sm text-slate-500">Sudah punya akun? <Link href="/login" className="font-medium text-indigo-600 hover:underline">Masuk</Link></p>
        </div>
    );
}

Register.layout = (page) => <PublicLayout>{page}</PublicLayout>;
