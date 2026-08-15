{{--
    resources/views/pages/settings/delete-user-modal.blade.php
    ------------------------------------------------------------------
    The actual confirmation dialog - password re-entry, then permanent
    deletion. PHP logic is completely unchanged from the original;
    only the markup below @endphp was rewritten.
--}}
<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

{{--
    :initially-open="$errors->isNotEmpty()" - if deleteUser() above
    fails validation (wrong password), Livewire re-renders this
    component with $errors populated. Without this, the modal would
    have no way to know it should stay open, and the error message
    would be rendered invisibly behind a closed dialog.
--}}
<x-forms.modal name="confirm-user-deletion" :initially-open="$errors->isNotEmpty()">
    <form wire:submit="deleteUser" class="space-y-6">
        <div>
            <h2 class="font-display text-lg font-semibold text-ink-950 dark:text-linen-50">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>
            <p class="mt-1 text-sm text-ink-600 dark:text-linen-300">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>
        </div>

        <x-forms.input wire:model="password" name="password" label="{{ __('Password') }}" type="password" viewable />

        <div class="flex justify-end gap-2">
            {{--
                Unlike the trigger button in delete-user-form.blade.php,
                this Cancel button DOESN'T need $dispatch - it's rendered
                inside {{ $slot }} in modal.blade.php, which is inside
                that component's own x-data="{ open: ... }" block. So
                `open` here refers directly to that same variable via
                normal Alpine scope inheritance - no event round-trip
                needed to close a modal from a button already inside it.
            --}}
            <x-forms.button type="button" variant="secondary" @click="close()">
                {{ __('Cancel') }}
            </x-forms.button>

            <x-forms.button type="submit" variant="danger" data-test="confirm-delete-user-button">
                {{ __('Delete account') }}
            </x-forms.button>
        </div>
    </form>
</x-forms.modal>