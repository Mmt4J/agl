<div class="max-w-2xl space-y-6">
    <p class="text-sm text-ink-900/60 dark:text-linen-100/60">
        Drives the "Open now / Closed" badge on the website, checked server-side against Africa/Lagos time.
    </p>

    <form wire:submit="save" class="space-y-6">
        <div
            class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 divide-y divide-ink-900/10 dark:divide-linen-100/10">
            @foreach ($dayOrder as $day)
                <div wire:key="hours-{{ $day }}" class="flex flex-wrap items-center gap-3 px-4 sm:px-5 py-3.5">
                    <p class="w-24 shrink-0 text-sm font-medium">{{ $dayLabels[$day] }}</p>

                    @if ($hours[$day]['is_closed'])
                        <span class="font-mono text-xs text-ink-900/40 dark:text-linen-100/40">Closed all day</span>
                    @else
                        <div class="flex items-center gap-2">
                            <input type="time" wire:model="hours.{{ $day }}.opens_at"
                                class="rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-2 py-1.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-copper-500" />
                            <span class="text-ink-900/40 dark:text-linen-100/40">–</span>
                            <input type="time" wire:model="hours.{{ $day }}.closes_at"
                                class="rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-2 py-1.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-copper-500" />
                        </div>
                    @endif

                    <label class="inline-flex items-center gap-1.5 text-[11px] font-mono ml-auto shrink-0">
                        <input type="checkbox" wire:model.live="hours.{{ $day }}.is_closed"
                            class="w-3.5 h-3.5 accent-danger-500" />
                        Closed
                    </label>

                    @error("hours.{$day}.opens_at")
                        <p class="w-full text-xs text-danger-500">{{ $message }}</p>
                    @enderror
                    @error("hours.{$day}.closes_at")
                        <p class="w-full text-xs text-danger-500">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <x-forms.button type="submit" variant="primary">Save</x-forms.button>

            @if ($justSaved)
                <p class="text-sm text-sage-600 dark:text-sage-400">Saved.</p>
            @endif
        </div>
    </form>
</div>
