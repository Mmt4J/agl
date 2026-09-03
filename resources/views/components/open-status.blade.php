{{--
    Live "Open now / Closed now" badge. Computed here, server-side, with
    Carbon::now('Africa/Lagos') - correct regardless of the visitor's
    own clock or timezone, and works even with JS disabled (the
    prototype's own version used the browser's clock as a stand-in for
    exactly this component).
--}}
@php
    $now = \Illuminate\Support\Carbon::now('Africa/Lagos');
    $today = \App\Models\BusinessHour::where('day_of_week', $now->dayOfWeek)->first();

    $isOpen = $today
        && ! $today->is_closed
        && $today->opens_at
        && $today->closes_at
        && $now->format('H:i:s') >= $today->opens_at
        && $now->format('H:i:s') <= $today->closes_at;
@endphp

<span
    class="hidden lg:inline-flex items-center gap-1.5 font-mono text-[11px] px-2.5 py-1 rounded-full border mr-1
        {{ $isOpen ? 'border-sage-500/40 text-sage-600 dark:text-sage-500' : 'border-ink-900/15 dark:border-linen-100/15 text-ink-900/40 dark:text-linen-100/40' }}"
    title="Business hours"
>
    <span class="w-1.5 h-1.5 rounded-full {{ $isOpen ? 'bg-sage-500' : 'bg-ink-900/30 dark:bg-linen-100/30' }}"></span>
    {{ $isOpen ? 'Open now' : 'Closed now' }}
</span>
