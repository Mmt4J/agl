<?php

use App\Models\QuoteRequest;
use App\Models\Service;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Request a Quote')] class extends Component {
    public int $step = 1;
    public ?int $serviceId = null;
    public string $preferredDate = '';
    public string $details = '';
    public string $fullName = '';
    public string $phone = '';
    public string $email = '';
    public bool $submitted = false;

    #[Computed]
    public function services()
    {
        return Service::query()->ordered()->get();
    }

    public function continueToDetails(): void
    {
        $this->validate(['serviceId' => ['required', 'exists:services,id']]);
        $this->step = 2;
    }

    public function continueToContact(): void
    {
        $this->validate([
            'preferredDate' => ['nullable', 'date', 'after_or_equal:today'],
            'details' => ['required', 'string', 'min:10', 'max:5000'],
        ]);
        $this->step = 3;
    }

    public function submitQuote(): void
    {
        $validated = $this->validate([
            'serviceId' => ['required', 'exists:services,id'],
            'preferredDate' => ['nullable', 'date', 'after_or_equal:today'],
            'details' => ['required', 'string', 'min:10', 'max:5000'],
            'fullName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        QuoteRequest::create([
            'service_id' => $validated['serviceId'],
            'preferred_date' => $validated['preferredDate'] ?: null,
            'details' => $validated['details'],
            'full_name' => $validated['fullName'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'status' => 'new',
        ]);

        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->step = 1;
    }
}; ?>

