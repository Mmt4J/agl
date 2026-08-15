{{--
    resources/views/components/auth-header.blade.php
    ------------------------------------------------------------------
    Title + description pair shown at the top of every auth page
    (login, register, forgot password, etc). Direct replacement for
    <flux:heading>/<flux:subheading>.

    USAGE
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    PROPS
        title         string   required
        description   string   required

    Note there's no $attributes->merge() here like in the forms/
    components - this one isn't meant to take arbitrary extra HTML
    attributes, it's a fixed two-line block, so we keep it simple.
--}}
@props(['title', 'description'])

<div class="flex w-full flex-col text-center gap-1">
    {{--
        font-display pulls in Zilla Slab (set up in app.css's @theme
        block) - this is what gives headings their distinct serif
        weight versus the Manrope body text everywhere else.
    --}}
    <h1 class="font-display text-2xl font-semibold text-ink-950 dark:text-linen-50">
        {{ $title }}
    </h1>
    <p class="text-sm text-ink-600 dark:text-linen-300">
        {{ $description }}
    </p>
</div>
