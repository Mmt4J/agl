@props([
    'title',
    'categoryName' => null,
    'class' => 'h-48',
])

<div {{ $attributes->merge([
    'class' => 'w-full ' . $class . ' grid place-items-center bg-ink-900 dark:bg-linen-100 text-linen-50 dark:text-ink-950 overflow-hidden relative',
    'role' => 'img',
    'aria-label' => 'Placeholder image for ' . $title,
]) }}>
    <div class="text-center px-4">
        @if ($categoryName)
            <p class="font-mono text-[10px] uppercase tracking-widest opacity-60 mb-1">{{ $categoryName }}</p>
        @endif
        <p class="font-display text-lg font-semibold leading-snug">{{ $title }}</p>
    </div>
</div>