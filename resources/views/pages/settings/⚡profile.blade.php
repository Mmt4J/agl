{{--
    resources/views/pages/settings/profile.blade.php
    ------------------------------------------------------------------
    Livewire full-page SFC (class-based per our project's admin/settings
    convention) - name/email form with inline "Saved." confirmation.

    Flux::toast() replaced with a plain boolean property ($justSaved)
    instead of a session flash: this component re-renders in place on
    every wire:submit (no page navigation happens), so Livewire's own
    state is simpler and more direct here than session()->flash() would
    be - no need to worry about which request "owns" the flash data.
--}}
<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::account')] #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    // Replaces Flux::toast() - a plain property the view checks to
    // show/hide the inline "Saved." message. Resets to false at the
    // START of every save attempt (see updateProfileInformation()
    // below) so a second save after an error doesn't show a stale
    // success message from a previous attempt.
    public bool $justSaved = false;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $this->justSaved = false;

        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->justSaved = true;
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    // Delete-account section only shows once email verification (if the
    // app requires it at all) is out of the way - an unverified user
    // shouldn't be offered account deletion before confirming the
    // account they're about to delete is really theirs.
    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || Auth::user()->hasVerifiedEmail();
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <h1 class="sr-only">{{ __('Profile settings') }}</h1>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <x-forms.input wire:model="name" name="name" label="{{ __('Name') }}" type="text" required autofocus
                autocomplete="name" />

            <div>
                <x-forms.input wire:model="email" name="email" label="{{ __('Email') }}" type="email" required
                    autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <p class="mt-4 text-sm text-ink-600 dark:text-linen-300">
                            {{ __('Your email address is unverified.') }}
                            <button type="button" wire:click.prevent="resendVerificationNotification"
                                class="text-sm text-copper-600 hover:text-copper-700 dark:text-copper-300 dark:hover:text-copper-200 underline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-sage-600 dark:text-sage-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <x-forms.button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Save') }}
                </x-forms.button>

                {{--
                    wire:loading.class="opacity-0" would hide this
                    instantly on the NEXT click before the new save even
                    resolves - but $justSaved is already reset to false
                    at the top of updateProfileInformation(), so by the
                    time this re-renders mid-save it's already hidden.
                    No extra wire:loading handling needed here.
                --}}
                @if ($justSaved)
                    <p class="text-sm text-sage-600 dark:text-sage-400">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
