<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="antialiased bg-linen-50 dark:bg-ink-950 text-ink-950 dark:text-linen-50">
        <x-website.nav />

        <main id="main-content">
            {{ $slot }}
        </main>

        <x-website.footer />
    </body>
</html>
