import { Head, router, usePage } from '@inertiajs/react';

export default function Inactive() {
    const page = usePage() as any;
    const reason = page.props.reason ?? 'Akun Anda tidak dapat mengakses sistem saat ini.';
    const status = page.props.status ?? 'unknown';

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-4">
            <Head title="Akun Tidak Aktif" />

            <div className="w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-200/40 dark:border-slate-700 dark:bg-slate-900 dark:shadow-slate-950/40">
                <div className="mb-6 text-center">
                    <p className="text-sm uppercase tracking-[0.3em] text-slate-400">Status Akun</p>
                    <h1 className="mt-3 text-3xl font-semibold text-slate-900 dark:text-slate-100">Akun Tidak Aktif</h1>
                    <p className="mt-3 text-sm text-slate-500 dark:text-slate-400">Status: {status}</p>
                </div>

                <div className="space-y-4 text-slate-600 dark:text-slate-300">
                    <p>{reason}</p>
                    <p>Silakan hubungi administrator jika Anda memerlukan bantuan atau ingin mengajukan aktivasi kembali.</p>
                </div>

                <div className="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        className="rounded-full bg-slate-900 px-5 py-3 text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200"
                        onClick={() => router.visit('/login')}
                    >
                        Kembali ke Login
                    </button>
                </div>
            </div>
        </div>
    );
}
