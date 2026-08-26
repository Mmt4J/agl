{{--
    resources/views/components/forms/panel.blade.php
    ------------------------------------------------------------------
    Slide-over panel from the right edge - for viewing/acting on one
    record's full detail (a message, a quote request), as opposed to
    x-forms.modal's centered dialog (used for create/edit forms).

    Same open/close event API as x-forms.modal on purpose: dispatch
    'open-modal'/'close-modal' with this panel's name, and a button
    inside it can call close() directly, exactly like a modal. Only
    the visual treatment differs - swap this component in wherever a
    modal would look wrong for "here's one record's full detail".

    USAGE
        <x-forms.panel name="message-detail">...</x-forms.panel>
        <button @click="$dispatch('open-modal', { name: 'message-detail' })">View</button>
--}}
@props([
    'name',
])

<div
    x-data="{
        open: false,
        close() {
            this.open = false;
            $dispatch('modal-closed', { name: '{{ $name }}' });
        },
    }"
    x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') close()"
    x-on:keydown.escape.window="if (open) close()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="open"
        x-transition.opacity
        @click="close()"
        class="absolute inset-0 bg-ink-950/60"
    ></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="translate-x-full"
        class="absolute right-0 inset-y-0 w-full max-w-md bg-linen-50 dark:bg-ink-950 shadow-2xl overflow-y-auto"
    >
        {{ $slot }}
    </div>
</div>
