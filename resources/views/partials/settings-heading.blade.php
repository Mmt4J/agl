{{--
    resources/views/partials/settings-heading.blade.php
    ------------------------------------------------------------------
    The big "Settings" page title shown once at the top of every
    settings page (Profile/Security/Appearance), above the sidebar +
    content split. Direct replacement for flux:heading/subheading/separator.
--}}
<div class="relative mb-6 w-full">
    <h1 class="font-display text-2xl font-semibold text-ink-950 dark:text-linen-50">
        {{ __('Settings') }}
    </h1>
    <p class="text-sm text-ink-600 dark:text-linen-300 mb-6">
        {{ __('Manage your profile and account settings') }}
    </p>
    <div class="border-t border-ink-100 dark:border-ink-800"></div>
</div>
