<div class="space-y-4">

    <div class="flex flex-wrap items-center gap-2">
        @foreach ([
            ['value' => null, 'label' => 'All'],
            ['value' => 'new', 'label' => 'New'],
            ['value' => 'contacted', 'label' => 'Contacted'],
            ['value' => 'quoted', 'label' => 'Quoted'],
            ['value' => 'won', 'label' => 'Won'],
            ['value' => 'lost', 'label' => 'Lost'],
        ] as $option)
            <button
                type="button"
                wire:click="filterByStatus({{ $option['value'] ? "'{$option['value']}'" : 'null' }})"
                class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors
                    {{ $statusFilter === $option['value']
                        ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500'
                        : 'border-ink-900/15 dark:border-linen-100/15 hover:bg-ink-900/5 dark:hover:bg-linen-100/5' }}"
            >
                {{ $option['label'] }}
            </button>
        @endforeach

        <span class="ml-auto font-mono text-xs text-ink-900/40 dark:text-linen-100/40">
            {{ $this->requests->total() }} {{ Str::plural('request', $this->requests->total()) }}
        </span>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-ink-900/[0.03] dark:bg-linen-100/[0.04] text-left font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Service</th>
                    <th class="px-5 py-3">Preferred date</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Received</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/10 dark:divide-linen-100/10">
                @forelse ($this->requests as $request)
                    <tr
                        wire:key="request-{{ $request->id }}"
                        wire:click="viewRequest({{ $request->id }})"
                        class="hover:bg-ink-900/[0.02] dark:hover:bg-linen-100/[0.03] cursor-pointer"
                    >
                        <td class="px-5 py-3.5">
                            <p class="font-medium">{{ $request->full_name }}</p>
                            <p class="font-mono text-xs text-ink-900/45 dark:text-linen-100/45">{{ $request->email }}</p>
                        </td>
                        <td class="px-5 py-3.5">{{ $request->service?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-mono text-xs">{{ $request->preferred_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-mono capitalize {{ match ($request->status) {
                                'new' => 'bg-copper-500/15 text-copper-600 dark:text-copper-300',
                                'contacted' => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                                'quoted' => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                                'won' => 'bg-sage-500/15 text-sage-600 dark:text-sage-400',
                                'lost' => 'bg-danger-500/15 text-danger-600 dark:text-danger-400',
                            } }}">{{ $request->status }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs text-ink-900/50 dark:text-linen-100/50">{{ $request->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <svg class="w-4 h-4 inline text-ink-900/30 dark:text-linen-100/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-sm text-ink-900/50 dark:text-linen-100/50 py-12">No quote requests with this status.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile stacked cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->requests as $request)
            <button
                type="button"
                wire:key="request-card-{{ $request->id }}"
                wire:click="viewRequest({{ $request->id }})"
                class="w-full text-left rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-4"
            >
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-sm">{{ $request->full_name }}</p>
                        <p class="font-mono text-xs text-ink-900/45 dark:text-linen-100/45">{{ $request->service?->name ?? '—' }}</p>
                    </div>
                    <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-mono capitalize shrink-0 {{ match ($request->status) {
                        'new' => 'bg-copper-500/15 text-copper-600 dark:text-copper-300',
                        'contacted' => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                        'quoted' => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                        'won' => 'bg-sage-500/15 text-sage-600 dark:text-sage-400',
                        'lost' => 'bg-danger-500/15 text-danger-600 dark:text-danger-400',
                    } }}">{{ $request->status }}</span>
                </div>
                <p class="font-mono text-[11px] text-ink-900/40 dark:text-linen-100/40 mt-3">{{ $request->created_at->diffForHumans() }}</p>
            </button>
        @empty
            <p class="text-center text-sm text-ink-900/50 dark:text-linen-100/50 py-12">No quote requests with this status.</p>
        @endforelse
    </div>

    {{ $this->requests->links() }}

    <x-forms.panel name="quote-detail">
        @if ($this->viewingRequest)
            <div class="flex items-center justify-between px-5 py-4 border-b border-ink-900/10 dark:border-linen-100/10 sticky top-0 bg-linen-50 dark:bg-ink-950">
                <h2 class="font-display font-semibold">Quote request</h2>
                <button type="button" @click="close()" class="w-9 h-9 grid place-items-center rounded-md hover:bg-ink-900/5 dark:hover:bg-linen-100/10" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 space-y-5">
                <div>
                    <p class="font-display font-semibold text-lg">{{ $this->viewingRequest->full_name }}</p>
                    <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50">{{ $this->viewingRequest->email }}</p>
                    <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50">{{ $this->viewingRequest->phone }}</p>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40 mb-1">Service</p>
                    <p class="text-sm">{{ $this->viewingRequest->service?->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40 mb-1">Preferred date</p>
                    <p class="text-sm font-mono">{{ $this->viewingRequest->preferred_date?->format('M d, Y') ?? 'Not specified' }}</p>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40 mb-1">Details</p>
                    <p class="text-sm leading-relaxed whitespace-pre-line">{{ $this->viewingRequest->details ?? '—' }}</p>
                </div>

                <form wire:submit="saveRequest" class="space-y-4 pt-2 border-t border-ink-900/10 dark:border-linen-100/10">
                    <div class="flex flex-col gap-1.5">
                        <label for="viewingStatus" class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40">Status</label>
                        <select wire:model="viewingStatus" id="viewingStatus" class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-3 py-2 text-sm capitalize focus:outline-none focus:ring-2 focus:ring-copper-500">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="quoted">Quoted</option>
                            <option value="won">Won</option>
                            <option value="lost">Lost</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="viewingNotes" class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40">Internal notes</label>
                        <textarea
                            wire:model="viewingNotes"
                            id="viewingNotes"
                            rows="3"
                            placeholder="Not visible to the client…"
                            class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500"
                        ></textarea>
                    </div>

                    <x-forms.button type="submit" variant="primary" class="w-full">Save</x-forms.button>
                </form>
            </div>
        @endif
    </x-forms.panel>
</div>