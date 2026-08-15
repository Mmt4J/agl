{{--
    resources/views/components/forms/button.blade.php
    ------------------------------------------------------------------
    Reusable button, styled to the Ink/Copper/Linen palette.
    Direct replacement for <flux:button variant="primary" ...>.

    USAGE
        <x-forms.button type="submit" class="w-full">Log in</x-forms.button>
        <x-forms.button variant="secondary" type="button">Cancel</x-forms.button>

    PROPS
        variant   string   'primary'   'primary' (solid copper) or 'secondary' (outlined)

    Everything else - type="submit", wire:click, data-test, disabled,
    wire:loading.attr="disabled" - is NOT in @props, so it flows through
    via $attributes->merge() exactly like forms.input above.
--}}
@props([
    'variant' => 'primary',
])

@php
    // Keeping the variant styles in a small PHP array (rather than an
    // @if/@elseif chain in the markup) is what makes adding a third
    // variant later ("danger", "ghost") a one-line change instead of a
    // markup edit. Same pattern Flux itself uses internally.
    $variants = [
        'primary' => 'bg-copper-500 hover:bg-copper-600 text-linen-50 border border-transparent',
        'secondary' =>
            'bg-transparent hover:bg-ink-50 dark:hover:bg-ink-800 text-ink-800 dark:text-linen-100 border border-ink-200 dark:border-ink-700',
        // No border, no background, even at rest - for low-emphasis actions
        // like "log out" that shouldn't visually compete with the primary
    // action on the same screen (e.g. "resend verification email").
    'ghost' =>
        'bg-transparent hover:bg-ink-50 dark:hover:bg-ink-800 text-ink-600 dark:text-linen-300 border border-transparent',
    // Solid red - destructive actions only ("Delete account",
    // "Disable 2FA", "Remove passkey"). Deliberately visually
    // louder than 'primary' so it never gets mistaken for the
    // main call-to-action on a page that also has one.
    'danger' => 'bg-danger-500 hover:bg-danger-600 text-linen-50 border border-transparent',
    ];
@endphp

<button {{--
        Note there's no wire:loading handling baked in here on purpose:
        these auth pages are plain Blade forms posted to Fortify's own
        controllers, not Livewire components, so there's no
        $wire.loading state to hook into. If we later use this same
        component inside an actual Livewire form (e.g. an admin content
        editor), wire:loading.attr="disabled" can just be passed in on
        the tag and $attributes->merge() will carry it through
        untouched - no change needed to this file.
    --}}
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center justify-center gap-2 rounded-md
                            px-4 py-2 text-sm font-medium
                            transition-colors duration-150
                            focus:outline-none focus:ring-2 focus:ring-copper-400 focus:ring-offset-2
                            disabled:opacity-50 disabled:cursor-not-allowed
                            ' . $variants[$variant],
    ]) }}>
    {{ $slot }}
</button>
