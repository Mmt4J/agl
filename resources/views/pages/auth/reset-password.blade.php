{{--
    resources/views/pages/auth/reset-password.blade.php
    ------------------------------------------------------------------
    Reached via the signed link in the password-reset email. The
    hidden "token" field is what Fortify checks server-side to prove
    this request actually came from that email link, not just anyone
    who guessed the URL.
--}}
<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">

        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf

            {{-- Not a visible field - request()->route('token') pulls the
                 token straight out of the reset URL itself (e.g.
                 /reset-password/{token}), so it's just carried forward
                 into the POST body, never typed by the user. --}}
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <x-forms.input name="email" label="{{ __('Email') }}" type="email" :value="request('email')" required
                autocomplete="email" />

            <x-forms.password-input name="password" label="{{ __('Password') }}" required autocomplete="new-password"
                placeholder="{{ __('Password') }}" />

            <x-forms.input name="password_confirmation" label="{{ __('Confirm password') }}" type="password" required
                autocomplete="new-password" placeholder="{{ __('Confirm password') }}" viewable />

            <div class="flex items-center justify-end">
                <x-forms.button type="submit" class="w-full" data-test="reset-password-button">
                    {{ __('Reset password') }}
                </x-forms.button>
            </div>
        </form>
    </div>
</x-layouts::auth>
