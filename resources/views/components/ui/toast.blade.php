<div
    x-data="{
        toast: {
            show: false,
            message: ''
        },

        showToast(message) {
            this.toast.message = message;
            this.toast.show = true;

            clearTimeout(this._toastTimer);

            this._toastTimer = setTimeout(() => {
                this.toast.show = false;
            }, 2500);
        }
    }"
    @toast.window="showToast($event.detail.message)"
>
    <div
        x-cloak
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0"
        class="fixed top-4 right-4 z-[90] max-w-sm w-[calc(100%-2rem)] sm:w-auto"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3 rounded-md bg-ink-900 dark:bg-linen-100 text-linen-50 dark:text-ink-900 shadow-2xl px-4 py-3 border-l-4 border-copper-500">

            <svg
                class="w-5 h-5 shrink-0 mt-0.5 text-copper-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75l2 2 4-4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>

            <p
                class="text-sm leading-snug font-mono"
                x-text="toast.message"
            ></p>

            <button
                type="button"
                @click="toast.show = false"
                class="ml-auto opacity-60 hover:opacity-100"
                aria-label="Dismiss"
            >
                ✕
            </button>

        </div>
    </div>
</div>