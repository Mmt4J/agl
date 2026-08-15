{{--
    Admin dashboard shell. Wraps every admin page: sidebar + topbar +
    content slot. Applied per-page via #[Layout('layouts::admin')].
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="antialiased bg-linen-50 dark:bg-ink-950 text-ink-950 dark:text-linen-50">
        <div x-data="{ sidebarOpen: false }">
            <x-admin.sidebar />

            {{-- Offset by sidebar width on desktop, full width on mobile --}}
            <div class="lg:pl-72 min-h-screen flex flex-col">
                <x-admin.topbar />

                <main class="flex-1 p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>