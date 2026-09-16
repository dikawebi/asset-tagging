<div class="bp-fs">
    <div class="bp-fs-form-col">
        <div class="bp-fs-form-inner">
            <img src="{{ asset('images/logo-bp.svg') }}" alt="PT Borneo Prima" class="bp-fs-logo" />

            <span class="bp-auth-badge">
                <span class="bp-auth-badge-dot"></span>
                PT Borneo Prima &bull; Asset Tagging
            </span>

            <h1 class="bp-fs-heading">{{ $this->getHeading() }}</h1>
            <p class="bp-fs-subheading">{{ $this->getSubheading() }}</p>

            {{ $this->content }}
        </div>
    </div>

    @include('filament.auth.fs-side')

    <x-filament-actions::modals />
</div>
