@php
    $rcNumber = \App\Models\Setting::get('company.rc_number');
    $scumlNumber = \App\Models\Setting::get('company.scuml_number');
    $address = \App\Models\Setting::get('company.address');
    $email = \App\Models\Setting::get('company.email');
    $phonePrimary = \App\Models\Setting::get('company.phone_primary');
@endphp

<footer class="bg-ink-900 dark:bg-ink-950 text-linen-50 mt-10 border-t-2 border-copper-500">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <x-app-logo-icon class="w-8 h-8" />
                <span class="font-display font-semibold">Anesmavisa</span>
            </div>
            <p class="text-sm text-linen-100/60">Multi-trade services across electronics, ICT, real estate and more.</p>
            @if ($rcNumber)
                <p class="font-mono text-[11px] text-linen-100/40">RC {{ $rcNumber }}@if($scumlNumber) &middot; SCUML {{ $scumlNumber }}@endif</p>
            @endif
        </div>

        <div class="space-y-2">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-300 mb-2">Company</p>
            <a href="{{ route('website.about') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">About</a>
            <a href="{{ route('website.portfolio') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">Portfolio</a>
            <a href="{{ route('website.blog') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">Blog</a>
            <a href="{{ route('website.contact') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">Contact</a>
        </div>

        <div class="space-y-2">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-300 mb-2">Services</p>
            <a href="{{ route('website.services') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">All services</a>
            <a href="{{ route('website.industries') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">Industries</a>
            <a href="{{ route('website.pricing') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">Pricing</a>
            <a href="{{ route('website.quote') }}" wire:navigate class="block text-sm text-linen-100/70 hover:text-linen-50">Request a quote</a>
        </div>

        <div class="space-y-2">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-300 mb-2">Contact</p>
            @if ($address)
                <p class="text-sm text-linen-100/70">{{ $address }}</p>
            @endif
            @if ($phonePrimary)
                <a href="tel:{{ $phonePrimary }}" class="block text-sm text-linen-100/70 hover:text-linen-50 font-mono">{{ $phonePrimary }}</a>
            @endif
            @if ($email)
                <a href="mailto:{{ $email }}" class="block text-sm text-linen-100/70 hover:text-linen-50 font-mono">{{ $email }}</a>
            @endif
        </div>
    </div>

    <div class="border-t border-linen-100/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-linen-100/40">
            <p>&copy; {{ now()->year }} Anesmavisa Global Ltd. All rights reserved.</p>
            {{-- Privacy/Terms pages aren't built yet - left as plain text
                 rather than pointing at routes that don't exist. --}}
            <p class="flex items-center gap-3">
                <span>Privacy Policy</span>
                <span>Terms of Service</span>
            </p>
        </div>
    </div>
</footer>
