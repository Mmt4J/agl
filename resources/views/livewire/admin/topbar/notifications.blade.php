<div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
    <button @click="notifOpen = !notifOpen"
        class="relative w-10 h-10 grid place-items-center rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/10"
        aria-label="Notifications" :aria-expanded="notifOpen">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if ($this->unreadCount > 0)
            <span
                class="absolute top-1 right-1 min-w-4 h-4 px-1 rounded-full bg-danger-500 text-white font-mono text-[10px] grid place-items-center">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Mobile scrim: on small screens the tray becomes a fixed sheet (below),
         so a scrim behind it keeps it visually separated from page content
         instead of appearing to overlap it. --}}
    <div x-show="notifOpen" x-cloak x-transition.opacity class="sm:hidden fixed inset-0 z-30 bg-ink-950/20"
        @click="notifOpen = false"></div>

    {{--
        FIX: the old markup put the "Notifications" label and all 4 filter
        tabs on a single `justify-between` row inside a fixed w-80 box with
        `overflow-hidden`. That's more text than 320px can hold, and because
        the parent clips overflow instead of scrolling it, the last tab
        ("Newsletter") got visually cut off to "News1" — that was the bug,
        not a data/wire issue.

        Fix: label and tabs are now stacked on separate rows, and the tab
        row is its own horizontally-scrollable strip, so it can never clip
        a label again regardless of viewport width. On mobile the tray is
        `fixed` under the header (like the search dropdown pattern) instead
        of an `absolute` dropdown, so it can't collide with page content.
    --}}
    <div x-cloak x-show="notifOpen" x-transition
        class="z-40 fixed inset-x-3 top-16 sm:absolute sm:inset-x-auto sm:top-auto sm:right-0 sm:mt-2
               w-auto sm:w-96 max-h-[75vh] sm:max-h-[28rem]
               flex flex-col rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900 shadow-xl overflow-hidden">

        <div class="shrink-0 px-4 pt-3 pb-2">
            <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">
                Notifications</p>
        </div>

        <div class="shrink-0 flex items-center gap-1.5 px-4 pb-2 overflow-x-auto no-scrollbar">
            @foreach ([['value' => 'all', 'label' => 'All'], ['value' => 'quotes', 'label' => 'Quotes'], ['value' => 'messages', 'label' => 'Messages'], ['value' => 'subscribers', 'label' => 'Newsletter']] as $tab)
                <button type="button" wire:click="filterByType('{{ $tab['value'] }}')"
                    class="shrink-0 whitespace-nowrap font-mono text-[10px] px-2.5 py-1 rounded-full border transition-colors
                        {{ $typeFilter === $tab['value']
                            ? 'bg-copper-500 text-ink-950 border-copper-500'
                            : 'border-ink-900/15 dark:border-linen-100/15 text-ink-900/60 dark:text-linen-100/60 hover:bg-ink-900/5 dark:hover:bg-linen-100/5' }}">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <ul class="grow overflow-y-auto divide-y divide-ink-900/10 dark:divide-linen-100/10">
            @forelse ($this->items as $item)
                <li wire:key="{{ $item['key'] }}">
                    <a href="{{ $item['url'] }}" wire:navigate @click="notifOpen = false"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-ink-900/5 dark:hover:bg-linen-100/5">
                        <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 bg-copper-500"></span>
                        <span class="min-w-0 flex-1">
                            <span
                                class="block text-sm truncate text-ink-950 dark:text-linen-50">{{ $item['title'] }}</span>
                            <span
                                class="block text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $item['meta'] }}</span>
                            <span
                                class="block font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40 mt-0.5">{{ $item['time'] }}</span>
                        </span>
                    </a>
                </li>
            @empty
                <li class="px-4 py-6 text-sm text-center text-ink-900/50 dark:text-linen-100/50">
                    {{ $typeFilter === 'all' ? 'No notifications.' : 'No ' . $typeFilter . '.' }}
                </li>
            @endforelse
        </ul>

        <div class="shrink-0 border-t border-ink-900/10 dark:border-linen-100/10 p-2">
            <button type="button" wire:click="markAllRead"
                class="w-full text-center font-mono text-xs text-ink-900/60 dark:text-linen-100/60 hover:text-copper-600 dark:hover:text-copper-300 py-1.5 rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/5 transition-colors">
                Mark all as read
            </button>
        </div>
    </div>
</div>
