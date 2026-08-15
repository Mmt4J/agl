{{--
    resources/views/pages/settings/two-factor-setup-modal.blade.php
    ------------------------------------------------------------------
    The QR-code wizard: scan/enter key -> verify 6-digit code -> done.
    PHP logic is completely unchanged from the original except for
    dropping @close="closeModal" in favor of the #[On('modal-closed')]
    listener at the bottom, which now handles ANY closing path (Escape,
    backdrop click, or an explicit button) consistently - see
    modal.blade.php's own header comment for why that changed.
--}}
<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showVerificationStep = false;

    public bool $setupComplete = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    public function mount(bool $requiresConfirmation): void
    {
        $this->requiresConfirmation = $requiresConfirmation;
    }

    #[On('start-two-factor-setup')]
    public function startTwoFactorSetup(): void
    {
        $enableTwoFactorAuthentication = app(EnableTwoFactorAuthentication::class);
        $enableTwoFactorAuthentication(auth()->user());

        $this->loadSetupData();
    }

    private function loadSetupData(): void
    {
        $user = auth()->user()?->fresh();

        try {
            if (! $user || ! $user->two_factor_secret) {
                throw new Exception('Two-factor setup secret is not available.');
            }

            $this->qrCodeSvg = $user->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
        $this->dispatch('two-factor-enabled');
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->setupComplete = true;

        $this->closeModal();

        $this->dispatch('two-factor-enabled');
    }

    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    public function closeModal(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'showVerificationStep',
            'setupComplete',
        );

        $this->resetErrorBag();
    }

    // Replaces @close="closeModal" - fires no matter which of the
    // modal's closing paths (Escape, backdrop, a button calling
    // close()) was actually used. $name arrives automatically:
    // Livewire matches dispatched event payload keys to this method's
    // parameter names, so { name: '...' } in the JS dispatch becomes
    // the $name argument here with no extra wiring.
    #[On('modal-closed')]
    public function onModalClosed($name): void
    {
        if ($name === 'two-factor-setup-modal') {
            $this->closeModal();
        }
    }

    #[Computed]
    public function modalConfig(): array
    {
        if ($this->setupComplete) {
            return [
                'title' => __('Two-factor authentication enabled'),
                'description' => __('Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.'),
                'buttonText' => __('Close'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify authentication code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Continue'),
            ];
        }

        return [
            'title' => __('Enable two-factor authentication'),
            'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
}; ?>

<x-forms.modal name="two-factor-setup-modal">
    <div class="space-y-6">
        <div class="flex flex-col items-center space-y-4">
            {{-- Simplified from the original's layered grid-line decoration -
                 that was mostly ornamental. A plain copper-ringed badge with
                 a QR-style icon communicates the same thing with far less markup. --}}
            <div class="w-16 h-16 rounded-full border-2 border-copper-400 bg-linen-100 dark:bg-ink-800 flex items-center justify-center">
                <svg class="w-7 h-7 text-copper-600 dark:text-copper-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A.75.75 0 014.5 3.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75v-4.5zM3.75 15a.75.75 0 01.75-.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5A.75.75 0 013.75 19.5V15zM14.25 4.5a.75.75 0 01.75-.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75v-4.5zM14.25 14.25h2.25v2.25h-2.25v-2.25zM17.25 17.25h2.25v2.25h-2.25v-2.25zM14.25 19.5h2.25v2.25h-2.25V19.5zM19.5 14.25h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75z" />
                </svg>
            </div>

            <div class="space-y-2 text-center">
                <h2 class="font-display text-lg font-semibold text-ink-950 dark:text-linen-50">{{ $this->modalConfig['title'] }}</h2>
                <p class="text-sm text-ink-600 dark:text-linen-300">{{ $this->modalConfig['description'] }}</p>
            </div>
        </div>

        @if ($showVerificationStep)
            <div class="space-y-6">
                <div class="flex flex-col items-center space-y-3 justify-center">
                    <x-forms.otp name="code" wire:model="code" length="6" />
                </div>

                <div class="flex items-center gap-3">
                    <x-forms.button variant="secondary" class="flex-1" wire:click="resetVerification">
                        {{ __('Back') }}
                    </x-forms.button>

                    <x-forms.button
                        variant="primary"
                        class="flex-1"
                        wire:click="confirmTwoFactor"
                        x-bind:disabled="$wire.code.length < 6"
                    >
                        {{ __('Confirm') }}
                    </x-forms.button>
                </div>
            </div>
        @else
            @error('setupData')
                <p class="rounded-md border border-danger-500/40 bg-danger-500/10 px-4 py-3 text-sm text-danger-600 dark:text-danger-400">
                    {{ $message }}
                </p>
            @enderror

            <div class="flex justify-center">
                <div class="relative w-64 overflow-hidden border rounded-lg border-ink-200 dark:border-ink-700 aspect-square">
                    @empty($qrCodeSvg)
                        <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-ink-800">
                            {{-- Small inline spinner - one CSS animation, no icon
                                 library dependency, same approach as everything else. --}}
                            <svg class="w-6 h-6 animate-spin text-ink-400 dark:text-linen-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full p-4">
                            {{-- dark:[filter:...] replaces the original's Alpine
                                 check against $flux.appearance - the QR SVG is
                                 drawn in black, so it needs inverting to stay
                                 legible against a dark background. Tailwind's
                                 arbitrary-property syntax does this with plain
                                 CSS, no JS check needed at all. --}}
                            <div class="bg-white p-3 rounded dark:[filter:invert(1)_brightness(1.5)]">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    @endempty
                </div>
            </div>

            <div>
                <x-forms.button
                    :disabled="$errors->has('setupData')"
                    variant="primary"
                    class="w-full"
                    wire:click="showVerificationIfNecessary"
                >
                    {{ $this->modalConfig['buttonText'] }}
                </x-forms.button>
            </div>

            <div class="space-y-4">
                <div class="relative flex items-center justify-center w-full">
                    <div class="absolute inset-0 w-full h-px top-1/2 bg-ink-200 dark:bg-ink-700"></div>
                    <span class="relative px-2 text-sm bg-linen-50 dark:bg-ink-900 text-ink-500 dark:text-linen-400">
                        {{ __('or, enter the code manually') }}
                    </span>
                </div>

                <div
                    x-data="{
                        copied: false,
                        async copy() {
                            try {
                                await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                this.copied = true;
                                setTimeout(() => this.copied = false, 1500);
                            } catch (e) {
                                console.warn('Could not copy to clipboard');
                            }
                        },
                    }"
                    class="flex items-center gap-2"
                >
                    <div class="flex items-stretch w-full border rounded-lg border-ink-200 dark:border-ink-700">
                        @empty($manualSetupKey)
                            <div class="flex items-center justify-center w-full p-3 bg-linen-100 dark:bg-ink-800">
                                <svg class="w-4 h-4 animate-spin text-ink-400 dark:text-linen-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        @else
                            <input
                                type="text"
                                readonly
                                value="{{ $manualSetupKey }}"
                                class="w-full p-3 bg-transparent outline-none text-ink-900 dark:text-linen-100 font-mono text-sm"
                            />

                            <button
                                @click="copy()"
                                type="button"
                                class="px-3 transition-colors border-l border-ink-200 dark:border-ink-700 cursor-pointer"
                            >
                                <svg x-show="!copied" class="w-4 h-4 text-ink-600 dark:text-linen-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                </svg>
                                <svg x-show="copied" x-cloak class="w-4 h-4 text-sage-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                        @endempty
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-forms.modal>