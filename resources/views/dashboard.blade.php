{{--
    resources/views/dashboard.blade.php
    ------------------------------------------------------------------
    Post-login landing page (config/fortify.php 'home' => '/dashboard').
    Original was Laravel's generic demo placeholder (3 gray boxes with
    a pattern SVG, x-placeholder-pattern) - meaningless filler content,
    not something worth reskinning. Replaced with an honest one-line
    placeholder instead. This whole file gets replaced again once the
    real admin dashboard (from dashboard.html) exists.
--}}
<x-layouts::account :title="__('Dashboard')">
    <div class="rounded-lg border border-ink-100 dark:border-ink-800 p-10 text-center">
        <p class="text-ink-600 dark:text-linen-300">
            {{ __('The admin dashboard lives here once it\'s built.') }}
        </p>
    </div>
</x-layouts::account>
