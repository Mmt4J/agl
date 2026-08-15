{{--
    resources/views/partials/head.blade.php
    ------------------------------------------------------------------
    Included inside <head> by every layout (layouts/auth/simple.blade.php,
    and later layouts/website.blade.php + layouts/admin.blade.php).

    Two directives that used to live here were Flux's:
      @fonts            - Flux's own font loader (Instrument Sans)
      @fluxAppearance   - an inline script that set the "dark" class
                           on <html> BEFORE first paint, so the page
                           never flashes the wrong theme on load

    Both are replaced below with our own equivalents, matching the
    palette/typography already defined in app.css and the exact
    localStorage key ('anesmavisa-theme') your prototype already uses.
--}}
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

{{--
    Replaces @fonts. Loading all three families (and the weights
    actually used in the design - Zilla Slab 500/600 for headings,
    Manrope 400/500/600 for body, Space Mono 400 for the RC-number/
    mono-styled labels) here instead of per-page means every page
    shares one cached request instead of re-fetching per route.
--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Zilla+Slab:wght@500;600;700&family=Manrope:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{--
    Replaces @fluxAppearance. This does TWO things, and both matter:

    1. Runs IMMEDIATELY, synchronously, before the browser paints
       anything - the only way to avoid a flash of the wrong theme on
       a normal hard page load (a fresh visit, a manual refresh).

    2. Re-runs after every wire:navigate transition too, via Livewire's
       own 'livewire:navigated' event. This part is NOT optional:
       wire:navigate swaps the page by morphing the DOM into what the
       server actually rendered - and the server-rendered <html> tag
       never has class="dark" in its markup, since dark mode is only
       ever applied client-side. Without this listener, the class gets
       silently wiped on every single navigation after the first page
       load - which is exactly the "works once, then reverts" bug.

    The logic mirrors your prototype's own toggleTheme()/init() exactly:
    explicit saved preference wins, otherwise fall back to the OS
    setting. classList.toggle() (not .add()) also means an explicit
    "light" choice is actively enforced, not just "dark never removed."
--}}
<script>
    (function () {
        function applyTheme() {
            var stored = localStorage.getItem('anesmavisa-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.classList.toggle('dark', stored === 'dark' || (!stored && prefersDark));
        }

        applyTheme();

        document.addEventListener('livewire:navigated', applyTheme);
    })();
</script>