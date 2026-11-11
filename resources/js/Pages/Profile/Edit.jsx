import { Head, useForm, usePage } from '@inertiajs/react';
import { Spinner } from '../../Components/LoadingIndicator';
import AppLayout from '../../Layouts/AppLayout';

export default function Edit() {
    const { auth } = usePage().props;
    const form = useForm({ name: auth.user.name, username: auth.user.username, password: '', password_confirmation: '', photo: null });

    const submit = (e) => {
        e.preventDefault();
        form.post('/profile', { forceFormData: true, preserveScroll: true, onSuccess: () => form.reset('password', 'password_confirmation', 'photo') });
    };

    const input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';
    const fields = [['name', 'Nama', 'text'], ['username', 'Username', 'text'], ['password', 'Password baru (opsional)', 'password'], ['password_confirmation', 'Konfirmasi password', 'password']];

    return (
        <>
            <Head title="Profil" />
            <h1 className="mb-6 font-serif text-2xl font-bold">Profil Penulis</h1>
            <form onSubmit={submit} className="max-w-lg space-y-4 rounded-xl border border-slate-200 bg-white p-6">
                {auth.user.photo_url && <img src={auth.user.photo_url} alt="" className="h-20 w-20 rounded-full object-cover" />}
                {fields.map(([key, label, type]) => (
                    <div key={key}>
                        <label htmlFor={`pr-${key}`} className="text-sm font-medium">{label}</label>
                        <input id={`pr-${key}`} type={type} className={input} value={form.data[key]} onChange={(e) => form.setData(key, e.target.value)} />
                        {form.errors[key] && <p className="mt-1 text-xs text-rose-600">{form.errors[key]}</p>}
                    </div>
                ))}
                <div>
                    <label htmlFor="pr-photo" className="text-sm font-medium">Foto profil</label>
                    <input id="pr-photo" type="file" accept="image/*" className={input} onChange={(e) => form.setData('photo', e.target.files[0] ?? null)} />
                    {form.errors.photo && <p className="mt-1 text-xs text-rose-600">{form.errors.photo}</p>}
                </div>
                <button type="submit" disabled={form.processing} className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-60">
                    {form.processing && <Spinner />} Simpan Profil
                </button>
            </form>
        </>
    );
}

Edit.layout = (page) => <AppLayout>{page}</AppLayout>;
