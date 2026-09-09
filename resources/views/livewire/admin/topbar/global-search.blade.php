<div class="relative" x-data="{ searchOpen: false }" @click.outside="searchOpen = false">
    <label class="relative block w-56 lg:w-72">
        <span class="sr-only">Search</span>
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-900/40 dark:text-linen-100/40"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="search" placeholder="Search records…" x-ref="searchInput"
            wire:model.live.debounce.250ms="query" @focus="searchOpen = true" @keydown.escape="searchOpen = false; $refs.searchInput.blur()"
            class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500" />
    </label>

    <div x-cloak x-show="searchOpen" x-transition
        class="absolute right-0 mt-2 w-80 max-w-[90vw] rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900 shadow-xl overflow-hidden">
        @if (trim($query) === '')
            <p class="px-4 py-6 text-sm text-center text-ink-900/50 dark:text-linen-100/50">Type to search across services, quotes, messages &amp; more…</p>
        @elseif ($this->hasResults)
            <ul class="max-h-96 overflow-y-auto divide-y divide-ink-900/10 dark:divide-linen-100/10">
                @foreach ($this->results as $type => $group)
                    <li class="px-4 pt-3 pb-1">
                        <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">
                            {{ $this->labelFor($type) }}</p>
                        <ul class="mt-1">
                            @foreach ($group as $result)
                                <li>
                                    <a href="{{ $this->urlFor($type) }}" wire:navigate @click="searchOpen = false"
                                        class="flex items-center gap-2 px-2 py-2 rounded-md text-sm hover:bg-ink-900/5 dark:hover:bg-linen-100/5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-copper-500 shrink-0"></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-ink-950 dark:text-linen-50">{{ $result['label'] }}</span>
                                            @if (!empty($result['meta']))
                                                <span class="block truncate text-xs text-ink-900/50 dark:text-linen-100/50">{{ $result['meta'] }}</span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="px-4 py-6 text-sm text-center text-ink-900/50 dark:text-linen-100/50">No matches for "{{ $query }}".</p>
        @endif
    </div>
</div>
