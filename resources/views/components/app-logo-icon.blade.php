{{--
    resources/views/components/app-logo-icon.blade.php
    ------------------------------------------------------------------
    Your actual brand mark - the wax-seal circle with "AGL" lettering -
    lifted directly from index.html's header (lines ~157-162), not
    Laravel's default logo shape.

    USAGE
        <x-app-logo-icon class="w-10 h-10" />

    No @props here on purpose: this component takes NO custom props at
    all, only passthrough $attributes (usually just a sizing class),
    same as the original Laravel version did. Keeping it prop-free
    means it behaves like a plain, reusable <svg> you can drop
    anywhere and resize with ordinary Tailwind classes.
--}}
<svg viewBox="0 0 48 48" {{ $attributes }} aria-hidden="true">
    {{-- Outer seal disc --}}
    <circle cx="24" cy="24" r="22" class="fill-ink-900 dark:fill-linen-100" />
    <circle cx="24" cy="24" r="22" fill="none" class="stroke-copper-400" stroke-width="1" />

    {{-- Dashed inner ring - the "wax seal" detail --}}
    <circle cx="24" cy="24" r="17.5" fill="none" class="stroke-copper-400/70" stroke-width="1"
        stroke-dasharray="2 3" />

    {{-- Monogram. font-display pulls Zilla Slab from app.css, matching
         every other heading in the app - even inside an <svg>. --}}
    <text x="24" y="29" text-anchor="middle" class="font-display fill-linen-50 dark:fill-ink-900" font-size="15"
        font-weight="600">AGL</text>
</svg>
