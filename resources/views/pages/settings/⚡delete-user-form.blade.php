{{--
    resources/views/pages/settings/delete-user-form.blade.php
    ------------------------------------------------------------------
    Small standalone Livewire component - just a heading + a button
    that opens the confirmation modal (delete-user-modal.blade.php
    below, embedded via <livewire:...> so it can hold its own
    password field + validation state, separate from this component).
--}}
<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h2 class="font-display text-lg font-semibold text-danger-600 dark:text-danger-400">
            {{ __('Delete account') }}
        </h2>
        <p class="text-sm text-ink-600 dark:text-linen-300">
            {{ __('Delete your account and all of its resources') }}
        </p>
    </div>

    {{--
        Note this button doesn't need to sit inside any x-data wrapper
        of its own for $dispatch to work - Alpine v3 processes
        directives anywhere in the page, not just inside explicit
        x-data blocks, so a bare @click="$dispatch(...)" here is valid
        on its own.
    --}}
    <x-forms.button type="button" variant="danger" data-test="delete-user-button"
        @click="$dispatch('open-modal', { name: 'confirm-user-deletion' })">
        {{ __('Delete account') }}
    </x-forms.button>

    <livewire:pages::settings.delete-user-modal />
</section>
