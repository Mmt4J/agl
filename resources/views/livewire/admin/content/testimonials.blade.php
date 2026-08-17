<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-ink-900/60 dark:text-linen-100/60">Only approved testimonials show on the website.</p>
        <x-forms.button type="button" variant="primary" wire:click="newTestimonial" @click="$dispatch('open-modal', { name: 'testimonial-form' })">
            Add testimonial
        </x-forms.button>
    </div>

    <div class="space-y-2">
        @foreach ($testimonials as $testimonial)
            <div class="flex items-start gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-4">
                <button
                    type="button"
                    wire:click="toggleApproved({{ $testimonial->id }})"
                    class="mt-0.5 shrink-0 font-mono text-[10px] px-2 py-1 rounded-full {{ $testimonial->is_approved ? 'bg-sage-500/15 text-sage-600 dark:text-sage-400' : 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/40 dark:text-linen-100/40' }}"
                >
                    {{ $testimonial->is_approved ? 'approved' : 'pending' }}
                </button>

                <div class="min-w-0 flex-1">
                    <p class="font-medium text-sm">{{ $testimonial->client_name }} <span class="font-normal text-ink-900/50 dark:text-linen-100/50">— {{ $testimonial->client_role }}</span></p>
                    <p class="text-xs text-ink-900/50 dark:text-linen-100/50 mt-0.5 line-clamp-2">{{ $testimonial->quote }}</p>
                </div>

                <span class="font-mono text-xs text-copper-600 dark:text-copper-300 shrink-0">{{ str_repeat('★', $testimonial->rating) }}{{ str_repeat('☆', 5 - $testimonial->rating) }}</span>

                <button type="button" wire:click="editTestimonial({{ $testimonial->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit</button>
                <button type="button" wire:click="confirmDelete({{ $testimonial->id }})" @click="$dispatch('open-modal', { name: 'confirm-delete' })" class="text-danger-500 shrink-0" aria-label="Delete this testimonial">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    <x-forms.modal name="testimonial-form">
        <form wire:submit="save" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $testimonialId ? 'Edit testimonial' : 'Add testimonial' }}</h2>

            <div class="grid grid-cols-2 gap-4">
                <x-forms.input wire:model="clientName" name="clientName" label="Client name" type="text" required />
                <x-forms.input wire:model="clientRole" name="clientRole" label="Role / company" type="text" required />
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="quote" class="text-sm font-medium text-ink-800 dark:text-linen-100">Quote</label>
                <textarea wire:model="quote" id="quote" rows="3" class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4 items-end">
                <div class="flex flex-col gap-1.5">
                    <label for="rating" class="text-sm font-medium text-ink-800 dark:text-linen-100">Rating</label>
                    <select wire:model="rating" id="rating" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                        @foreach ([1,2,3,4,5] as $n)
                            <option value="{{ $n }}">{{ $n }} star{{ $n > 1 ? 's' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <x-forms.input wire:model="sortOrder" name="sortOrder" label="Sort order" type="number" required />
                <x-forms.checkbox wire:model="isApproved" name="isApproved" label="Approved" />
            </div>

            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this testimonial?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>