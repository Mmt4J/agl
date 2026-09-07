<div class="space-y-4">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            @foreach ([['value' => null, 'label' => 'All'], ['value' => 'subscribed', 'label' => 'Subscribed'], ['value' => 'unsubscribed', 'label' => 'Unsubscribed']] as $option)
                <button type="button"
                    wire:click="filterByStatus({{ $option['value'] ? "'{$option['value']}'" : 'null' }})"
                    class="px-3 py-1.5 rounded-full text-xs font-medium border capitalize transition
                        {{ $statusFilter === $option['value']
                            ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500'
                            : 'border-ink-900/15 dark:border-linen-100/15 hover:bg-ink-900/5 dark:hover:bg-linen-100/5' }}">
                    {{ $option['label'] }}
                </button>
            @endforeach
        </div>

        <button type="button" wire:click="exportCsv" wire:loading.attr="disabled" wire:target="exportCsv"
            class="inline-flex items-center gap-2 rounded-md border border-ink-900/15 dark:border-linen-100/15 px-3 py-1.5 text-xs hover:bg-ink-900/5 dark:hover:bg-linen-100/5 disabled:opacity-50">
            <svg wire:loading.remove wire:target="exportCsv" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <svg wire:loading wire:target="exportCsv" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            <span wire:loading.remove wire:target="exportCsv">Export CSV</span>
            <span wire:loading wire:target="exportCsv">Preparing…</span>
        </button>
    </div>

    {{-- Desktop table --}}
    <div
        class="hidden md:block overflow-hidden rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40">
        <table class="w-full text-sm">
            <thead class="bg-ink-900/5 dark:bg-linen-100/5 text-left text-xs uppercase">
                <tr>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Subscribed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/10 dark:divide-linen-100/10">
                @forelse ($this->subscribers as $subscriber)
                    <tr wire:key="subscriber-{{ $subscriber->id }}"
                        class="hover:bg-ink-900/5 dark:hover:bg-linen-100/5">
                        <td class="px-5 py-3 font-mono text-xs">{{ $subscriber->email }}</td>
                        <td class="px-5 py-3">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs capitalize
                                {{ $subscriber->status === 'subscribed'
                                    ? 'bg-sage-500/15 text-sage-600 dark:text-sage-400'
                                    : 'bg-danger-500/15 text-danger-600 dark:text-danger-400' }}">
                                {{ $subscriber->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs opacity-60">
                            {{ optional($subscriber->subscribed_at)->format('M d, Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-xs opacity-60">No subscribers match this
                            filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->subscribers as $subscriber)
            <div wire:key="subscriber-mobile-{{ $subscriber->id }}"
                class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-4 flex justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate font-mono text-xs">{{ $subscriber->email }}</p>
                    <p class="mt-1 text-[10px] opacity-50">
                        {{ optional($subscriber->subscribed_at)->format('M d, Y H:i') }}</p>
                </div>
                <span
                    class="h-fit rounded-full px-2 py-1 text-[10px] capitalize
                    {{ $subscriber->status === 'subscribed'
                        ? 'bg-sage-500/15 text-sage-600 dark:text-sage-400'
                        : 'bg-danger-500/15 text-danger-600 dark:text-danger-400' }}">
                    {{ $subscriber->status }}
                </span>
            </div>
        @empty
            <p class="text-xs opacity-60 text-center py-8">No subscribers match this filter.</p>
        @endforelse
    </div>

    {{ $this->subscribers->links() }}

</div>
