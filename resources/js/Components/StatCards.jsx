export default function StatCards({ stats }) {
    const items = [
        { label: 'Total Artikel', value: stats.total, bg: 'bg-indigo-50', labelClass: 'text-indigo-800', valueClass: 'text-indigo-900' },
        { label: 'Total Selesai', value: stats.published, bg: 'bg-emerald-50', labelClass: 'text-emerald-800', valueClass: 'text-emerald-900' },
        { label: 'Belum Selesai', value: stats.draft, bg: 'bg-amber-50', labelClass: 'text-amber-800', valueClass: 'text-amber-900' },
    ];

    return (
        <div className="mb-6 grid gap-4 sm:grid-cols-3">
            {items.map((s) => (
                <div key={s.label} className={`rounded-xl p-5 shadow-sm ${s.bg}`}>
                    <div className={`text-sm font-semibold ${s.labelClass}`}>{s.label}</div>
                    <div className={`mt-1 text-3xl font-bold ${s.valueClass}`}>{s.value}</div>
                </div>
            ))}
        </div>
    );
}
