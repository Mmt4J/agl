{{--
    Listens for a browser event named "toast", dispatched from any
    Livewire component via $this->dispatch('toast', message: '...', type: 'success').
    Rendered once in layouts/admin.blade.php - every admin page gets
    this automatically, no per-page setup needed.

    Three types: 'success' (default, green - saves/creates), 'danger'
    (red - completed deletions, distinct from a routine save without
    being a full interruption), 'error' (red, reserved for genuine
    failures if ever needed - not currently dispatched anywhere).
--}}
{{-- <div
    x-data="{ toasts: [] }"
    x-on:toast.window="
        const id = Date.now();
        toasts.push({ id, message: $event.detail.message, type: $event.detail.type ?? 'success' });
        setTimeout(() => toasts = toasts.filter(t => t.id !== id), 3000);
    "
    class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 w-full max-w-sm pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition
            x-text="toast.message"
            class="rounded-md px-4 py-3 text-sm shadow-lg border pointer-events-auto"
            :class="{
                'bg-sage-500/10 border-sage-500/30 text-sage-700 dark:text-sage-300': toast.type === 'success',
                'bg-danger-500/10 border-danger-500/30 text-danger-600 dark:text-danger-400': toast.type === 'danger' || toast.type === 'error',
            }"
        ></div>
    </template>
</div> --}}









{{-- 
    Listens for a browser event named "toast", dispatched from any
    Livewire component via $this->dispatch('toast', message: '...', type: 'success').

    Types:
    - success (default)
    - danger
    - error
--}}
<div
    x-data="{ toasts: [] }"
    x-on:toast.window="
        const id = Date.now();

        toasts.push({
            id,
            message: $event.detail.message,
            type: $event.detail.type ?? 'success',
            show: true
        });

        setTimeout(() => {
            const toast = toasts.find(t => t.id === id);
            if (toast) toast.show = false;

            setTimeout(() => {
                toasts = toasts.filter(t => t.id !== id);
            }, 300);
        }, 3000);
    "
    class="fixed top-4 right-4 z-[100] max-w-sm w-[calc(100%-2rem)] sm:w-auto"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-cloak
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-end="opacity-0"
            class="w-full"
            role="status"
            aria-live="polite"
        >
            <div
                class="flex items-start gap-3 rounded-md bg-ink-900 dark:bg-linen-100 text-linen-50 dark:text-ink-900 shadow-2xl px-4 py-3 border-l-4 border-copper-500"
            >
                {{-- Icon --}}
                <template x-if="toast.type === 'success'">
                    <svg
                        class="w-5 h-5 shrink-0 mt-0.5 text-copper-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </template>

                <template x-if="toast.type === 'danger' || toast.type === 'error'">
                    <svg
                        class="w-5 h-5 shrink-0 mt-0.5 text-copper-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m0 3h.008M10.29 3.86l-7.5 12.99A1.5 1.5 0 004.09 19h15.82a1.5 1.5 0 001.3-2.15l-7.5-12.99a1.5 1.5 0 00-2.6 0z"
                        />
                    </svg>
                </template>

                <p
                    class="text-sm leading-snug font-mono"
                    x-text="toast.message"
                ></p>

                <button
                    @click="toast.show = false"
                    class="ml-auto opacity-60 hover:opacity-100 transition"
                    aria-label="Dismiss"
                >
                    ✕
                </button>
            </div>
        </div>
    </template>
</div>












{{-- My Alternative that may be useful later in the future --}}
{{-- <div
    x-data="{ toasts: [] }"
    x-on:toast.window="
        const id = Date.now();
        toasts.push({ id, message: $event.detail.message, type: $event.detail.type ?? 'success' });
        setTimeout(() => toasts = toasts.filter(t => t.id !== id), 3000);
    "
    class="fixed inset-0 z-[60] flex items-center justify-center pointer-events-none"
>
    <!-- Background overlay -->
    <div
        x-show="toasts.length"
        x-transition.opacity
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
    ></div>

    <!-- Toast container -->
    <div class="relative flex flex-col items-center gap-3 w-full max-w-sm px-4">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="true"
                x-transition
                x-text="toast.message"
                class="rounded-lg px-6 py-4 text-center text-sm shadow-xl border pointer-events-auto w-full"
                :class="toast.type === 'error'
                    ? 'bg-danger-500/10 border-danger-500/30 text-danger-600 dark:text-danger-400'
                    : 'bg-sage-500/10 border-sage-500/30 text-sage-700 dark:text-sage-300'"
            ></div>
        </template>
    </div>
</div> --}}