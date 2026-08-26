<div class="space-y-4">

    <div class="flex flex-wrap gap-2">
        @foreach ([
            ['value' => null, 'label' => 'All'],
            ['value' => 'unread', 'label' => 'Unread'],
            ['value' => 'read', 'label' => 'Read'],
            ['value' => 'replied', 'label' => 'Replied'],
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
    </div>

    <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 divide-y divide-ink-900/10 dark:divide-linen-100/10 overflow-hidden bg-white dark:bg-ink-900/40">
        @forelse ($this->messages as $message)
            <button
                type="button"
                wire:key="message-{{ $message->id }}"
                wire:click="viewMessage({{ $message->id }})"
                class="w-full text-left flex items-start gap-4 px-4 sm:px-6 py-4 hover:bg-ink-900/[0.02] dark:hover:bg-linen-100/[0.03]"
            >
                {{-- Unread indicator dot - matches the prototype's row treatment --}}
                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $message->status === 'unread' ? 'bg-copper-500' : 'bg-transparent' }}"></span>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm truncate {{ $message->status === 'unread' ? 'font-semibold text-ink-950 dark:text-linen-50' : 'font-medium text-ink-800 dark:text-linen-100' }}">
                            {{ $message->full_name }}
                        </p>
                        <span class="text-xs text-ink-900/40 dark:text-linen-100/40 shrink-0">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-ink-900/60 dark:text-linen-100/60 truncate">{{ $message->subject }}</p>
                    <p class="text-xs text-ink-900/40 dark:text-linen-100/40 truncate mt-0.5">{{ $message->message }}</p>
                </div>

                <span class="font-mono text-[10px] uppercase px-2 py-1 rounded-full shrink-0
                    {{ match ($message->status) {
                        'unread' => 'bg-copper-500/15 text-copper-600 dark:text-copper-300',
                        'read' => 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60',
                        'replied' => 'bg-sage-500/15 text-sage-600 dark:text-sage-400',
                    } }}">
                    {{ $message->status }}
                </span>
            </button>
        @empty
            <p class="text-sm text-ink-900/40 dark:text-linen-100/40 px-6 py-8 text-center">No messages here.</p>
        @endforelse
    </div>

    {{ $this->messages->links() }}

    <x-forms.panel name="message-detail">
        @if ($this->viewingMessage)
            <div class="p-6 space-y-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-display text-lg font-semibold truncate">{{ $this->viewingMessage->full_name }}</h2>
                        <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $this->viewingMessage->email }}</p>
                    </div>
                    <button type="button" @click="close()" class="shrink-0 text-ink-900/50 dark:text-linen-100/50 hover:text-ink-900 dark:hover:text-linen-50" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Subject</p>
                    <p class="text-sm">{{ $this->viewingMessage->subject }}</p>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Message</p>
                    <p class="text-sm whitespace-pre-line text-ink-800 dark:text-linen-100">{{ $this->viewingMessage->message }}</p>
                </div>

                <div>
                    <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Received</p>
                    <p class="text-sm">{{ $this->viewingMessage->created_at->format('M d, Y H:i') }}</p>
                </div>

                <form wire:submit="updateStatus" class="space-y-4 pt-2 border-t border-ink-900/10 dark:border-linen-100/10">
                    <div class="flex flex-col gap-1.5">
                        <label for="viewingStatus" class="text-sm font-medium text-ink-800 dark:text-linen-100">Status</label>
                        <select wire:model="viewingStatus" id="viewingStatus" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                            <option value="replied">Replied</option>
                        </select>
                    </div>

                    <x-forms.button type="submit" variant="primary" class="w-full">Save</x-forms.button>
                </form>

                <a href="mailto:{{ $this->viewingMessage->email }}?subject=Re: {{ $this->viewingMessage->subject }}" class="block text-center text-sm text-copper-600 dark:text-copper-300 hover:text-copper-700 dark:hover:text-copper-200">
                    Reply by email →
                </a>
            </div>
        @endif
    </x-forms.panel>
</div>