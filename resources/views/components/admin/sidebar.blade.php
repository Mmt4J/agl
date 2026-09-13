{{--
    Admin nav. Data now lives in App\Support\AdminNav (shared with
    topbar.blade.php, which needs the same labels for the page title).
    Each item's route must exist in routes/web.php before this renders.
--}}
@php
    $navGroups = \App\Support\AdminNav::groups();
@endphp

@persist('admin-sidenav')

{{-- Off-canvas on mobile (translate via sidebarOpen from the layout's x-data), pinned on desktop.
     The whole aside is persisted across wire:navigate so its scroll position, Alpine state and
     hover state survive page changes instead of being rebuilt from scratch on every click.
     Active-link highlighting inside persisted elements must use wire:current (the automatic
     data-current toggling does not run there). --}}
<aside
    class="fixed inset-y-0 left-0 z-40 w-72 bg-ink-950 text-linen-100 flex flex-col transition-transform duration-200 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    {{-- Brand mark + wordmark, closes on mobile --}}
    <div class="h-16 flex items-center gap-3 px-5 border-b border-linen-100/10 shrink-0">
        <x-app-logo-icon class="w-9 h-9" />
        <div class="leading-none">
            <p class="font-display font-semibold text-sm">Anesmavisa</p>
            <p class="font-mono text-[10px] tracking-widest text-copper-300">ADMIN CONSOLE</p>
        </div>
        <button @click="sidebarOpen=false"
            class="ml-auto lg:hidden w-9 h-9 grid place-items-center rounded-md hover:bg-linen-100/10"
            aria-label="Close menu">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Grouped nav, one <a> per item. wire:current.exact: highlights the link
         matching the current URL inside the persisted sidebar (the automatic
         data-current marker does not update within @persist elements). --}}
    <nav id="admin-sidebar-nav" class="flex-1 overflow-y-auto px-3 py-4 space-y-6" wire:navigate:scroll aria-label="Admin">
        @foreach ($navGroups as $group => $items)
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-linen-100/35 px-3 mb-1.5">
                    {{ $group }}</p>
                <div class="space-y-0.5">
                    @foreach ($items as $item)
                        <a href="{{ route($item['route']) }}" wire:navigate wire:current.exact="admin-nav-active"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors
                                   text-linen-100/75 hover:bg-linen-100/10 hover:text-linen-50">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">{!! $item['icon'] !!}</svg>
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <div class="p-4 border-t border-linen-100/10 shrink-0">
        <a href="{{ route('website.home') }}" wire:navigate
            class="flex items-center gap-2 text-xs text-linen-100/60 hover:text-copper-300 transition-colors font-mono">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V13.5m-9 1.5L18.75 4.5M18.75 4.5H13.5m5.25 0v5.25" />
            </svg>
            View live site
        </a>
    </div>
</aside>

@endpersist
