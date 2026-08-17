<div class="grid lg:grid-cols-2 gap-6">

    {{-- Devices --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold">Devices we service</h2>
            <x-forms.button type="button" variant="secondary" wire:click="newDevice" @click="$dispatch('open-modal', { name: 'device-form' })">
                Add device
            </x-forms.button>
        </div>

        <div class="space-y-2">
            @forelse ($devices as $device)
                <div class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm">{{ $device->name }}</p>
                        <p class="text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $device->examples }}</p>
                    </div>
                    <button type="button" wire:click="editDevice({{ $device->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                    <button type="button" wire:click="confirmDelete('device', {{ $device->id }})" @click="$dispatch('open-modal', { name: 'confirm-delete' })" class="text-danger-500" aria-label="Delete {{ $device->name }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @empty
                <p class="text-sm text-ink-900/40 dark:text-linen-100/40">No devices yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Industries --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold">Industries we serve</h2>
            <x-forms.button type="button" variant="secondary" wire:click="newIndustry" @click="$dispatch('open-modal', { name: 'industry-form' })">
                Add industry
            </x-forms.button>
        </div>

        <div class="space-y-2">
            @forelse ($industries as $industry)
                <div class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm">{{ $industry->name }}</p>
                        <p class="text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $industry->description }}</p>
                    </div>
                    <button type="button" wire:click="editIndustry({{ $industry->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                    <button type="button" wire:click="confirmDelete('industry', {{ $industry->id }})" @click="$dispatch('open-modal', { name: 'confirm-delete' })" class="text-danger-500" aria-label="Delete {{ $industry->name }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @empty
                <p class="text-sm text-ink-900/40 dark:text-linen-100/40">No industries yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Device create/edit modal --}}
    <x-forms.modal name="device-form">
        <form wire:submit="saveDevice" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $deviceId ? 'Edit device' : 'Add device' }}</h2>
            <x-forms.input wire:model="deviceName" name="deviceName" label="Name" type="text" required />
            <x-forms.input wire:model="deviceExamples" name="deviceExamples" label="Examples (short description)" type="text" required />
            <x-forms.input wire:model="deviceIcon" name="deviceIcon" label="Icon name" type="text" required />
            <x-forms.input wire:model="deviceSortOrder" name="deviceSortOrder" label="Sort order" type="number" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    {{-- Industry create/edit modal --}}
    <x-forms.modal name="industry-form">
        <form wire:submit="saveIndustry" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $industryId ? 'Edit industry' : 'Add industry' }}</h2>
            <x-forms.input wire:model="industryName" name="industryName" label="Name" type="text" required />
            <x-forms.input wire:model="industryDescription" name="industryDescription" label="Description" type="text" required />
            <x-forms.input wire:model="industrySortOrder" name="industrySortOrder" label="Sort order" type="number" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    {{-- Shared delete confirmation --}}
    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this entry?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>