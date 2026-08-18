<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-ink-900/60 dark:text-linen-100/60">Shown on the website's FAQ section, active ones only, in this order.</p>
        <x-forms.button type="button" variant="primary" wire:click="newFaq" @click="$dispatch('open-modal', { name: 'faq-form' })">
            Add FAQ
        </x-forms.button>
    </div>

    <div class="space-y-2">
        @foreach ($faqs as $faq)
            <div class="flex items-start gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-4">
                <button
                    type="button"
                    wire:click="toggleActive({{ $faq->id }})"
                    class="mt-0.5 shrink-0 font-mono text-[10px] px-2 py-1 rounded-full {{ $faq->is_active ? 'bg-sage-500/15 text-sage-600 dark:text-sage-400' : 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/40 dark:text-linen-100/40' }}"
                >
                    {{ $faq->is_active ? 'active' : 'hidden' }}
                </button>

                <div class="min-w-0 flex-1">
                    <p class="font-medium text-sm">{{ $faq->question }}</p>
                    <p class="text-xs text-ink-900/50 dark:text-linen-100/50 mt-0.5 line-clamp-2">{{ $faq->answer }}</p>
                </div>

                <button type="button" wire:click="editFaq({{ $faq->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit</button>
                <button type="button" wire:click="confirmDelete({{ $faq->id }})" class="text-danger-500 shrink-0" aria-label="Delete this FAQ">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    <x-forms.modal name="faq-form">
        <form wire:submit="save" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $faqId ? 'Edit FAQ' : 'Add FAQ' }}</h2>
            <x-forms.input wire:model="question" name="question" label="Question" type="text" required />

            <div class="flex flex-col gap-1.5">
                <label for="answer" class="text-sm font-medium text-ink-800 dark:text-linen-100">Answer</label>
                <textarea wire:model="answer" id="answer" rows="4" class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 items-end">
                <x-forms.input wire:model="sortOrder" name="sortOrder" label="Sort order" type="number" required />
                <x-forms.checkbox wire:model="isActive" name="isActive" label="Active" />
            </div>

            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this FAQ?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>