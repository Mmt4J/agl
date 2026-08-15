{{--
    resources/views/pages/settings/appearance.blade.php
    ------------------------------------------------------------------
    Original Flux version offered Light/Dark/System via x-model="$flux.appearance" -
    Flux's own magic Alpine store. Rebuilt to match the SAME mechanism
    your prototype already uses (localStorage 'anesmavisa-theme', set/read
    by partials/head.blade.php's blocking script and the header's toggle
    button) rather than introducing a second, different theme system.

    Deliberately kept to two options (Light/Dark), not three - your
    header toggle elsewhere in the app is a straight on/off switch, so
    a "System" option here would behave inconsistently with it. Worth
    revisiting together if you'd like System added everywhere later.
--}}
<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts::account')] #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <h1 class="sr-only">{{ __('Appearance settings') }}</h1>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div
            x-data="{
                theme: localStorage.getItem('anesmavisa-theme')
                    || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

                setTheme(value) {
                    this.theme = value;
                    localStorage.setItem('anesmavisa-theme', value);
                    document.documentElement.classList.toggle('dark', value === 'dark');
                },
            }"
            role="radiogroup"
            aria-label="{{ __('Theme') }}"
            class="inline-flex rounded-md border border-ink-200 dark:border-ink-700 p-1 bg-linen-100 dark:bg-ink-800"
        >
            <button
                type="button"
                role="radio"
                :aria-checked="theme === 'light'"
                @click="setTheme('light')"
                class="flex items-center gap-2 rounded px-4 py-2 text-sm font-medium transition-colors"
                :class="theme === 'light'
                    ? 'bg-white text-ink-900 shadow-sm'
                    : 'text-ink-600 dark:text-linen-300 hover:text-ink-900 dark:hover:text-linen-50'"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364-6.364l-1.06 1.06M6.696 17.304l-1.06 1.06m12.728 0l-1.06-1.06M6.696 6.696L5.636 5.636M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                </svg>
                {{ __('Light') }}
            </button>

            <button
                type="button"
                role="radio"
                :aria-checked="theme === 'dark'"
                @click="setTheme('dark')"
                class="flex items-center gap-2 rounded px-4 py-2 text-sm font-medium transition-colors"
                :class="theme === 'dark'
                    ? 'bg-ink-900 text-linen-50 shadow-sm'
                    : 'text-ink-600 dark:text-linen-300 hover:text-ink-900 dark:hover:text-linen-50'"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
                {{ __('Dark') }}
            </button>
        </div>
    </x-pages::settings.layout>
</section>