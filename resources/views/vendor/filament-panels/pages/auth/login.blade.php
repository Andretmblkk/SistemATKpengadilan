@php
    use Filament\Facades\Filament;
@endphp

<x-filament-panels::page.simple>
    <x-slot name="subheading">
        <div style="display:flex;flex-direction:column;align-items:center;gap:0.7rem;margin-bottom:1.2rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PTA Jayapura" style="width:90px;height:90px;border-radius:50%;box-shadow:0 2px 8px #22c55e33;background:#f0fdf4;border:3px solid #22c55e;object-fit:contain;">
            <div style="font-size:1.3rem;font-weight:800;color:#15803d;letter-spacing:0.5px;">Pengadilan Tinggi Agama Jayapura</div>
            <div style="color:#475569;font-size:1.02rem;text-align:center;max-width:320px;">Login untuk menggunakan sistem.</div>
        </div>
        @if ($errors->any())
            <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;border-radius:8px;padding:0.7rem 1rem;margin-bottom:1.2rem;font-size:0.98rem;text-align:left;">
                <ul style="margin:0; padding-left:1.2em;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-slot>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <div style="border:2px solid #22c55e; border-radius:16px; padding:clamp(1.2rem,4vw,2.8rem) clamp(1rem,4vw,2.2rem); background:#fff; max-width:520px; min-width:320px; width:100%; margin:0 auto 1.5rem auto; box-shadow:0 2px 16px #22c55e22;">
        <x-filament-panels::form id="form" wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
</x-filament-panels::page.simple>
