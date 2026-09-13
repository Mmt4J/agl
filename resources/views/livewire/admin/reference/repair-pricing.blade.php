<div class="space-y-8">

    {{-- Two small lookup lists, same pattern as Devices & Industries --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold">Device types</h2>
                <x-forms.button type="button" variant="secondary" wire:click="newDeviceType"
                    @click="$dispatch('open-modal', { name: 'device-type-form' })">Add</x-forms.button>
            </div>
            <div class="space-y-2">
                @foreach ($deviceTypes as $type)
                    <div
                        class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-2.5">
                        <p class="flex-1 text-sm">{{ $type->name }}</p>
                        <button type="button" wire:click="editDeviceType({{ $type->id }})"
                            class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                        <button type="button" wire:click="confirmDelete('device-type', {{ $type->id }})"
                            class="text-danger-500" aria-label="Delete {{ $type->name }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold">Issue types</h2>
                <x-forms.button type="button" variant="secondary" wire:click="newIssueType"
                    @click="$dispatch('open-modal', { name: 'issue-type-form' })">Add</x-forms.button>
            </div>
            <div class="space-y-2">
                @foreach ($issueTypes as $type)
                    <div
                        class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-2.5">
                        <p class="flex-1 text-sm">{{ $type->name }}</p>
                        <button type="button" wire:click="editIssueType({{ $type->id }})"
                            class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                        <button type="button" wire:click="confirmDelete('issue-type', {{ $type->id }})"
                            class="text-danger-500" aria-label="Delete {{ $type->name }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pricing matrix - one card per device type. Adding more issue types
         can never break the layout: the cells wrap onto new rows instead of
         widening a table into a horizontal scrollbox, so it reads well on a
         laptop and on a phone alike. Deleting either a device type or an
         issue type above still cascades and removes its cells automatically. --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold">Pricing matrix</h2>
            <p class="text-xs text-ink-900/50 dark:text-linen-100/50 hidden sm:block">Cells wrap as new issues are added — nothing to scroll.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            @forelse ($deviceTypes as $deviceType)
                @php $pricedCount = $matrix->get($deviceType->id)?->count() ?? 0; @endphp
                <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10">
                    <div class="flex items-center justify-between gap-3 border-b border-ink-900/10 dark:border-linen-100/10 px-4 py-3">
                        <h3 class="font-medium text-sm">{{ $deviceType->name }}</h3>
                        <span class="font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40 shrink-0">{{ $pricedCount }} / {{ $issueTypes->count() }} priced</span>
                    </div>
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2 p-4">
                        @foreach ($issueTypes as $issueType)
                            @php $cell = $matrix->get($deviceType->id)?->get($issueType->id); @endphp
                            <button type="button"
                                wire:click="editCell({{ $deviceType->id }}, {{ $issueType->id }})"
                                aria-label="{{ $issueType->name }} for {{ $deviceType->name }}"
                                class="text-left rounded-md border px-3 py-2.5 transition-colors {{ $cell ? 'border-copper-500/40 bg-copper-500/5 hover:bg-copper-500/10' : 'border-ink-900/10 dark:border-linen-100/10 hover:bg-ink-900/5 dark:hover:bg-linen-100/10' }}">
                                <span class="block text-[10px] font-mono uppercase tracking-wider text-ink-900/45 dark:text-linen-100/45 truncate">{{ $issueType->name }}</span>
                                <span class="block text-sm font-medium {{ $cell ? 'text-ink-900 dark:text-linen-100' : 'text-ink-900/25 dark:text-linen-100/25' }}">{{ $cell ? $cell->formatted_range : 'Set price' }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-900/40 dark:text-linen-100/40">No device types yet — add one above.</p>
            @endforelse
        </div>
    </div>

    <x-forms.modal name="device-type-form">
        <form wire:submit="saveDeviceType" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $deviceTypeId ? 'Edit device type' : 'Add device type' }}
            </h2>
            <x-forms.input wire:model="deviceTypeName" name="deviceTypeName" label="Name" type="text" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1"
                    @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="issue-type-form">
        <form wire:submit="saveIssueType" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $issueTypeId ? 'Edit issue type' : 'Add issue type' }}
            </h2>
            <x-forms.input wire:model="issueTypeName" name="issueTypeName" label="Name" type="text" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1"
                    @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    {{-- One cell's price range. "Clear" removes the row entirely, putting
         the cell back to "—" (not a common combination) rather than
         storing a zero/blank price. --}}
    <x-forms.modal name="cell-form">
        <form wire:submit="saveCell" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Price range</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-forms.input wire:model="priceMin" name="priceMin" label="Min (₦)" type="number" required />
                <x-forms.input wire:model="priceMax" name="priceMax" label="Max (₦)" type="number" required />
            </div>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" wire:click="clearCell">Clear</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this entry?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">Any prices using it are removed too. This can't be
                undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1"
                    @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1"
                    wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>
