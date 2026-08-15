{{--
    resources/views/pages/auth/two-factor-challenge.blade.php
    ------------------------------------------------------------------
    Shown mid-login, AFTER a correct email/password, when the account
    has 2FA enabled. Two ways through it: a 6-digit code from an
    authenticator app, or one of the account's one-time recovery codes
    (for when the user's authenticator device is lost/unavailable).
    Both post to the SAME route - Fortify inspects which field
    ("code" vs "recovery_code") was actually filled in.
--}}
<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div class="relative w-full h-auto" x-cloak x-data="{
            // @js() safely converts this PHP boolean into a JS literal.
            // If the LAST submit failed validation on recovery_code
            // specifically, we open straight into that view instead of
            // defaulting back to the OTP boxes - otherwise a user fixing
            // a typo would have to click 'use a recovery code' again.
            showRecoveryInput: @js($errors->has('recovery_code')),
            recovery_code: '',
        
            toggleInput() {
                this.showRecoveryInput = !this.showRecoveryInput;
                this.recovery_code = '';
        
                this.$nextTick(() => {
                    if (this.showRecoveryInput) {
                        this.$refs.recoveryInput?.focus();
                    } else {
                        // The OTP boxes live in a separate component with
                        // their own isolated Alpine scope (see forms/otp.blade.php),
                        // so we can't reach into it directly - we find it by
                        // the data-otp-first marker instead.
                        this.$el.querySelector('[data-otp-first]')?.focus();
                    }
                });
            },
        }">
            <div x-show="!showRecoveryInput">
                <x-auth-header :title="__('Authentication code')" :description="__('Enter the authentication code provided by your authenticator application.')" />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header :title="__('Recovery code')" :description="__(
                    'Please confirm access to your account by entering one of your emergency recovery codes.',
                )" />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput" class="flex items-center justify-center my-5">
                        <x-forms.otp name="code" length="6" />
                    </div>

                    <div x-show="showRecoveryInput" class="my-5">
                        <input type="text" name="recovery_code" x-ref="recoveryInput"
                            x-bind:required="showRecoveryInput" autocomplete="one-time-code" x-model="recovery_code"
                            class="w-full rounded-md border px-3 py-2 text-sm text-center font-mono
                                bg-white dark:bg-ink-900
                                text-ink-950 dark:text-linen-50
                                border-ink-200 dark:border-ink-700
                                focus:outline-none focus:ring-2 focus:ring-copper-400 focus:border-copper-500" />

                        @error('recovery_code')
                            <p class="mt-2 text-sm text-danger-500 dark:text-danger-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <x-forms.button type="submit" class="w-full">
                        {{ __('Continue') }}
                    </x-forms.button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center text-ink-600 dark:text-linen-300">
                    <span class="opacity-70">{{ __('or you can') }}</span>
                    <button type="button" @click="toggleInput()"
                        class="font-medium underline text-copper-600 dark:text-copper-300 hover:text-copper-700 dark:hover:text-copper-200">
                        <span x-show="!showRecoveryInput">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput">{{ __('login using an authentication code') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
