{{--
    Public site header. Sticky, with the live open-status badge, dark
    mode toggle (same localStorage key/logic as the admin topbar), and
    a mobile slide-down menu. data-current: (Livewire's own wire:navigate
    marker) handles active-link highlighting - no client "page" tracking.
--}}
<a href="#main-content"
    class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-ink-900 focus:text-linen-50 focus:px-4 focus:py-2 focus:rounded-md">
    Skip to content
</a>

<header x-data="{ mobileOpen: false }"
    class="sticky top-0 z-40 border-b border-ink-900/10 dark:border-linen-100/10 bg-linen-50/95 dark:bg-ink-950/95 backdrop-blur">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-4">
        <a href="{{ route('website.home') }}" wire:navigate class="flex items-center gap-2 shrink-0">
            <x-app-logo-icon class="w-9 h-9" />
            <span class="leading-none">
                <span class="block font-display font-semibold text-ink-900 dark:text-linen-50">Anesmavisa</span>
                <span class="block font-mono text-[9px] tracking-widest text-copper-600 dark:text-copper-300">GLOBAL
                    LTD</span>
            </span>
        </a>

        <nav class="hidden lg:flex items-center gap-1 ml-4" aria-label="Primary">
            @foreach ([['route' => 'website.home', 'active' => 'website.home', 'label' => 'Home'], ['route' => 'website.about', 'active' => 'website.about', 'label' => 'About'], ['route' => 'website.services', 'active' => 'website.services', 'label' => 'Services'], ['route' => 'website.industries', 'active' => 'website.industries', 'label' => 'Industries'], ['route' => 'website.portfolio', 'active' => 'website.portfolio', 'label' => 'Portfolio'], ['route' => 'website.pricing', 'active' => 'website.pricing', 'label' => 'Pricing'], ['route' => 'website.blog', 'active' => 'website.blog*', 'label' => 'Blog'], ['route' => 'website.contact', 'active' => 'website.contact', 'label' => 'Contact']] as $link)
                <a href="{{ route($link['route']) }}" wire:navigate @class([
                    'relative px-3 py-2 text-sm text-ink-700 dark:text-linen-200 hover:text-ink-950 dark:hover:text-linen-50 after:absolute after:inset-x-3 after:bottom-0 after:h-0.5 after:bg-copper-500 after:transition-transform',
                    'font-semibold text-ink-950 dark:text-linen-50 after:scale-x-100' => request()->routeIs(
                        $link['active']),
                    'after:scale-x-0' => !request()->routeIs($link['active']),
                ])
                    @if (request()->routeIs($link['active'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-2">
            <x-open-status />

            <div x-data="{
                theme: localStorage.getItem('anesmavisa-theme') ||
                    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
                setTheme(value) {
                    this.theme = value;
                    localStorage.setItem('anesmavisa-theme', value);
                    document.documentElement.classList.toggle('dark', value === 'dark');
                },
            }">
                <button @click="setTheme(theme === 'dark' ? 'light' : 'dark')"
                    class="w-10 h-10 grid place-items-center rounded-md text-ink-900 dark:text-copper-300 hover:bg-ink-900/5 dark:hover:bg-linen-100/10 transition-colors"
                    :aria-label="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="theme !== 'dark'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1.5m0 15V21m8.485-8.485H19M5 12H3.515m13.435 6.364l-1.06-1.06M6.11 6.11l-1.06-1.06m12.02 0l-1.06 1.06M6.11 17.89l-1.06 1.06M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </button>
            </div>

            @php
                $isAdmin = auth()->user()?->isAdmin() ?? false;
                $firstName = $isAdmin ? explode(' ', auth()->user()->name)[0] : '';
            @endphp
            <a href="{{ $isAdmin ? route('admin.overview') : route('website.quote') }}" wire:navigate
                class="hidden sm:inline-flex items-center rounded-md bg-copper-500 hover:bg-copper-600 text-ink-950 font-semibold px-4 py-2 text-sm transition-colors">
                {{ $isAdmin ? $firstName : 'Request a Quote' }}
            </a>

            <button @click="mobileOpen = !mobileOpen"
                class="lg:hidden w-10 h-10 grid place-items-center rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/10"
                aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <nav x-show="mobileOpen" x-cloak x-transition
        class="lg:hidden px-4 pb-4 flex flex-col gap-1 border-t border-ink-900/10 dark:border-linen-100/10"
        aria-label="Mobile">
        @foreach ([['route' => 'website.home', 'active' => 'website.home', 'label' => 'Home'], ['route' => 'website.about', 'active' => 'website.about', 'label' => 'About'], ['route' => 'website.services', 'active' => 'website.services', 'label' => 'Services'], ['route' => 'website.industries', 'active' => 'website.industries', 'label' => 'Industries'], ['route' => 'website.portfolio', 'active' => 'website.portfolio', 'label' => 'Portfolio'], ['route' => 'website.pricing', 'active' => 'website.pricing', 'label' => 'Pricing'], ['route' => 'website.blog', 'active' => 'website.blog*', 'label' => 'Blog'], ['route' => 'website.contact', 'active' => 'website.contact', 'label' => 'Contact']] as $link)
            <a href="{{ route($link['route']) }}" wire:navigate @click="mobileOpen = false" @class([
                'px-3 py-2.5 text-sm text-ink-700 dark:text-linen-200 hover:bg-ink-900/5 dark:hover:bg-linen-100/10 border-b-2 border-transparent',
                'font-semibold text-ink-950 dark:text-linen-50 border-copper-500' => request()->routeIs(
                    $link['active']),
            ])
                @if (request()->routeIs($link['active'])) aria-current="page" @endif>
                {{ $link['label'] }}
            </a>
        @endforeach
        <a href="{{ route('website.quote') }}" wire:navigate
            class="mt-2 text-center rounded-md bg-copper-500 text-ink-950 font-semibold px-4 py-2.5 text-sm">
            Request a Quote
        </a>
    </nav>
</header>

@php
    $whatsappNumber = \App\Models\Setting::get('company.whatsapp_number');
    $whatsappMessage = \App\Models\Setting::get('company.whatsapp_default_message', '');
@endphp

@if ($whatsappNumber)
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsappNumber) }}?text={{ urlencode($whatsappMessage) }}"
        target="_blank" rel="noopener"
        class="fixed bottom-5 right-5 z-40 w-14 h-14 rounded-full bg-sage-500 hover:bg-sage-600 text-white grid place-items-center shadow-lg"
        aria-label="Chat with us on WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M12 2C6.477 2 2 6.477 2 12c0 1.82.48 3.53 1.32 5.01L2 22l5.13-1.3A9.94 9.94 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a7.9 7.9 0 01-4.24-1.22l-.3-.19-3.13.8.83-3.05-.2-.31A7.9 7.9 0 1112 20zm4.4-5.9c-.24-.12-1.43-.7-1.65-.79-.22-.08-.38-.12-.54.12-.16.24-.62.79-.76.95-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.92-1.18-.71-.63-1.19-1.42-1.33-1.66-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.19-.46-.39-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.43-.58 1.63-1.15.2-.57.2-1.05.14-1.15-.06-.1-.22-.16-.46-.28z" />
        </svg>
    </a>
@endif
