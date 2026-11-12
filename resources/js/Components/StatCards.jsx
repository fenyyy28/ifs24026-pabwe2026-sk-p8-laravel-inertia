export default function StatCards({ stats }) {
    const items = [
        { label: 'Total Artikel', value: stats.total, color: 'text-indigo-900 bg-indigo-50' },
        { label: 'Total Selesai', value: stats.published, color: 'text-emerald-900 bg-emerald-50' },
        { label: 'Belum Selesai', value: stats.draft, color: 'text-amber-900 bg-amber-50' },
    ];

    return (
        <div className="mb-6 grid gap-4 sm:grid-cols-3">
            {items.map((s) => (
                <div key={s.label} className={`rounded-xl p-5 shadow-sm ${s.color}`}>
                    <div className="text-sm font-medium">{s.label}</div>
                    <div className="mt-1 text-3xl font-bold">{s.value}</div>
                </div>
            ))}
        </div>
    );
}
