{{--
    resources/views/components/forms/input.blade.php
    ------------------------------------------------------------------
    Reusable labeled text input, styled to the Ink/Copper/Linen palette.
    This is the direct replacement for <flux:input> (now removed).

    USAGE
        <x-forms.input name="email" label="Email address" type="email" required autofocus />
        <x-forms.input name="password" label="Password" type="password" viewable required />

    PROPS (declared below via @props - these are the ONLY attributes
    this component reads for its own logic; everything else you pass
    on the tag is forwarded untouched, see $attributes note further down)
        name          string   required   Used for id="", name="", and label's for=""
        label         string   required   Visible <label> text
        type          string   'text'     Any native input type
        value         mixed    null       Pre-fill, e.g. old('email')
        viewable      bool     false      Adds a show/hide toggle (for password fields)
--}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'viewable' => false])

{{--
    $errors is a Laravel MessageBag. Blade shares it with EVERY view
    automatically after a failed validation redirect - we never pass it
    in ourselves, it's just already available. Fortify triggers this
    for us on login/register failures, so this works with zero wiring.
--}}
@php
    $hasError = $errors->has($name);
@endphp

<div {{--
        x-data here only matters when $viewable is true (password
        fields). For a plain email/text input this Alpine component is
        still technically initialized, but does nothing and costs
        nothing measurable - simpler than conditionally adding x-data
        only sometimes.
    --}} x-data="{ visible: false }" class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium text-ink-800 dark:text-linen-100">
        {{ $label }}
    </label>

    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" {{--
                When viewable, Alpine's `:type` binding takes over and
                flips the real DOM attribute between "password" and
                "text" as the user clicks the eye icon. We still print
                the static type="{{ $type }}" first so the field is
                correctly masked BEFORE Alpine finishes booting on page
                load (avoids a one-frame flash of plaintext password).
            --}}
            @if ($viewable) :type="visible ? 'text' : 'password'" @endif type="{{ $type }}"
            value="{{ $value }}" {{--
                $attributes->merge() is doing two jobs at once:

                1. PASS-THROUGH: any attribute the caller wrote on
                   <x-forms.input ...> that ISN'T one of the @props
                   above - required, autofocus, autocomplete,
                   placeholder, wire:model, data-test - was NOT
                   consumed by @props, so Blade automatically collected
                   it into $attributes. Merging it here writes it onto
                   the real <input>. This is exactly how <flux:input
                   required autofocus> used to behave.

                2. CLASS MERGING: if the caller also added class="..."
                   on the tag, Blade appends it to ours below instead
                   of one silently overwriting the other.

                Everything inside the string below is the actual visual
                replacement for whatever Flux used to render: white/ink
                background depending on light/dark, copper focus ring,
                and a red border switch when $hasError is true.
            --}}
            {{ $attributes->merge([
                'class' =>
                    'w-full rounded-md border px-3 py-2 text-sm
                                bg-white dark:bg-ink-900
                                text-ink-950 dark:text-linen-50
                                placeholder:text-ink-400 dark:placeholder:text-ink-600
                                focus:outline-none focus:ring-2 focus:ring-copper-400 focus:border-copper-500
                                transition-colors
                                ' . ($hasError ? 'border-danger-500 dark:border-danger-400' : 'border-ink-200 dark:border-ink-700'),
            ]) }} />

        @if ($viewable)
            {{--
                Show/hide toggle. This is purely a client-side display
                preference - nothing here needs to reach the server, so
                it stays 100% Alpine and never becomes a Livewire
                property. (Matches the "Alpine owns decorative UI
                state, Livewire owns state that must survive
                wire:navigate" rule from earlier in this project.)
            --}}
            <button type="button" @click="visible = !visible"
                class="absolute inset-y-0 end-0 flex items-center px-3
                    text-ink-500 hover:text-ink-800
                    dark:text-linen-300 dark:hover:text-linen-50"
                :aria-label="visible ? 'Hide password' : 'Show password'">
                {{-- Two tiny inline "eye" SVGs, swapped with x-show. Keeps this
                     component dependency-free - no icon library import needed. --}}
                <svg x-show="!visible" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="visible" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 012.132-3.362m3.132-2.148A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.965 9.965 0 01-4.132 5.411M3 3l18 18" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Per-field validation message. Only renders when THIS field failed. --}}
    @if ($hasError)
        <p class="text-sm text-danger-500 dark:text-danger-400">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
