<div class="max-w-2xl space-y-6">
    <p class="text-sm text-ink-900/60 dark:text-linen-100/60">
        These values feed the website footer, contact page, and WhatsApp links.
    </p>

    <form wire:submit="save" class="space-y-6">
        <x-forms.input wire:model="companyName" name="companyName" label="Company name" type="text" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-forms.input wire:model="rcNumber" name="rcNumber" label="RC number" type="text" required />
            <x-forms.input wire:model="scumlNumber" name="scumlNumber" label="SCUML number" type="text" />
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <x-forms.input wire:model="tin" name="tin" label="TIN" type="text" />
            <x-forms.input wire:model="incorporatedAt" name="incorporatedAt" label="Incorporated on" type="date" />
        </div>

        <x-forms.input wire:model="address" name="address" label="Address" type="text" required />
        <x-forms.input wire:model="email" name="email" label="Company email" type="email" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-forms.input wire:model="phonePrimary" name="phonePrimary" label="Primary phone" type="text" required />
            <x-forms.input wire:model="phoneSecondary" name="phoneSecondary" label="Secondary phone" type="text" />
        </div>

        <x-forms.input wire:model="whatsappNumber" name="whatsappNumber" label="WhatsApp number" type="text" />

        <div class="flex flex-col gap-1.5">
            <label for="whatsappDefaultMessage" class="text-sm font-medium text-ink-800 dark:text-linen-100">Default WhatsApp message</label>
            <textarea
                wire:model="whatsappDefaultMessage"
                id="whatsappDefaultMessage"
                rows="3"
                class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"
            ></textarea>
        </div>

        <div class="flex items-center gap-4">
            <x-forms.button type="submit" variant="primary">Save</x-forms.button>

            @if ($justSaved)
                <p class="text-sm text-sage-600 dark:text-sage-400">Saved.</p>
            @endif
        </div>
    </form>
</div>
