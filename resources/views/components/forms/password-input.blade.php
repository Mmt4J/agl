{{--
    resources/views/components/forms/password-input.blade.php
    ------------------------------------------------------------------
    A password field with a LIVE checklist beneath it - ticks off each
    rule in real time as the user types, instead of only finding out
    which rules failed after a full server round-trip on submit.

    USAGE
        <x-forms.password-input name="password" label="Password" required autocomplete="new-password" />

    IMPORTANT - THIS IS A MIRROR, NOT THE SOURCE OF TRUTH:
    The actual password policy is enforced by Fortify on the server,
    configured in app/Providers/AppServiceProvider.php via
    Password::defaults(). This component reads the SAME environment
    check (app()->isProduction()) to build a matching rule list, so
    what the user sees typing matches what the server will actually
    check. If that policy changes, this file's $rules below must be
    updated to match - there's no automatic link between the two, this
    is a deliberately parallel client-side copy for instant feedback.

    One rule from the real policy is NOT shown here on purpose:
    ->uncompromised() checks the password against a breached-password
    database (via the k-anonymity range API), which requires a live
    network call. That's inherently a server-side-only check - a
    password can tick every box below (length, case, number, symbol)
    and still fail on submit for being a known-leaked password. That's
    expected, not a bug in this checklist.
--}}
@props(['name', 'label'])

@php
    // Mirrors AppServiceProvider::configureDefaults() exactly:
    //   production -> min 12, mixed case, letters, numbers, symbols
    //   local/testing -> min 8, nothing else required
    $isProduction = app()->isProduction();
    $minLength = $isProduction ? 12 : 8;
    $hasError = $errors->has($name);
@endphp

<div x-data="{
    visible: false,
    value: '',
    touched: false,
    get hasMinLength() { return this.value.length >= {{ $minLength }} },
    @if($isProduction)
    get hasMixedCase() { return /[a-z]/.test(this.value) && /[A-Z]/.test(this.value) },
    get hasNumber() { return /[0-9]/.test(this.value) },
    get hasSymbol() { return /[^a-zA-Z0-9]/.test(this.value) },
    @endif
}" class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium text-ink-800 dark:text-linen-100">
        {{ $label }}
    </label>

    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" :type="visible ? 'text' : 'password'" type="password"
            {{--
                x-model keeps Alpine's `value` in sync with every
                keystroke SO THE CHECKLIST CAN REACT LIVE - but this
                doesn't change how the form actually submits. The
                input is still a normal named field; the browser
                submits its real DOM value on POST exactly as before.
                x-model just gives Alpine a live copy to read from.
            --}} x-model="value" @focus="touched = true"
            {{ $attributes->merge([
                'class' =>
                    'w-full rounded-md border px-3 py-2 text-sm
                                                        bg-white dark:bg-ink-900
                                                        text-ink-950 dark:text-linen-50
                                                        placeholder:text-ink-400 dark:placeholder:text-ink-600
                                                        focus:outline-none focus:ring-2 focus:ring-copper-400 focus:border-copper-500
                                                        transition-colors
                                                        ' .
                    ($hasError ? 'border-danger-500 dark:border-danger-400' : 'border-ink-200 dark:border-ink-700'),
            ]) }} />

        <button type="button" @click="visible = !visible"
            class="absolute inset-y-0 end-0 flex items-center px-3
                text-ink-500 hover:text-ink-800
                dark:text-linen-300 dark:hover:text-linen-50"
            :aria-label="visible ? 'Hide password' : 'Show password'">
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
    </div>

    {{--
        x-show="touched" (not just "always visible") means this list
        stays hidden until the user actually clicks into the field -
        showing a wall of red X's on a blank required field before
        they've typed anything reads as broken, not helpful.
    --}}
    <ul x-show="touched" x-cloak class="flex flex-col gap-1 mt-1">
        <li class="flex items-center gap-1.5 text-xs"
            :class="hasMinLength ? 'text-sage-600 dark:text-sage-400' : 'text-ink-400 dark:text-ink-500'">
            <svg x-show="hasMinLength" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <svg x-show="!hasMinLength" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="3">
                <circle cx="12" cy="12" r="3" />
            </svg>
            {{ __(':count characters minimum', ['count' => $minLength]) }}
        </li>

        @if ($isProduction)
            <li class="flex items-center gap-1.5 text-xs"
                :class="hasMixedCase ? 'text-sage-600 dark:text-sage-400' : 'text-ink-400 dark:text-ink-500'">
                <svg x-show="hasMixedCase" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <svg x-show="!hasMixedCase" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="3">
                    <circle cx="12" cy="12" r="3" />
                </svg>
                {{ __('Upper & lowercase letters') }}
            </li>
            <li class="flex items-center gap-1.5 text-xs"
                :class="hasNumber ? 'text-sage-600 dark:text-sage-400' : 'text-ink-400 dark:text-ink-500'">
                <svg x-show="hasNumber" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <svg x-show="!hasNumber" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="3">
                    <circle cx="12" cy="12" r="3" />
                </svg>
                {{ __('At least one number') }}
            </li>
            <li class="flex items-center gap-1.5 text-xs"
                :class="hasSymbol ? 'text-sage-600 dark:text-sage-400' : 'text-ink-400 dark:text-ink-500'">
                <svg x-show="hasSymbol" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <svg x-show="!hasSymbol" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="3">
                    <circle cx="12" cy="12" r="3" />
                </svg>
                {{ __('At least one symbol') }}
            </li>
        @endif
    </ul>

    @if ($hasError)
        <p class="text-sm text-danger-500 dark:text-danger-400">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
