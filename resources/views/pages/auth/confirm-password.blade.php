{{--
    resources/views/pages/auth/confirm-password.blade.php
    ------------------------------------------------------------------
    Shown when a logged-in user tries to reach a "sensitive" action
    (e.g. deleting their account, viewing recovery codes) and Fortify
    wants to re-verify it's really them before proceeding - a
    re-auth checkpoint, not a login page.

    Same as login.blade.php: <x-passkey-verify /> is left out for now
    (we're deferring passkeys until after the core auth flow is done).
    This route ('password.confirm.store') and the passkey
    confirm-options/confirm routes already exist untouched in Fortify -
    re-adding the component later is a one-line change here.
--}}
<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">

        <x-auth-header :title="__('Confirm password')" :description="__('This is a secure area of the application. Please confirm your password before continuing.')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <x-forms.input name="password" label="{{ __('Password') }}" type="password" required
                autocomplete="current-password" placeholder="{{ __('Password') }}" viewable />

            <x-forms.button type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('Confirm') }}
            </x-forms.button>
        </form>
    </div>
</x-layouts::auth>
