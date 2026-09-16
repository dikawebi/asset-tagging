<div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    <a href="{{ \App\Filament\Resources\Assets\AssetResource::getUrl('create') }}"
       class="group flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-[#0066ff] hover:shadow-lg hover:shadow-blue-500/10 dark:border-white/10 dark:bg-white/5">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[#0066ff] text-white shadow-md shadow-blue-600/30">
            <x-heroicon-m-plus class="h-6 w-6" />
        </span>
        <span>
            <b class="block text-sm font-bold text-gray-900 dark:text-white">Tambah Aset</b>
            <span class="block text-xs text-gray-500 dark:text-gray-400">Daftarkan barang baru + QR</span>
        </span>
    </a>

    <a href="{{ \App\Filament\Pages\ScanAsset::getUrl() }}"
       class="group flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-[#0066ff] hover:shadow-lg hover:shadow-blue-500/10 dark:border-white/10 dark:bg-white/5">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-md shadow-emerald-600/30">
            <x-heroicon-m-qr-code class="h-6 w-6" />
        </span>
        <span>
            <b class="block text-sm font-bold text-gray-900 dark:text-white">Scan QR</b>
            <span class="block text-xs text-gray-500 dark:text-gray-400">Buka kamera pemindai aset</span>
        </span>
    </a>

    <a href="{{ \App\Filament\Resources\Assets\AssetResource::getUrl('index') }}"
       class="group flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-[#0066ff] hover:shadow-lg hover:shadow-blue-500/10 dark:border-white/10 dark:bg-white/5">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-slate-700 text-white shadow-md shadow-slate-700/30 dark:bg-slate-600">
            <x-heroicon-m-clipboard-document-list class="h-6 w-6" />
        </span>
        <span>
            <b class="block text-sm font-bold text-gray-900 dark:text-white">Daftar Aset</b>
            <span class="block text-xs text-gray-500 dark:text-gray-400">Lihat &amp; kelola inventaris</span>
        </span>
    </a>
</div>
