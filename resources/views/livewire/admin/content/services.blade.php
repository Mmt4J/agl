<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-ink-900/60 dark:text-linen-100/60">Services shown on the website, in this order.</p>
        <x-forms.button type="button" variant="primary" wire:click="newService" @click="$dispatch('open-modal', { name: 'service-form' })">
            Add service
        </x-forms.button>
    </div>

    <div class="space-y-2">
        @foreach ($services as $service)
            <div wire:key="service-{{ $service->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-4">
                <span class="font-mono text-xs text-copper-600 dark:text-copper-300 shrink-0 w-10">{{ $service->code }}</span>

                <div class="min-w-0 flex-1">
                    <p class="font-medium text-sm">{{ $service->name }}</p>
                    <p class="text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $service->short_description }} · {{ $service->features_count }} feature{{ $service->features_count === 1 ? '' : 's' }}</p>
                </div>

                @if ($service->is_featured)
                    <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300 shrink-0">featured</span>
                @endif

                <button type="button" wire:click="editService({{ $service->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit</button>
                <button type="button" wire:click="confirmDelete({{ $service->id }})" class="text-danger-500 shrink-0" aria-label="Delete {{ $service->name }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    <x-forms.modal name="service-form">
        <form wire:submit="save" class="space-y-6 max-h-[75vh] overflow-y-auto pr-1">
            <h2 class="font-display text-lg font-semibold">{{ $serviceId ? 'Edit service' : 'Add service' }}</h2>

            <div class="grid grid-cols-3 gap-4">
                <x-forms.input wire:model="code" name="code" label="Code" type="text" placeholder="A.1" required />
                <x-forms.input wire:model="serviceName" name="serviceName" label="Name" type="text" required class="col-span-2" />
            </div>

            <x-forms.input wire:model="slug" name="slug" label="Slug (leave blank to auto-generate)" type="text" />
            <x-forms.input wire:model="shortDescription" name="shortDescription" label="Short description (Home preview card)" type="text" required />
            <x-forms.input wire:model="blurb" name="blurb" label="Blurb (hero panel)" type="text" required />

            <div class="flex flex-col gap-1.5">
                <label for="description" class="text-sm font-medium text-ink-800 dark:text-linen-100">Full description (Services page)</label>
                <textarea wire:model="description" id="description" rows="4" class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4 items-end">
                <x-forms.input wire:model="icon" name="icon" label="Icon name" type="text" required />
                <x-forms.input wire:model="sortOrder" name="sortOrder" label="Sort order" type="number" required />
                <x-forms.checkbox wire:model="isFeatured" name="isFeatured" label="Featured" />
            </div>

            {{-- Repeater: each row is one bullet-point feature. Position in
                 this list becomes its sort_order when saved - no separate
                 order field to manage. --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-ink-800 dark:text-linen-100">Features</label>
                    <button type="button" wire:click="addFeatureRow" class="text-xs text-copper-600 dark:text-copper-300">+ Add feature</button>
                </div>

                @foreach ($features as $index => $row)
                    <div wire:key="feature-{{ $index }}" class="flex items-center gap-2">
                        <input
                            type="text"
                            wire:model="features.{{ $index }}.feature"
                            class="flex-1 rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"
                        />
                        <button type="button" wire:click="removeFeatureRow({{ $index }})" class="text-danger-500 shrink-0" aria-label="Remove this feature">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @error("features.{$index}.feature") <p class="text-xs text-danger-500">{{ $message }}</p> @enderror
                @endforeach

                @if (empty($features))
                    <p class="text-xs text-ink-900/40 dark:text-linen-100/40">No features yet.</p>
                @endif
            </div>

            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this service?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">Existing quote requests that reference it are kept, just unlinked. This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>