<div>
    <section class="relative overflow-hidden border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="absolute -right-24 -top-24 size-96 rounded-full text-ink-900/[0.05] dark:text-linen-100/[0.04] seal-ring"
            aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="grid lg:grid-cols-12 gap-10 items-end">
                <div class="lg:col-span-8">
                    <p class="font-mono text-xs uppercase tracking-[0.2em] text-copper-600 dark:text-copper-300 mb-5">New
                        entry · Quote request</p>
                    <h1
                        class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.05] tracking-tight">
                        Book a service.<br><span class="text-copper-500 dark:text-copper-300">Put the scope in
                            writing.</span></h1>
                    <p
                        class="mt-6 max-w-2xl text-base sm:text-lg text-ink-900/70 dark:text-linen-100/70 leading-relaxed">
                        Tell us what needs building, fixing, training, selling, or designing. We route your request to
                        the right division and reply within one business day.</p>
                </div>
                <div class="lg:col-span-4 lg:border-l lg:border-ink-900/15 dark:lg:border-linen-100/15 lg:pl-8">
                    <p
                        class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50 mb-3">
                        Correspondence code</p>
                    <p class="font-display text-2xl font-semibold">QUOTE /
                        {{ str_pad((string) $step, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="mt-2 text-sm text-ink-900/60 dark:text-linen-100/60">Three short steps. No payment is
                        taken here.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-ink-900/10 dark:border-linen-100/10 bg-linen-100 dark:bg-ink-900/40">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <ol class="grid grid-cols-3 gap-2" aria-label="Quote request progress">
                @foreach ([1 => 'The service', 2 => 'The details', 3 => 'Your contact'] as $number => $label)
                    <li
                        class="flex items-center gap-3 {{ $step >= $number ? 'text-ink-900 dark:text-linen-50' : 'text-ink-900/35 dark:text-linen-100/35' }}">
                        <span
                            class="size-8 shrink-0 grid place-items-center font-mono text-xs {{ $step >= $number ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950' : 'border border-ink-900/20 dark:border-linen-100/20' }}">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="hidden sm:block text-xs font-medium">{{ $label }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        @if ($submitted)
            <div class="border-2 border-sage-500 bg-sage-500/10 p-7 sm:p-10" role="status">
                <p class="font-mono text-xs uppercase tracking-widest text-sage-600 dark:text-sage-500 mb-3">Entry filed
                </p>
                <h2 class="font-display font-semibold text-3xl">Your request is on record.</h2>
                <p class="mt-3 text-sm text-ink-900/70 dark:text-linen-100/70 max-w-xl">Thank you, {{ $fullName }}.
                    Our team will review the details and contact you within one business day.</p>
                <div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('website.home') }}" wire:navigate
                        class="rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-5 py-3 text-sm">Back
                        to the register</a><button type="button" wire:click="resetForm"
                        class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-5 py-3 text-sm">File
                        another request</button></div>
            </div>
        @else
            <div class="border-t-2 border-copper-500 pt-6">
                @if ($step === 1)
                    <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">01
                        / The service</p>
                    <h2 class="font-display font-semibold text-3xl">What do you need?</h2>
                    <p class="mt-3 text-sm text-ink-900/60 dark:text-linen-100/60">Choose the division that best matches
                        your request. We can involve more than one team later.</p>
                    <div class="grid sm:grid-cols-2 gap-3 mt-8">
                        @foreach ($this->services as $service)
                            <label wire:key="quote-service-{{ $service->id }}"
                                class="flex items-start gap-3 border p-4 cursor-pointer transition-colors {{ $serviceId === $service->id ? 'border-copper-500 bg-copper-500/5' : 'border-ink-900/12 dark:border-linen-100/12 hover:border-copper-500/50' }}"><input
                                    type="radio" wire:model="serviceId" value="{{ $service->id }}"
                                    class="mt-1 accent-copper-500"><span><span
                                        class="block font-mono text-[10px] text-copper-600 dark:text-copper-300">{{ $service->code }}</span><span
                                        class="block text-sm font-semibold mt-1">{{ $service->name }}</span><span
                                        class="block text-xs text-ink-900/55 dark:text-linen-100/55 mt-1">{{ $service->short_description }}</span></span></label>
                        @endforeach
                    </div>
                    @error('serviceId')
                        <p class="mt-3 text-xs text-danger-500">{{ $message }}</p>
                    @enderror
                    <button type="button" wire:click="continueToDetails"
                        class="mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-7 py-3 text-sm">Continue
                        -></button>
                @elseif ($step === 2)
                    <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">02
                        / The details</p>
                    <h2 class="font-display font-semibold text-3xl">Describe the job.</h2>
                    <p class="mt-3 text-sm text-ink-900/60 dark:text-linen-100/60">A little context helps us assign the
                        right person and prepare before we call.</p>
                    <div class="space-y-5 mt-8">
                        <div><label for="preferred-date"
                                class="font-mono text-[10px] uppercase tracking-widest block mb-2">Preferred date <span
                                    class="normal-case tracking-normal text-ink-900/40">(optional)</span></label><input
                                id="preferred-date" type="date" wire:model="preferredDate"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            @error('preferredDate')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div><label for="quote-details"
                                class="font-mono text-[10px] uppercase tracking-widest block mb-2">Details</label>
                            <textarea id="quote-details" wire:model="details" rows="7"
                                placeholder="For example: We need a mobile-first website for a fashion retailer, with WhatsApp checkout and a simple product catalogue."
                                class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500"></textarea>
                            @error('details')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8"><button type="button" wire:click="$set('step', 1)"
                            class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-6 py-3 text-sm">Back</button><button
                            type="button" wire:click="continueToContact"
                            class="rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-7 py-3 text-sm">Continue
                            -></button></div>
                @else
                    <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">03
                        / Your contact</p>
                    <h2 class="font-display font-semibold text-3xl">Where should we reach you?</h2>
                    <p class="mt-3 text-sm text-ink-900/60 dark:text-linen-100/60">We use these details only to respond
                        to this request.</p>
                    <form wire:submit="submitQuote" class="space-y-5 mt-8">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div><label for="quote-name"
                                    class="font-mono text-[10px] uppercase tracking-widest block mb-2">Full
                                    name</label><input id="quote-name" wire:model="fullName" required type="text"
                                    class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                                @error('fullName')
                                    <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div><label for="quote-phone"
                                    class="font-mono text-[10px] uppercase tracking-widest block mb-2">Phone
                                    number</label><input id="quote-phone" wire:model="phone" required type="tel"
                                    class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                                @error('phone')
                                    <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div><label for="quote-email"
                                class="font-mono text-[10px] uppercase tracking-widest block mb-2">Email
                                address</label><input id="quote-email" wire:model="email" required type="email"
                                class="w-full border border-ink-900/15 dark:bg-ink-900/40 dark:border-linen-100/15 bg-white px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            @error('email')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex gap-3 pt-3"><button type="button" wire:click="$set('step', 2)"
                                class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-6 py-3 text-sm">Back</button><button
                                type="submit" wire:loading.attr="disabled"
                                class="rounded-md bg-copper-500 hover:bg-copper-600 disabled:opacity-60 text-linen-50 font-semibold px-7 py-3 text-sm"><span
                                    wire:loading.remove wire:target="submitQuote">File request -></span><span
                                    wire:loading wire:target="submitQuote">Filing...</span></button></div>
                    </form>
                @endif
            </div>
        @endif
    </section>
</div>
