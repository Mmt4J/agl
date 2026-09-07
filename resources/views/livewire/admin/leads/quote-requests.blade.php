<div class="space-y-4">

    <div class="flex flex-wrap gap-2">
        @foreach ([['value' => null, 'label' => 'All'], ['value' => 'new', 'label' => 'New'], ['value' => 'contacted', 'label' => 'Contacted'], ['value' => 'quoted', 'label' => 'Quoted'], ['value' => 'won', 'label' => 'Won'], ['value' => 'lost', 'label' => 'Lost']] as $option)
            <button type="button" wire:click="filterByStatus({{ $option['value'] ? "'{$option['value']}'" : 'null' }})"
                class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors
                    {{ $statusFilter === $option['value']
                        ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500'
                        : 'border-ink-900/15 dark:border-linen-100/15 hover:bg-ink-900/5 dark:hover:bg-linen-100/5' }}">
                {{ $option['label'] }}
            </button>
        @endforeach
    </div>

    <div
        class="rounded-md border border-ink-900/10 dark:border-linen-100/10 divide-y divide-ink-900/10 dark:divide-linen-100/10 overflow-hidden bg-white dark:bg-ink-900/40">
        @forelse ($this->requests as $request)
            <button type="button" wire:key="quote-request-{{ $request->id }}"
                wire:click="viewRequest({{ $request->id }})"
                class="w-full text-left flex items-start gap-4 px-4 sm:px-6 py-4 hover:bg-ink-900/[0.02] dark:hover:bg-linen-100/[0.03]">
                <span
                    class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $request->status === 'new' ? 'bg-copper-500' : 'bg-transparent' }}"></span>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p
                            class="text-sm truncate {{ $request->status === 'new' ? 'font-semibold text-ink-950 dark:text-linen-50' : 'font-medium text-ink-800 dark:text-linen-100' }}">
                            {{ $request->full_name }}
                        </p>
                        <span
                            class="text-xs text-ink-900/40 dark:text-linen-100/40 shrink-0">{{ $request->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-ink-900/60 dark:text-linen-100/60 truncate">
                        {{ $request->service?->name ?? 'No service specified' }}</p>
                    <p class="text-xs text-ink-900/40 dark:text-linen-100/40 truncate mt-0.5">{{ $request->details }}
                    </p>
                </div>

                @if ($request->preferred_date)
                    <span
                        class="hidden sm:block font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40 shrink-0 mt-1">
                        {{ $request->preferred_date->format('d M') }}
                    </span>
                @endif

                <span
                    class="font-mono text-[10px] uppercase px-2 py-1 rounded-full shrink-0
                    {{ match ($request->status) {
                        'new' => 'bg-copper-500/15 text-copper-600 dark:text-copper-300',
                        'contacted' => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                        'quoted' => 'bg-sage-500/15 text-sage-600 dark:text-sage-400',
                        'won' => 'bg-sage-500/25 text-sage-700 dark:text-sage-300',
                        'lost' => 'bg-ink-900/5 dark:bg-linen-100/5 text-ink-900/40 dark:text-linen-100/40',
                        default => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                    } }}">
                    {{ $request->status }}
                </span>
            </button>
        @empty
            <p class="text-sm text-ink-900/40 dark:text-linen-100/40 px-6 py-8 text-center">No quote requests match this
                filter.</p>
        @endforelse
    </div>

    {{ $this->requests->links() }}

    <x-forms.panel name="quote-detail">
        @if ($this->viewingRequest)
            <div class="p-6 space-y-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-display text-lg font-semibold truncate">{{ $this->viewingRequest->full_name }}
                        </h2>
                        <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50 truncate">
                            {{ $this->viewingRequest->email }}</p>
                    </div>
                    <button type="button" @click="close()"
                        class="shrink-0 text-ink-900/50 dark:text-linen-100/50 hover:text-ink-900 dark:hover:text-linen-50"
                        aria-label="Close">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Phone</p>
                        <p class="text-sm">{{ $this->viewingRequest->phone }}</p>
                    </div>
                    <div>
                        <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Service
                        </p>
                        <p class="text-sm">{{ $this->viewingRequest->service?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Preferred
                            date</p>
                        <p class="text-sm">
                            {{ $this->viewingRequest->preferred_date?->format('M d, Y') ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Submitted
                        </p>
                        <p class="text-sm">{{ $this->viewingRequest->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Details</p>
                    <p class="text-sm whitespace-pre-line text-ink-800 dark:text-linen-100">
                        {{ $this->viewingRequest->details }}</p>
                </div>

                <form wire:submit="saveRequest"
                    class="space-y-4 pt-2 border-t border-ink-900/10 dark:border-linen-100/10">
                    <div class="flex flex-col gap-1.5">
                        <label for="viewingStatus"
                            class="text-sm font-medium text-ink-800 dark:text-linen-100">Status</label>
                        <select wire:model="viewingStatus" id="viewingStatus"
                            class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="quoted">Quoted</option>
                            <option value="won">Won</option>
                            <option value="lost">Lost</option>
                        </select>
                        @error('viewingStatus')
                            <p class="text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="viewingNotes" class="text-sm font-medium text-ink-800 dark:text-linen-100">Internal
                            notes</label>
                        <textarea wire:model="viewingNotes" id="viewingNotes" rows="3" placeholder="Not visible to the customer"
                            class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"></textarea>
                        @error('viewingNotes')
                            <p class="text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-forms.button type="submit" variant="primary" class="w-full">Save</x-forms.button>
                </form>

                <a href="mailto:{{ $this->viewingRequest->email }}?subject=Re: your quote request"
                    class="block text-center text-sm text-copper-600 dark:text-copper-300 hover:text-copper-700 dark:hover:text-copper-200">
                    Reply by email →
                </a>
            </div>
        @endif
    </x-forms.panel>
</div>
