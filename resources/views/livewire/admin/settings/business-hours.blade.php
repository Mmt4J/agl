<div class="max-w-2xl space-y-6">
    <p class="text-sm text-ink-900/60 dark:text-linen-100/60">
        Drives the "Open now / Closed" badge on the website, checked server-side against Africa/Lagos time.
    </p>

    <form wire:submit="save" class="space-y-3">
        @foreach ($dayOrder as $day)
            <div class="flex items-center gap-4 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-3">
                <p class="w-24 shrink-0 text-sm font-medium">{{ $dayLabels[$day] }}</p>

                <div class="flex items-center gap-2 flex-1" @if ($hours[$day]['is_closed']) style="opacity:.4" @endif>
                    <input
                        type="time"
                        wire:model="hours.{{ $day }}.opens_at"
                        @disabled($hours[$day]['is_closed'])
                        class="rounded-md border px-2 py-1.5 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"
                    />
                    <span class="text-ink-900/40 dark:text-linen-100/40">–</span>
                    <input
                        type="time"
                        wire:model="hours.{{ $day }}.closes_at"
                        @disabled($hours[$day]['is_closed'])
                        class="rounded-md border px-2 py-1.5 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"
                    />
                </div>

                <label class="flex items-center gap-2 text-sm shrink-0">
                    <input type="checkbox" wire:model.live="hours.{{ $day }}.is_closed" class="rounded accent-copper-500" />
                    Closed
                </label>
            </div>
        @endforeach

        <div class="flex items-center gap-4 pt-2">
            <x-forms.button type="submit" variant="primary">Save</x-forms.button>

            @if ($justSaved)
                <p class="text-sm text-sage-600 dark:text-sage-400">Saved.</p>
            @endif
        </div>
    </form>
</div>
