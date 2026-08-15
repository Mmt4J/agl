{{--
    resources/views/components/forms/otp.blade.php
    ------------------------------------------------------------------
    A row of single-digit boxes that behave like one field: typing
    auto-advances to the next box, backspace on an empty box jumps
    back, and pasting a full code splits it across all boxes at once.
    Direct replacement for <flux:otp>.

    USAGE
        <x-forms.otp name="code" length="6" />
        <x-forms.otp name="code" length="6" wire:model="code" />

    PROPS
        name      string   required   The field name the server receives - a
                                       single concatenated string, e.g. "482913"
        length    int      6          How many digit boxes to render

    WIRE:MODEL NOTE: Alpine's x-bind:value on the hidden input (below)
    sets the DOM value programmatically, which does NOT fire a native
    "input" event on its own - and Livewire's wire:model only detects
    changes through real "input"/"change" events. So every method that
    changes `digits` also manually fires one on the hidden field via
    $refs.hidden.dispatchEvent(new Event('input')), which is enough to
    make wire:model (and $wire.code in Alpine expressions elsewhere on
    the page) stay in sync, exactly as if a user had typed into it directly.

    IMPORTANT DESIGN NOTE - read this before touching the JS below:
    The 6 visible boxes are PURELY visual. None of them has a `name`
    attribute, so none of them is submitted with the form on its own.
    Instead there's one <input type="hidden"> at the bottom carrying
    the real name="{{ $name }}", kept in sync with what's typed. This
    is the same trick every "OTP input" component uses under the hood
    (Flux's included) - it's what lets 6 separate DOM elements act
    like one form field from the server's point of view.
--}}
@props([
    'name',
    'length' => 6,
])

<div
    x-data="{
        digits: Array({{ $length }}).fill(''),

        // A getter (not a plain property) - this recalculates from
        // `digits` every time it's read, so the hidden input below
        // always reflects the boxes without us manually keeping two
        // separate values in sync.
        get code() { return this.digits.join('') },

        onInput(i, event) {
            // .replace(/\D/g, '') strips anything non-numeric (in case
            // of a stray paste-like browser behavior), .slice(-1) keeps
            // only the LAST character - handles the edge case where a
            // box already had a digit and the browser reports both the
            // old and newly-typed character in event.target.value.
            const value = event.target.value.replace(/\D/g, '').slice(-1);
            this.digits[i] = value;
            event.target.value = value;

            if (value && event.target.nextElementSibling) {
                event.target.nextElementSibling.focus();
            }

            this.$refs.hidden.dispatchEvent(new Event('input'));
        },

        onKeydown(i, event) {
            // Only jump back if THIS box is already empty - otherwise
            // a normal backspace-to-clear-this-box would also jump
            // focus away, which feels broken to type into.
            if (event.key === 'Backspace' && !this.digits[i] && event.target.previousElementSibling) {
                event.target.previousElementSibling.focus();
            }
        },

        onPaste(event) {
            event.preventDefault();
            const pasted = (event.clipboardData || window.clipboardData)
                .getData('text')
                .replace(/\D/g, '')
                .slice(0, {{ $length }})
                .split('');

            pasted.forEach((digit, i) => { this.digits[i] = digit; });

            this.$nextTick(() => {
                const boxes = this.$refs.boxes.querySelectorAll('input');
                boxes[Math.min(pasted.length, {{ $length }}) - 1]?.focus();
            });

            this.$refs.hidden.dispatchEvent(new Event('input'));
        },
    }"
    class="flex flex-col items-center gap-2"
>
    <div
        x-ref="boxes"
        role="group"
        aria-label="{{ __('One-time authentication code') }}"
        class="flex items-center justify-center gap-2"
    >
        @for ($i = 0; $i < $length; $i++)
            <input
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="1"
                {{-- data-otp-first lets the PARENT page (two-factor-challenge.blade.php)
                     focus this box from outside this component's own Alpine
                     scope, e.g. after switching back from the recovery-code
                     view - see toggleInput() there. --}}
                @if ($i === 0) data-otp-first autofocus @endif
                x-on:input="onInput({{ $i }}, $event)"
                x-on:keydown="onKeydown({{ $i }}, $event)"
                x-on:paste="onPaste($event)"
                class="w-11 h-12 text-center text-lg font-mono rounded-md border
                    bg-white dark:bg-ink-900
                    text-ink-950 dark:text-linen-50
                    border-ink-200 dark:border-ink-700
                    focus:outline-none focus:ring-2 focus:ring-copper-400 focus:border-copper-500"
            />
        @endfor
    </div>

    <input type="hidden" name="{{ $name }}" x-ref="hidden" x-bind:value="code" {{ $attributes }} />
</div>