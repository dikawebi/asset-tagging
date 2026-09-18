<div class="bp-fs">
    <div class="bp-fs-form-col">
        <div class="bp-fs-form-inner">
<style>
                .bp-fs-brand { margin-bottom: 1.25rem; }
                .bp-fs-brand-row { display: flex; align-items: center; gap: 0.8rem; }
                .bp-fs-brand-logo { height: 3.25rem; width: 3.25rem; object-fit: contain; filter: drop-shadow(0 8px 20px rgb(0 102 255 / 0.25)); }
                .bp-fs-brand-name { margin-top: 0.3rem; font-size: 1.15rem; font-weight: 800; letter-spacing: 0.01em; color: #0F172A; line-height: 1; }
                .bp-fs-brand-sub { margin-top: -2px; margin-left: calc(3.25rem + 0.8rem); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.28em; color: #0066FF; line-height: 1; }
                .dark .bp-fs-brand-name { color: #ffffff; }
                .dark .bp-fs-brand-sub { color: #93c5fd; }
            </style>

            <div class="bp-fs-brand">
                <div class="bp-fs-brand-row">
                    <img src="{{ asset('images/logo-bp.svg') }}" alt="PT Borneo Prima" class="bp-fs-brand-logo" fetchpriority="high" decoding="async" />
                    <div class="bp-fs-brand-name">PT BORNEO PRIMA</div>
                </div>
                <div class="bp-fs-brand-sub">Asset Tagging</div>
            </div>

            <h1 class="bp-fs-heading">{{ $this->getHeading() }}</h1>
            <p class="bp-fs-subheading">{{ $this->getSubheading() }}</p>

            {{ $this->content }}

            <p class="bp-internal-note">
                <x-heroicon-m-lock-closed class="bp-internal-note-icon" />
                Portal internal — khusus karyawan PT Borneo Prima.
            </p>
        </div>
    </div>

    @include('filament.auth.fs-side')

    <x-filament-actions::modals />
</div>

{{-- Polish login tanpa mengubah layout: hint Caps Lock khusus kolom password. --}}
<script>
    (() => {
        const root = document.querySelector('.bp-fs');
        if (!root) return;
        const password = root.querySelector('input[type="password"]');
        if (!password) return;

        const hint = document.createElement('p');
        hint.className = 'bp-caps-hint';
        hint.setAttribute('role', 'status');
        hint.hidden = true;
        hint.textContent = 'Caps Lock aktif — periksa huruf besar/kecil.';
        password.closest('.fi-fo-field-wrp, .fi-field, div')?.appendChild(hint);

        const update = (event) => {
            const on = event?.getModifierState ? event.getModifierState('CapsLock') : false;
            hint.hidden = !on;
        };
        password.addEventListener('keyup', update);
        password.addEventListener('keydown', update);
        password.addEventListener('blur', () => { hint.hidden = true; });
    })();
</script>
