@php
    // Galeri read-only untuk halaman view aset (state berupa array JSON `images`).
    // Mendukung data-URI base64 (hasil kamera HP) maupun path file storage.
    // Lightbox memakai CSS :target agar bekerja tanpa JavaScript/Alpine.
    $photos = $images ?? [];

    if (is_string($photos)) {
        $decoded = json_decode($photos, true);
        $photos = is_array($decoded) ? $decoded : [$photos];
    }

    $photos = array_values(array_filter(is_array($photos) ? $photos : []));

    $resolveSrc = function ($photo) {
        if (! is_string($photo) || $photo === '') {
            return null;
        }

        if (str_starts_with($photo, 'data:image') || str_starts_with($photo, 'http')) {
            return $photo;
        }

        return \Illuminate\Support\Facades\Storage::url($photo);
    };

    $sources = [];
    foreach ($photos as $photo) {
        $src = $resolveSrc($photo);
        if ($src) {
            $sources[] = $src;
        }
    }
@endphp

<style>
    .asset-photo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; width: 100%; }
    .asset-photo-thumb { width: 100%; height: 140px; object-fit: cover; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: zoom-in; }
    .asset-lightbox { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; }
    .asset-lightbox:target { display: flex; }
    .asset-lightbox img { max-width: 100%; max-height: 90%; border-radius: 8px; object-fit: contain; }
    .asset-lightbox-close { position: absolute; top: 16px; right: 20px; color: #fff; font-size: 32px; line-height: 1; text-decoration: none; }
    .asset-lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); color: #fff; font-size: 40px; line-height: 1; text-decoration: none; padding: 12px; }
    .asset-lightbox-prev { left: 12px; }
    .asset-lightbox-next { right: 12px; }
</style>

@if (empty($sources))
    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada foto aset.</p>
@else
    <div class="asset-photo-grid">
        @foreach ($sources as $index => $src)
            <a href="#asset-photo-{{ $index }}" title="Klik untuk lihat full size">
                <img src="{{ $src }}" alt="Foto aset {{ $index + 1 }}" loading="lazy" class="asset-photo-thumb">
            </a>
        @endforeach
    </div>

    @foreach ($sources as $index => $src)
        <div id="asset-photo-{{ $index }}" class="asset-lightbox" onclick="if (event.target === this) { location.hash = '#!'; }">
            <a href="#!" class="asset-lightbox-close" title="Tutup">&times;</a>
            @if (count($sources) > 1)
                <a href="#asset-photo-{{ ($index - 1 + count($sources)) % count($sources) }}" class="asset-lightbox-nav asset-lightbox-prev" title="Sebelumnya">&#8249;</a>
                <a href="#asset-photo-{{ ($index + 1) % count($sources) }}" class="asset-lightbox-nav asset-lightbox-next" title="Berikutnya">&#8250;</a>
            @endif
            <img src="{{ $src }}" alt="Foto aset {{ $index + 1 }} full size">
        </div>
    @endforeach
@endif
