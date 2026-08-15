{{--
    resources/views/components/forms/modal.blade.php
    ------------------------------------------------------------------
    Accessible dialog overlay. Direct replacement for <flux:modal>.

    USAGE
        Open it from ANYWHERE - a plain button, a Livewire action, even
        another component entirely - by dispatching a browser event
        with the modal's name:

            <button @click="$dispatch('open-modal', { name: 'confirm-user-deletion' })">
                Delete account
            </button>

            <x-forms.modal name="confirm-user-deletion">
                ...content...
            </x-forms.modal>

        From PHP (a Livewire method), the same event works via
        $this->dispatch('close-modal', name: 'confirm-user-deletion').

        To run PHP logic whenever THIS modal closes, no matter how it
        closed (backdrop click, Escape key, a Cancel button using
        @click="close()"), listen for 'modal-closed' in the Livewire
        component this modal's content belongs to:

            #[On('modal-closed')]
            public function onModalClosed($name): void
            {
                if ($name === 'two-factor-setup-modal') { ... }
            }

    WHY EVENTS INSTEAD OF A PROP:
    Flux's modal used a global name-based registry internally so a
    trigger button anywhere in the page could open a modal declared
    anywhere else, without them needing to share any Livewire state.
    Browser custom events do the same job here: 'open-modal' /
    'close-modal' are dispatched on `window` (see the .window modifier
    below), so any modal instance listening for its own name reacts,
    regardless of where in the DOM the button that fired it lives.
    This means NO wire:model or parent Alpine state is required for
    the basic open/close case - the modal is fully self-contained.

    PROPS
        name             string   required   Matches the {name} passed to open-modal/close-modal
        initially-open   bool     false       Starts open on page load - e.g. pass
                                               :initially-open="$errors->isNotEmpty()" so a modal
                                               whose form just failed validation doesn't silently
                                               close when Livewire re-renders the page with errors.
--}}
@props([
    'name',
    'initiallyOpen' => false,
])

<div
    x-data="{
        open: {{ $initiallyOpen ? 'true' : 'false' }},
        // Every closing path (backdrop, Escape, a Cancel button) should
        // call this SAME method, so 'modal-closed' fires exactly once,
        // consistently, no matter which one the user actually used.
        close() {
            this.open = false;
            $dispatch('modal-closed', { name: '{{ $name }}' });
        },
    }"

    {{-- .window means "listen on the whole window, not just this element" -
         necessary since the button that opens this modal usually lives
         completely outside this component's own DOM subtree. --}}
    x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') close()"
    x-on:keydown.escape.window="if (open) close()"

    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop. Clicking it closes the modal - a click that reaches
         this element at all means it landed OUTSIDE the panel below,
         since the panel calls @click.stop to prevent the click from
         bubbling this far when it originates inside the dialog. --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-ink-950/60"
        @click="close()"
    ></div>

    <div
        x-show="open"
        x-transition
        @click.stop
        class="relative w-full max-w-lg rounded-lg border border-ink-100 dark:border-ink-800
               bg-linen-50 dark:bg-ink-900 p-6 shadow-xl"
    >
        {{ $slot }}
    </div>
</div>