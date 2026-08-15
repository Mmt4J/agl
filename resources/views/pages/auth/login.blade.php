{{-- 
    resources/views/pages/auth/login.blade.php
    ------------------------------------------------------------------
    Plain Blade view (not a Livewire component - no PHP class, no
    ⚡ prefix). Fortify's LoginController receives this form's POST
    directly via route('login.store'); this file only renders markup.

    Two things intentionally dropped from the original:
      - <x-passkey-verify /> (the {{-- @chisel-passkeys --}}
{{-- block) - left out for now since passkey/WebAuthn support is a separate
decision we haven't made yet. Backend rate-limiter for it is
still registered in FortifyServiceProvider, untouched, so
re-adding this later is just re-adding this one line. --} {{-- @chisel-registration --}}
{{-- comment markers - those were
instructions to a starter-kit code generator (Chisel), not
something our app needs at runtime. Registration link itself
is kept. --}}

<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">

        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        {{-- Renders nothing unless Fortify flashed a status message
             (e.g. after a successful password reset redirects here). --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <x-forms.input name="email" label="{{ __('Email address') }}" type="email" :value="old('email')" required
                autofocus autocomplete="email" placeholder="email@example.com" />

            {{--
                Password field + "Forgot your password?" link share one
                relative wrapper so the link can sit absolutely
                positioned in the field's top-right corner - same
                layout trick the original Flux version used.
            --}}
            <div class="relative">
                <x-forms.input name="password" label="{{ __('Password') }}" type="password" required
                    autocomplete="current-password" placeholder="{{ __('Password') }}" viewable />

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="absolute top-0 end-0 text-sm text-copper-600 hover:text-copper-700 dark:text-copper-300 dark:hover:text-copper-200">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-forms.checkbox name="remember" label="{{ __('Remember me') }}" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <x-forms.button type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </x-forms.button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-ink-600 dark:text-linen-300">
            <span>{{ __("Don't have an account?") }}</span>
            <a href="{{ route('register') }}" wire:navigate
                class="text-copper-600 hover:text-copper-700 dark:text-copper-300 dark:hover:text-copper-200 font-medium">
                {{ __('Sign up') }}
            </a>
        </div>
    </div>
</x-layouts::auth>
