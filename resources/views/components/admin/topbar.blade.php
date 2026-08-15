{{--
    Admin topbar. Sits inside the same x-data="{ sidebarOpen }" scope as
    the sidebar (see layouts/admin.blade.php), plus its own local state
    for the notification/profile dropdowns.
--}}
<header x-data="{ notifOpen: false, profileOpen: false }"
    class="sticky top-0 z-20 h-16 flex items-center gap-3 px-4 sm:px-6 border-b border-ink-900/10 dark:border-linen-100/10 bg-linen-50/95 dark:bg-ink-950/95 backdrop-blur">
    {{-- Opens the sidebar on mobile - sidebarOpen lives one level up, in the layout --}}
    <button @click="sidebarOpen=true"
        class="lg:hidden w-10 h-10 grid place-items-center rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/10"
        aria-label="Open menu">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Was x-text="currentPageLabel()" (client-side page var) - now a real
         server-side lookup via the same AdminNav data the sidebar uses. --}}
    <h1 class="font-display font-semibold text-lg sm:text-xl truncate">{{ \App\Support\AdminNav::currentLabel() }}</h1>

    {{-- Static for now - no search backend exists yet, this gets wired once a page needs it --}}
    <label class="relative ml-auto hidden sm:block w-56 lg:w-72">
        <span class="sr-only">Search</span>
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-900/40 dark:text-linen-100/40"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="search" placeholder="Search records…"
            class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500" />
    </label>

    <div class="flex items-center gap-1 sm:ml-2 ml-auto sm:ml-0">

        {{-- Dark mode toggle - same localStorage key/logic as settings/appearance.blade.php, just a single icon button here instead of a segmented control --}}
        <div x-data="{
            theme: localStorage.getItem('anesmavisa-theme') ||
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
            setTheme(value) {
                this.theme = value;
                localStorage.setItem('anesmavisa-theme', value);
                document.documentElement.classList.toggle('dark', value === 'dark');
            },
        }">
            <button @click="setTheme(theme === 'dark' ? 'light' : 'dark')"
                class="w-10 h-10 grid place-items-center rounded-md text-ink-900 dark:text-copper-300 hover:bg-ink-900/5 dark:hover:bg-linen-100/10 transition-colors"
                :aria-label="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="theme !== 'dark'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v1.5m0 15V21m8.485-8.485H19M5 12H3.515m13.435 6.364l-1.06-1.06M6.11 6.11l-1.06-1.06m12.02 0l-1.06 1.06M6.11 17.89l-1.06 1.06M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
            </button>
        </div>

        {{-- Notifications - UI shell only. No notifications table/backend
             exists yet, so this shows an honest empty state rather than
             fake data. Wire this up once a real notification source exists. --}}
        <div class="relative" @click.outside="notifOpen=false">
            <button @click="notifOpen=!notifOpen"
                class="relative w-10 h-10 grid place-items-center rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/10"
                aria-label="Notifications" :aria-expanded="notifOpen">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </button>
            <div x-cloak x-show="notifOpen" x-transition
                class="absolute right-0 mt-2 w-80 max-w-[90vw] rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900 shadow-xl overflow-hidden">
                <p
                    class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50 px-4 pt-3 pb-2">
                    Notifications</p>
                <p class="px-4 py-6 text-sm text-center text-ink-900/50 dark:text-linen-100/50">No new notifications.
                </p>
            </div>
        </div>

        {{-- Profile - real Auth::user() data, initials() from the User model --}}
        <div class="relative" @click.outside="profileOpen=false">
            <button @click="profileOpen=!profileOpen"
                class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/10"
                :aria-expanded="profileOpen">
                <span
                    class="w-8 h-8 rounded-full bg-copper-500 text-ink-950 grid place-items-center font-display font-semibold text-sm">
                    {{ auth()->user()->initials() }}
                </span>
                <svg class="w-4 h-4 hidden sm:block text-ink-900/50 dark:text-linen-100/50" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-cloak x-show="profileOpen" x-transition
                class="absolute right-0 mt-2 w-56 rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900 shadow-xl overflow-hidden text-sm">
                <div class="px-4 py-3 border-b border-ink-900/10 dark:border-linen-100/10">
                    <p class="font-semibold text-ink-950 dark:text-linen-50">{{ auth()->user()->name }}</p>
                    <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50">
                        {{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->email }}</p>
                </div>

                <a href="{{ route('admin.settings.company') }}" wire:navigate
                    class="block px-4 py-2.5 hover:bg-ink-900/5 dark:hover:bg-linen-100/5 text-ink-900 dark:text-linen-100">
                    Company settings
                </a>

                {{-- Only admins manage other users - editors don't get this option --}}
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.settings.users') }}" wire:navigate
                        class="block px-4 py-2.5 hover:bg-ink-900/5 dark:hover:bg-linen-100/5 text-ink-900 dark:text-linen-100">
                        Manage users
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}"
                    class="border-t border-ink-900/10 dark:border-linen-100/10">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2.5 hover:bg-ink-900/5 dark:hover:bg-linen-100/5 text-danger-500">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
