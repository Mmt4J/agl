{{--
    resources/views/pages/settings/security.blade.php
    ------------------------------------------------------------------
    Password change + two-factor authentication management.

    The passkeys section (list/add/remove passkeys, plus its own
    delete-confirmation modal at the bottom of the original file) is
    REMOVED here, same decision as login.blade.php and
    confirm-password.blade.php earlier - passkeys are deferred, not
    abandoned. The original file conveniently wrapped that whole
    section in /* @chisel-passkeys */ ... /* @end-chisel-passkeys */
    markers, which made it easy to identify and cut cleanly: every
    passkey-related property, the loadPasskeys()/confirmDelete()/
    deletePasskey()/closeDeleteModal() methods, and the bottom
    <flux:modal name="delete-passkey-modal"> block are all gone below.
    Re-adding this later means restoring that section and rebuilding
    ITS markup the same way we've done everything else - nothing about
    removing it now makes that harder later.
--}}
<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::account')] #[Title('Security settings')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Replaces Flux::toast() - same inline-confirmation pattern as
    // profile.blade.php's $justSaved. A dedicated property (not just
    // checking if $password is non-empty) because updatePassword()
    // below clears all three password fields immediately after
    // saving, so there's nothing left in state to infer success from.
    public bool $passwordUpdated = false;

    public bool $canManageTwoFactor;
    public bool $twoFactorEnabled;
    public bool $requiresConfirmation;

    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $this->canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        if ($this->canManageTwoFactor) {
            if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
                $disableTwoFactorAuthentication(auth()->user());
            }

            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
            $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }
    }

    public function updatePassword(): void
    {
        $this->passwordUpdated = false;

        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->passwordUpdated = true;
    }

    #[On('two-factor-enabled')]
    public function onTwoFactorEnabled(): void
    {
        $this->twoFactorEnabled = true;
    }

    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <h1 class="sr-only">{{ __('Security settings') }}</h1>

    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <x-forms.input wire:model="current_password" name="current_password" label="{{ __('Current password') }}"
                type="password" required autocomplete="current-password" viewable />

            {{-- Live checklist reused exactly as built for register.blade.php -
                 this is the payoff of that component existing already: zero
                 new code needed here, just drop it in with a different name. --}}
            <x-forms.password-input wire:model="password" name="password" label="{{ __('New password') }}" required
                autocomplete="new-password" />

            <x-forms.input wire:model="password_confirmation" name="password_confirmation"
                label="{{ __('Confirm password') }}" type="password" required autocomplete="new-password" viewable />

            <div class="flex items-center gap-4">
                <x-forms.button variant="primary" type="submit" data-test="update-password-button">
                    {{ __('Save') }}
                </x-forms.button>

                @if ($passwordUpdated)
                    <p class="text-sm text-sage-600 dark:text-sage-400">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>

        @if ($canManageTwoFactor)
            <section class="mt-12">
                <h2 class="font-display text-lg font-semibold text-ink-900 dark:text-linen-50">
                    {{ __('Two-factor authentication') }}
                </h2>
                <p class="text-sm text-ink-600 dark:text-linen-300">
                    {{ __('Manage your two-factor authentication settings') }}
                </p>

                <div class="flex flex-col w-full mx-auto space-y-6 text-sm mt-4" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="space-y-4">
                            <p class="text-ink-700 dark:text-linen-200">
                                {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                            </p>

                            <div class="flex justify-start">
                                <x-forms.button variant="danger" wire:click="disable">
                                    {{ __('Disable 2FA') }}
                                </x-forms.button>
                            </div>

                            <livewire:pages::settings.two-factor.recovery-codes :$requiresConfirmation />
                        </div>
                    @else
                        <div class="space-y-4">
                            <p class="text-ink-500 dark:text-linen-400">
                                {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                            </p>

                            {{--
                                Two events fire on click: 'start-two-factor-setup'
                                is a Livewire event the setup-modal component
                                listens for (to generate a fresh QR code/secret
                                each time it opens, not just once on page load),
                                and 'open-modal' is our own modal component's
                                open trigger - same $dispatch pattern as
                                delete-user-form.blade.php earlier.
                            --}}
                            <x-forms.button variant="primary" wire:click="$dispatch('start-two-factor-setup')"
                                @click="$dispatch('open-modal', { name: 'two-factor-setup-modal' })">
                                {{ __('Enable 2FA') }}
                            </x-forms.button>

                            <livewire:pages::settings.two-factor-setup-modal :requires-confirmation="$requiresConfirmation" />
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </x-pages::settings.layout>
</section>
