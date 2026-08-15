{{--
    resources/views/layouts/auth/simple.blade.php
    ------------------------------------------------------------------
    The actual <html> shell for every unauthenticated auth page
    (login, register, forgot/reset password, etc). Wired via
    layouts/auth.blade.php -> x-layouts::auth.simple.

    Three things changed from the original Flux version:
      1. class="dark" was hardcoded on <html> - removed. Whether dark
         mode is on is now decided entirely by partials/head.blade.php's
         blocking script (localStorage / OS preference), same as the
         rest of the site.
      2. @persist('toast') / <flux:toast.group> / @fluxScripts removed -
         no toast library exists anymore. Session status messages
         still work via <x-auth-session-status> inside each page.
      3. The generic centered logo link now uses your real seal mark +
         wordmark, matching index.html's header exactly, rather than a
         bare icon with a screen-reader-only app name.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-linen-50 dark:bg-ink-950 text-ink-950 dark:text-linen-50">
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-2">

            {{--
                    Same markup as index.html's header logo block, just
                    without the @scroll-driven sticky styling - this page
                    never scrolls past a fixed header, so none of that
                    applies here.
                --}}
            <a href="{{ route('website.home') }}" class="flex flex-col items-center gap-2 mb-1" wire:navigate>
                <x-app-logo-icon class="w-11 h-11" />
                <span class="flex flex-col items-center leading-none mt-1">
                    <span class="font-display font-semibold tracking-tight text-lg text-ink-900 dark:text-linen-50">
                        Anesmavisa
                    </span>
                    <span class="font-mono text-[10px] tracking-widest text-copper-600 dark:text-copper-300">
                        GLOBAL LTD · RC 9417288
                    </span>
                </span>
            </a>

            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
