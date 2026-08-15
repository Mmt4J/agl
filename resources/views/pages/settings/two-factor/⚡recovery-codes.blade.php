{{--
    resources/views/pages/settings/two-factor/recovery-codes.blade.php
    ------------------------------------------------------------------
    Shown under the "Disable 2FA" button in security.blade.php once
    2FA is enabled. Show/hide codes is local Alpine state (no need to
    round-trip to the server just to reveal text already sent down in
    the initial render) - only Regenerate actually calls PHP.
--}}
<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');

                $this->recoveryCodes = [];
            }
        }
    }
}; ?>

<div
    class="py-6 space-y-6 border shadow-sm rounded-xl border-ink-100 dark:border-ink-800"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="px-6 space-y-2">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-ink-700 dark:text-linen-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h3 class="font-display text-base font-semibold text-ink-900 dark:text-linen-50">
                {{ __('2FA recovery codes') }}
            </h3>
        </div>
        <p class="text-sm text-ink-500 dark:text-linen-400">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </p>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            {{--
                Two buttons, toggled by x-show, rather than one button
                with dynamic text - keeps the aria-expanded/aria-controls
                pairing simple and matches the original's structure
                exactly (a screen reader announces state changes more
                reliably this way than swapping text inside one button).
            --}}
            <x-forms.button
                type="button"
                x-show="!showRecoveryCodes"
                variant="primary"
                @click="showRecoveryCodes = true"
                aria-expanded="false"
                aria-controls="recovery-codes-section"
            >
                {{ __('View recovery codes') }}
            </x-forms.button>

            <x-forms.button
                type="button"
                x-show="showRecoveryCodes"
                x-cloak
                variant="primary"
                @click="showRecoveryCodes = false"
                aria-expanded="true"
                aria-controls="recovery-codes-section"
            >
                {{ __('Hide recovery codes') }}
            </x-forms.button>

            @if (filled($recoveryCodes))
                <x-forms.button
                    type="button"
                    x-show="showRecoveryCodes"
                    x-cloak
                    variant="secondary"
                    wire:click="regenerateRecoveryCodes"
                >
                    {{ __('Regenerate codes') }}
                </x-forms.button>
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
            x-bind:aria-hidden="!showRecoveryCodes"
        >
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                    <p class="rounded-md border border-danger-500/40 bg-danger-500/10 px-4 py-3 text-sm text-danger-600 dark:text-danger-400">
                        {{ $message }}
                    </p>
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-linen-100 dark:bg-white/5"
                        role="list"
                        aria-label="{{ __('Recovery codes') }}"
                    >
                        @foreach ($recoveryCodes as $code)
                            <div role="listitem" class="select-text" wire:loading.class="opacity-50 animate-pulse">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-ink-500 dark:text-linen-400">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate codes above.') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>