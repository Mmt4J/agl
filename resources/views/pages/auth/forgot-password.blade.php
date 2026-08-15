{{--
    resources/views/pages/auth/forgot-password.blade.php
    ------------------------------------------------------------------
    Plain Blade view. Posts to Fortify's password.email route, which
    fires the "Reset Password" notification email if the address
    exists - Fortify deliberately shows the same session status either
    way, so this page can't be used to check which emails are
    registered.
--}}
<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">

        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <x-forms.input name="email" label="{{ __('Email address') }}" type="email" required autofocus
                placeholder="email@example.com" />

            <x-forms.button type="submit" class="w-full" data-test="email-password-reset-link-button">
                {{ __('Email password reset link') }}
            </x-forms.button>
        </form>

        {{--
            Original used text-zinc-400 unconditionally (no dark:
            variant) - that's a Flux/Tailwind default-palette leftover,
            not an intentional design choice. Swapped for our ink/linen
            pair so this line is actually readable in both themes.
        --}}
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-ink-600 dark:text-linen-300">
            <span>{{ __('Or, return to') }}</span>
            <a href="{{ route('login') }}" wire:navigate
                class="text-copper-600 hover:text-copper-700 dark:text-copper-300 dark:hover:text-copper-200 font-medium">
                {{ __('log in') }}
            </a>
        </div>
    </div>
</x-layouts::auth>
