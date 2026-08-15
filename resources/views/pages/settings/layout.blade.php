{{--
    resources/views/pages/settings/layout.blade.php
    ------------------------------------------------------------------
    Used as <x-pages::settings.layout :heading="..." :subheading="...">
    inside profile.blade.php, security.blade.php, and appearance.blade.php -
    it's the sidebar nav + section heading + content slot they all share.

    Note this file lives under pages/settings/ but is used as a Blade
    COMPONENT (<x-pages::settings.layout>), not routed directly - the
    pages:: namespace works both ways depending on how a file is
    referenced, same as we saw with the pages:: routes themselves.
--}}
@props(['heading', 'subheading'])

<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav aria-label="{{ __('Settings') }}" class="flex flex-col gap-1">
            {{--
                data-current: is the same Tailwind v4 attribute variant
                we used for the website/dashboard nav earlier - Livewire's
                wire:navigate automatically tags whichever link matches
                the current URL with data-current, no Alpine or PHP
                route-checking needed to style the active item.
            --}}
            <a href="{{ route('profile.edit') }}" wire:navigate
                class="rounded-md px-3 py-2 text-sm text-ink-700 dark:text-linen-200 hover:bg-ink-50 dark:hover:bg-ink-800
                      data-current:bg-copper-50 data-current:text-copper-700 data-current:font-medium
                      dark:data-current:bg-copper-500/10 dark:data-current:text-copper-300">
                {{ __('Profile') }}
            </a>
            <a href="{{ route('security.edit') }}" wire:navigate
                class="rounded-md px-3 py-2 text-sm text-ink-700 dark:text-linen-200 hover:bg-ink-50 dark:hover:bg-ink-800
                      data-current:bg-copper-50 data-current:text-copper-700 data-current:font-medium
                      dark:data-current:bg-copper-500/10 dark:data-current:text-copper-300">
                {{ __('Security') }}
            </a>
            <a href="{{ route('appearance.edit') }}" wire:navigate
                class="rounded-md px-3 py-2 text-sm text-ink-700 dark:text-linen-200 hover:bg-ink-50 dark:hover:bg-ink-800
                      data-current:bg-copper-50 data-current:text-copper-700 data-current:font-medium
                      dark:data-current:bg-copper-500/10 dark:data-current:text-copper-300">
                {{ __('Appearance') }}
            </a>
        </nav>
    </div>

    <div class="border-t border-ink-100 dark:border-ink-800 w-full md:hidden"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="font-display text-lg font-semibold text-ink-900 dark:text-linen-50">{{ $heading ?? '' }}</h2>
        <p class="text-sm text-ink-600 dark:text-linen-300">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
