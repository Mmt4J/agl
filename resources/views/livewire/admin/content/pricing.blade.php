<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-ink-900/60 dark:text-linen-100/60">Categories become tabs on the Pricing page; plans list under each.</p>
        <x-forms.button type="button" variant="secondary" wire:click="newCategory" @click="$dispatch('open-modal', { name: 'category-form' })">
            Add category
        </x-forms.button>
    </div>

    @foreach ($categories as $category)
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-display font-semibold">{{ $category->name }}</h2>
                    <button type="button" wire:click="editCategory({{ $category->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                    <button type="button" wire:click="confirmDelete('category', {{ $category->id }})" class="text-danger-500" aria-label="Delete {{ $category->name }} category">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <x-forms.button type="button" variant="secondary" wire:click="newPlan({{ $category->id }})" @click="$dispatch('open-modal', { name: 'plan-form' })">
                    + Add plan
                </x-forms.button>
            </div>

            <div class="space-y-2">
                @forelse ($plans->get($category->id, collect()) as $plan)
                    <div class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-sm">{{ $plan->name }}</p>
                            <p class="text-xs text-ink-900/50 dark:text-linen-100/50">{{ $plan->price_label }} · {{ $plan->period_label }}</p>
                        </div>
                        @if ($plan->is_highlighted)
                            <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300 shrink-0">highlighted</span>
                        @endif
                        <button type="button" wire:click="editPlan({{ $plan->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit</button>
                        <button type="button" wire:click="confirmDelete('plan', {{ $plan->id }})" class="text-danger-500 shrink-0" aria-label="Delete {{ $plan->name }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-ink-900/40 dark:text-linen-100/40">No plans in this category yet.</p>
                @endforelse
            </div>
        </div>
    @endforeach

    <x-forms.modal name="category-form">
        <form wire:submit="saveCategory" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $categoryId ? 'Edit category' : 'Add category' }}</h2>
            <x-forms.input wire:model="categoryName" name="categoryName" label="Name" type="text" required />
            <x-forms.input wire:model="categorySortOrder" name="categorySortOrder" label="Sort order" type="number" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="plan-form">
        <form wire:submit="savePlan" class="space-y-6 max-h-[75vh] overflow-y-auto pr-1">
            <h2 class="font-display text-lg font-semibold">{{ $planId ? 'Edit plan' : 'Add plan' }}</h2>

            <div class="flex flex-col gap-1.5">
                <label for="planCategoryId" class="text-sm font-medium text-ink-800 dark:text-linen-100">Category</label>
                <select wire:model="planCategoryId" id="planCategoryId" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <x-forms.input wire:model="planName" name="planName" label="Name" type="text" required />
            <x-forms.input wire:model="tagline" name="tagline" label="Tagline" type="text" required />

            <div class="grid grid-cols-2 gap-4">
                <x-forms.input wire:model="priceLabel" name="priceLabel" label="Price label" type="text" placeholder="₦180,000" required />
                <x-forms.input wire:model="periodLabel" name="periodLabel" label="Period label" type="text" placeholder="one-time" required />
            </div>

            <x-forms.checkbox wire:model="isHighlighted" name="isHighlighted" label="Highlighted (\"Most requested\" ribbon)" />
            <x-forms.input wire:model="planSortOrder" name="planSortOrder" label="Sort order" type="number" required />

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-ink-800 dark:text-linen-100">Features</label>
                    <button type="button" wire:click="addFeatureRow" class="text-xs text-copper-600 dark:text-copper-300">+ Add feature</button>
                </div>

                @foreach ($features as $index => $row)
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model="features.{{ $index }}.feature" class="flex-1 rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400" />
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
            <h2 class="font-display text-lg font-semibold">Delete this?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">Deleting a category removes its plans too. This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>