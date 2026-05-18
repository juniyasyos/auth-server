<div class="space-y-8 mt-8">
    <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
        <div class="relative isolate px-6 py-8 sm:px-8 sm:py-10">
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-sky-50 via-white to-emerald-50 dark:from-sky-950/20 dark:via-gray-950 dark:to-emerald-950/20"></div>
            <div class="max-w-3xl">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400">Settings</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white sm:text-4xl">
                    Settings Center
                </h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-300 sm:text-base">
                    Pilih kartu di bawah untuk masuk ke grup konfigurasi yang ingin diubah. Halaman ini menjadi pintu utama pengelolaan settings.
                </p>
            </div>
        </div>
    </section>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
        <a
            href="{{ $card['url'] }}"
            class="group flex h-full flex-col rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-gray-300 hover:shadow-xl dark:border-gray-800 dark:bg-gray-950 dark:hover:border-gray-700">
            <div class="flex items-start justify-end gap-4">
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    {{ $card['badge'] }}
                </span>
            </div>

            <div class="mt-6 flex-1">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                    {{ $card['title'] }}
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                    {{ $card['description'] }}
                </p>
            </div>

            <div class="mt-6 flex items-center gap-2 text-sm font-semibold" style="color: {{ $card['color'] }}">
                <span>Buka halaman</span>
                <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
            </div>
        </a>
        @endforeach
    </section>
</div>