{{--
    resources/views/layouts/account.blade.php
    ------------------------------------------------------------------
    Deliberately MINIMAL - a header (logo, Settings link, Log out) and
    a content slot. This is NOT the real admin dashboard layout - that
    gets built later from dashboard.html with its full sidebar/topbar
    and business-specific nav. This exists so pages that need to be
    "logged in and functional" (settings, the post-login landing page)
    have somewhere clean to render RIGHT NOW instead of falling back
    to Livewire's default layout - which is the old Flux-based
    layouts/app.blade.php + layouts/app/sidebar.blade.php pair, both
    now fully broken since Flux was removed.

    Once the real admin layout exists, settings pages' #[Layout(...)]
    attribute gets repointed to it and this file (along with
    layouts/app.blade.php, layouts/app/sidebar.blade.php,
    layouts/app/header.blade.php, and components/desktop-user-menu.blade.php)
    can be deleted - all four are dead the moment nothing points at
    them anymore, which is why we're doing that repointing now rather
    than leaving it as a vague "later."
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-linen-50 dark:bg-ink-950 text-ink-950 dark:text-linen-50">
    <header class="border-b border-ink-100 dark:border-ink-800">
        <div class="mx-auto max-w-5xl px-6 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                <x-app-logo-icon class="w-8 h-8" />
                <span class="font-display font-semibold text-ink-900 dark:text-linen-50">
                    Anesmavisa
                </span>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}" wire:navigate
                    class="text-sm text-ink-700 dark:text-linen-200 hover:text-ink-950 dark:hover:text-linen-50
                              data-current:text-copper-600 dark:data-current:text-copper-300 data-current:font-medium">
                    {{ __('Settings') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-forms.button variant="ghost" type="submit" class="text-sm" data-test="logout-button">
                        {{ __('Log out') }}
                    </x-forms.button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">
        {{ $slot }}
    </main>
</body>

</html>
