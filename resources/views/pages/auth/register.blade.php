{{--
    resources/views/pages/auth/register.blade.php
    ------------------------------------------------------------------
    Plain Blade view, same pattern as login.blade.php - Fortify's
    RegisterController handles the actual POST via route('register.store').
    This file only renders markup, and now needs zero new components:
    everything here is x-forms.input / x-forms.button, already built.
--}}
<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">

        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <x-forms.input name="name" label="{{ __('Name') }}" type="text" :value="old('name')" required autofocus
                autocomplete="name" placeholder="{{ __('Full name') }}" />

            <x-forms.input name="email" label="{{ __('Email address') }}" type="email" :value="old('email')" required
                autocomplete="email" placeholder="email@example.com" />

            {{--
                x-forms.password-input instead of x-forms.input: same
                field, but with a live pass/fail checklist beneath it
                that mirrors Password::defaults() from
                AppServiceProvider - see that component's own header
                comment for the full explanation of what it does and
                does NOT check client-side.
            --}}
            <x-forms.password-input name="password" label="{{ __('Password') }}" required autocomplete="new-password"
                placeholder="{{ __('Password') }}" />

            <x-forms.input name="password_confirmation" label="{{ __('Confirm password') }}" type="password" required
                autocomplete="new-password" placeholder="{{ __('Confirm password') }}" viewable />

            <div class="flex items-center justify-end">
                <x-forms.button type="submit" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </x-forms.button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-ink-600 dark:text-linen-300">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" wire:navigate
                class="text-copper-600 hover:text-copper-700 dark:text-copper-300 dark:hover:text-copper-200 font-medium">
                {{ __('Log in') }}
            </a>
        </div>
    </div>
</x-layouts::auth>
