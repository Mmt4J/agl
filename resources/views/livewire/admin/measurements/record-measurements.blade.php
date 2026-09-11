<div class="space-y-6">
    <p class="text-sm text-ink-900/60 dark:text-linen-100/60">
        Find an existing customer to update their record, or record a fresh set of measurements. One editable
        record per customer — re-measuring a customer updates their existing record instead of duplicating it.
    </p>

    <div class="relative max-w-md">
        <x-forms.input
            wire:model.live.debounce.300ms="findCustomer"
            name="findCustomer"
            label="Find an existing customer"
            type="text"
            placeholder="Search by code, name or phone…"
        />

        @if ($this->findCustomer !== '' && $this->lookupResults->isNotEmpty())
            <div
                class="absolute z-10 mt-1 w-full max-w-md rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900 shadow-lg divide-y divide-ink-900/10 dark:divide-linen-100/10 overflow-hidden">
                @foreach ($this->lookupResults as $match)
                    <button
                        type="button"
                        wire:key="match-{{ $match->id }}"
                        wire:click="selectCustomer({{ $match->id }})"
                        class="w-full text-left flex items-center gap-3 px-4 py-3 hover:bg-ink-900/[0.02] dark:hover:bg-linen-100/[0.03]">
                        <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300 shrink-0">
                            {{ $match->customer_code }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm truncate font-medium text-ink-900 dark:text-linen-50">{{ $match->full_name }}</span>
                            <span class="block text-xs text-ink-900/40 dark:text-linen-100/40 truncate">{{ $match->phone }}</span>
                        </span>
                        <span class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit →</span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <form wire:submit="save" class="max-w-3xl space-y-6">
        @if ($recordId)
            <div class="flex items-center gap-3 text-sm">
                <span>
                    Updating record
                    <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300">{{ $customerCode }}</span>
                </span>
                <button type="button" wire:click="startNew" class="text-xs text-copper-600 dark:text-copper-300">Start a new record →</button>
            </div>
        @endif

        <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-5 sm:p-6 space-y-4">
            <h2 class="font-display font-semibold">Customer</h2>

            @if (! $recordId)
                <p class="text-xs text-ink-900/50 dark:text-linen-100/50">
                    A unique code ({{ \App\Models\CustomerMeasurement::CODE_PREFIX }}0001) is assigned automatically on save.
                </p>
            @endif

            <div class="grid sm:grid-cols-2 gap-4">
                <x-forms.input wire:model="fullName" name="fullName" label="Full name" type="text" required />
                <x-forms.input wire:model="phone" name="phone" label="Phone" type="text" required />
            </div>

            <x-forms.input wire:model="email" name="email" label="Email" type="email" />
        </div>

        <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h2 class="font-display font-semibold">Units</h2>
                <div class="flex gap-2">
                    @foreach ([['value' => 'in', 'label' => 'Inches'], ['value' => 'cm', 'label' => 'Centimetres']] as $option)
                        <button
                            type="button"
                            wire:click="switchUnit('{{ $option['value'] }}')"
                            class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors
                                {{ $unit === $option['value']
                                    ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500'
                                    : 'border-ink-900/15 dark:border-linen-100/15 hover:bg-ink-900/5 dark:hover:bg-linen-100/5' }}">
                            {{ $option['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
            <p class="text-xs text-ink-900/50 dark:text-linen-100/50">
                Pick the unit the measurements were taken in. Switching units just relabels the fields — it doesn't convert numbers already entered.
            </p>
        </div>

        <div x-data="{ open: true }" class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 overflow-hidden">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 px-5 sm:px-6 py-4 text-left">
                <span class="font-display font-semibold">Upper body</span>
                <svg :class="open ? '' : '-rotate-90'" class="w-4 h-4 text-ink-900/40 dark:text-linen-100/40 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-transition class="px-5 sm:px-6 pb-5 sm:pb-6">
                <div class="grid grid-cols-2 gap-4">
                    <x-forms.input wire:model="chest" name="chest" label="Chest ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="bust" name="bust" label="Bust ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="waist" name="waist" label="Waist ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="shoulder" name="shoulder" label="Shoulder ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="armLength" name="armLength" label="Arm length ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="topLength" name="topLength" label="Top length ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="halfBust" name="halfBust" label="Half bust ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="halfLength" name="halfLength" label="Half length ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="roundSleeve" name="roundSleeve" label="Round sleeve ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="lengthSleeve" name="lengthSleeve" label="Sleeve length ({{ $unit }})" type="number" step="0.1" min="0" />
                </div>
            </div>
        </div>

        <div x-data="{ open: true }" class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 overflow-hidden">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 px-5 sm:px-6 py-4 text-left">
                <span class="font-display font-semibold">Lower body</span>
                <svg :class="open ? '' : '-rotate-90'" class="w-4 h-4 text-ink-900/40 dark:text-linen-100/40 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-transition class="px-5 sm:px-6 pb-5 sm:pb-6">
                <div class="grid grid-cols-2 gap-4">
                    <x-forms.input wire:model="hip" name="hip" label="Hip ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="inseam" name="inseam" label="Inseam ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="thigh" name="thigh" label="Thigh ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="ankle" name="ankle" label="Ankle ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="trouserSkirtLength" name="trouserSkirtLength" label="Trouser / skirt length ({{ $unit }})" type="number" step="0.1" min="0" />
                </div>
            </div>
        </div>

        <div x-data="{ open: true }" class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 overflow-hidden">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 px-5 sm:px-6 py-4 text-left">
                <span class="font-display font-semibold">Overall</span>
                <svg :class="open ? '' : '-rotate-90'" class="w-4 h-4 text-ink-900/40 dark:text-linen-100/40 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-transition class="px-5 sm:px-6 pb-5 sm:pb-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <x-forms.input wire:model="height" name="height" label="Height ({{ $unit }})" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="weight" name="weight" label="Weight (kg)" type="number" step="0.1" min="0" />
                    <x-forms.input wire:model="gownLength" name="gownLength" label="Gown length ({{ $unit }})" type="number" step="0.1" min="0" />
                </div>
                <div class="mt-4">
                    <x-forms.input wire:model="dressSize" name="dressSize" label="Dress size" type="text" placeholder="e.g. M or 12" />
                </div>

                <div class="mt-4 flex flex-col gap-1.5">
                    <label for="notes" class="text-sm font-medium text-ink-800 dark:text-linen-100">Notes</label>
                    <textarea
                        wire:model="notes"
                        id="notes"
                        rows="3"
                        class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"
                    ></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-forms.button type="submit" variant="primary">Save measurements</x-forms.button>
        </div>
    </form>
</div>