{{--
    resources/views/pages/auth/verify-email.blade.php
    ------------------------------------------------------------------
    Shown to a logged-in user whose email isn't verified yet - Fortify's
    'verified' middleware redirects here automatically whenever such a
    user tries to reach a route that requires verification. Two actions:
    resend the verification email, or log out.
--}}
<x-layouts::auth :title="__('Email verification')">
    <div class="mt-4 flex flex-col gap-6">
        <p class="text-center text-sm text-ink-700 dark:text-linen-200">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="text-center text-sm font-medium text-sage-600 dark:text-sage-400">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </p>
        @endif

        <div class="flex flex-col items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <x-forms.button type="submit" class="w-full">
                    {{ __('Resend verification email') }}
                </x-forms.button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-forms.button variant="ghost" type="submit" class="text-sm" data-test="logout-button">
                    {{ __('Log out') }}
                </x-forms.button>
            </form>
        </div>
    </div>
</x-layouts::auth>
