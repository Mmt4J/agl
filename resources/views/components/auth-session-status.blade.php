{{--
    resources/views/components/auth-session-status.blade.php
    ------------------------------------------------------------------
    Shows a one-line success message (e.g. "We have emailed your
    password reset link.") when Laravel flashes a `status` value to
    the session - Fortify does this itself, we don't trigger it.

    USAGE
        <x-auth-session-status class="text-center" :status="session('status')" />

    PROPS
        status   string|null   required   Usually session('status') passed straight in.
                                           Component renders nothing at all if this is empty.

    This one WAS already Flux-free in the original starter kit - the
    only change here is swapping the hardcoded text-green-600 for our
    own sage token, so a success message matches the rest of the
    palette instead of a generic default green.
--}}
@props(['status'])

@if ($status)
    {{--
        $attributes->merge() still applies even on a component this
        small - it's why callers can add class="text-center" on the
        tag (as in the usage example above) and have it combine with
        our own classes below, rather than needing a dedicated
        "$align" prop just for that one case.
    --}}
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-sage-600 dark:text-sage-400']) }}>
        {{ $status }}
    </div>
@endif
