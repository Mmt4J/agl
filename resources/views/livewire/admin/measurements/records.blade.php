<div class="space-y-4">
    <p class="text-sm text-ink-900/60 dark:text-linen-100/60">
        Browse every customer measurement on file. Search by customer code, name, phone or email.
    </p>

    <x-forms.input
        wire:model.live.debounce.300ms="search"
        name="search"
        label="Search records"
        type="text"
        placeholder="Search by code, name, phone or email…"
        class="max-w-md"
    />

    <div
        class="rounded-md border border-ink-900/10 dark:border-linen-100/10 divide-y divide-ink-900/10 dark:divide-linen-100/10 overflow-hidden bg-white dark:bg-ink-900/40">
        @forelse ($this->records as $record)
            <div wire:key="record-{{ $record->id }}"
                class="flex items-start gap-3 px-4 sm:px-6 py-4">
                <button type="button" wire:click="viewRecord({{ $record->id }})" class="min-w-0 flex-1 text-left flex items-start gap-3">
                    <span class="mt-1 font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300 shrink-0">
                        {{ $record->customer_code }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium text-ink-900 dark:text-linen-50 truncate">{{ $record->full_name }}</span>
                        <span class="block text-xs text-ink-900/40 dark:text-linen-100/40 truncate mt-0.5">{{ $record->phone }}</span>
                    </span>
                </button>

                <button type="button" wire:click="viewRecord({{ $record->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">View</button>
                <button type="button" wire:click="editRecord({{ $record->id }})" class="text-xs shrink-0" aria-label="Edit this record">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                </button>
                <button type="button" wire:click="confirmDelete({{ $record->id }})" class="text-danger-500 shrink-0" aria-label="Delete this record">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @empty
            <p class="text-sm text-ink-900/40 dark:text-linen-100/40 px-6 py-8 text-center">
                {{ trim($search) !== '' ? 'No records match your search.' : 'No measurements recorded yet.' }}
            </p>
        @endforelse
    </div>

    {{ $this->records->links() }}

    <x-forms.panel name="measurement-detail">
        @if ($this->viewingRecord)
            @php
                $measurement = $this->viewingRecord;
                $keepFilled = fn ($value) => $value !== null && $value !== '';

                $upper = collect([
                    ['label' => 'Chest', 'value' => $measurement->chest],
                    ['label' => 'Bust', 'value' => $measurement->bust],
                    ['label' => 'Waist', 'value' => $measurement->waist],
                    ['label' => 'Shoulder', 'value' => $measurement->shoulder],
                    ['label' => 'Arm length', 'value' => $measurement->arm_length],
                    ['label' => 'Top length', 'value' => $measurement->top_length],
                    ['label' => 'Half bust', 'value' => $measurement->half_bust],
                    ['label' => 'Half length', 'value' => $measurement->half_length],
                    ['label' => 'Round sleeve', 'value' => $measurement->round_sleeve],
                    ['label' => 'Sleeve length', 'value' => $measurement->length_sleeve],
                ])->filter(fn ($row) => $keepFilled($row['value']))->values();

                $lower = collect([
                    ['label' => 'Hip', 'value' => $measurement->hip],
                    ['label' => 'Inseam', 'value' => $measurement->inseam],
                    ['label' => 'Thigh', 'value' => $measurement->thigh],
                    ['label' => 'Ankle', 'value' => $measurement->ankle],
                    ['label' => 'Trouser / skirt length', 'value' => $measurement->trouser_skirt_length],
                ])->filter(fn ($row) => $keepFilled($row['value']))->values();

                $overall = collect([
                    ['label' => 'Height', 'value' => $measurement->height],
                    ['label' => 'Weight (kg)', 'value' => $measurement->weight],
                    ['label' => 'Dress size', 'value' => $measurement->dress_size],
                    ['label' => 'Gown length', 'value' => $measurement->gown_length],
                ])->filter(fn ($row) => $keepFilled($row['value']))->values();
            @endphp

            <div class="p-6 space-y-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300">{{ $measurement->customer_code }}</span>
                            <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60">{{ $measurement->unit === 'cm' ? 'cm' : 'in' }}</span>
                        </div>
                        <h2 class="font-display text-lg font-semibold truncate mt-2">{{ $measurement->full_name }}</h2>
                        <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $measurement->phone }}</p>
                        @if ($measurement->email)
                            <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $measurement->email }}</p>
                        @endif
                    </div>
                    <button type="button" @click="close()"
                        class="shrink-0 text-ink-900/50 dark:text-linen-100/50 hover:text-ink-900 dark:hover:text-linen-50"
                        aria-label="Close">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    @php
                        $groups = [
                            ['title' => 'Upper body', 'rows' => $upper],
                            ['title' => 'Lower body', 'rows' => $lower],
                            ['title' => 'Overall', 'rows' => $overall],
                        ];
                    @endphp

                    @foreach ($groups as $group)
                        @if ($group['rows']->isEmpty())
                            @continue
                        @endif

                        <div x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 mb-2">
                                <span class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40">{{ $group['title'] }}</span>
                                <svg :class="open ? '' : '-rotate-90'" class="w-4 h-4 text-ink-900/40 dark:text-linen-100/40 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-show="open" x-transition class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach ($group['rows'] as $row)
                                    <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 px-3 py-2">
                                        <p class="text-[10px] font-mono uppercase text-ink-900/40 dark:text-linen-100/40">{{ $row['label'] }}</p>
                                        <p class="text-sm">{{ number_format((float) $row['value'], 1) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if ($measurement->notes)
                        <div>
                            <p class="font-mono text-[10px] uppercase text-ink-900/40 dark:text-linen-100/40 mb-1">Notes</p>
                            <p class="text-sm whitespace-pre-line">{{ $measurement->notes }}</p>
                        </div>
                    @endif

                    <p class="text-xs text-ink-900/40 dark:text-linen-100/40">Last updated {{ $measurement->updated_at->format('M d, Y H:i') }}</p>
                </div>

                <div class="flex gap-3 pt-2 border-t border-ink-900/10 dark:border-linen-100/10">
                    <x-forms.button type="button" variant="primary" class="flex-1" wire:click="editRecord">Edit record</x-forms.button>
                    <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Close</x-forms.button>
                </div>
            </div>
        @endif
    </x-forms.panel>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this measurement record?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>