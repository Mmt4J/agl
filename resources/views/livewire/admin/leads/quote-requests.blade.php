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
        return Service::featured()->ordered()->get();
    }

    public function continueToDetails(): void
    {
        $this->validate(['serviceId' => ['required', 'exists:services,id']]);
        $this->step = 2;
    }

    public function continueToContact(): void
    {
        $this->validate([
            'preferredDate' => ['nullable', '   date', 'after_or_equal:today'],
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
    {{-- Hero --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-14 sm:pt-20 sm:pb-20 text-center">
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">New Entry
            </p>
            <h1 class="font-display font-semibold text-3xl sm:text-4xl lg:text-5xl tracking-tight">Book a service or
                request a quote</h1>
            <p class="mt-4 text-ink-900/65 dark:text-linen-100/65 max-w-2xl mx-auto text-base sm:text-lg">We route it
                to the right division and reply within one business day.</p>
        </div>
    </section>

    <section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Progress --}}
        <ol class="flex items-center justify-between mb-10" aria-label="Quote request progress">
            @foreach ([1 => 'The service', 2 => 'The details', 3 => 'Your contact'] as $number => $label)
                <li class="flex-1 flex items-center">
                    <span
                        class="w-8 h-8 rounded-full grid place-items-center text-xs font-mono font-bold shrink-0 {{ $step >= $number ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950' : 'bg-ink-900/10 text-ink-900/40 dark:bg-linen-100/10 dark:text-linen-100/40' }}">
                        {{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    @if ($number < 3)
                        <span
                            class="h-px flex-1 mx-2 {{ $step > $number ? 'bg-copper-500' : 'bg-ink-900/10 dark:bg-linen-100/10' }}"></span>
                    @endif
                </li>
            @endforeach
        </ol>

        @if ($submitted)
            <div class="rounded-md border-2 border-sage-500 bg-sage-500/10 p-6 sm:p-8" role="status">
                <p class="font-mono text-xs uppercase tracking-widest text-sage-600 dark:text-sage-500 mb-2">Entry
                    filed</p>
                <h2 class="font-display font-semibold text-2xl">Your request is on record.</h2>
                <p class="mt-3 text-sm text-ink-900/70 dark:text-linen-100/70">Thank you, {{ $fullName }}. Our team
                    will review the details and contact you within one business day.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('website.home') }}" wire:navigate
                        class="rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-5 py-3 text-sm">Back
                        to home</a>
                    <button type="button" wire:click="resetForm"
                        class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-5 py-3 text-sm">File
                        another request</button>
                </div>
            </div>
        @else
            <div class="rounded-md border border-ink-900/12 dark:border-linen-100/12 p-6 sm:p-8">

                {{-- Step 1 --}}
                @if ($step === 1)
                    <h2 class="font-display font-semibold text-xl mb-5">1. What do you need?</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach ($this->services as $service)
                            <label wire:key="quote-service-{{ $service->id }}"
                                class="flex items-center gap-3 rounded-md border px-4 py-3 cursor-pointer transition-colors {{ $serviceId === $service->id ? 'border-copper-500 bg-copper-500/5' : 'border-ink-900/12 dark:border-linen-100/12' }}">
                                <input type="radio" wire:model="serviceId" value="{{ $service->id }}"
                                    class="accent-copper-500">
                                <span class="text-sm font-medium">{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('serviceId')
                        <p class="mt-3 text-xs text-danger-500">{{ $message }}</p>
                    @enderror
                    <button type="button" wire:click="continueToDetails"
                        class="mt-8 w-full sm:w-auto rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-7 py-3 text-sm">Continue</button>

                    {{-- Step 2 --}}
                @elseif ($step === 2)
                    <h2 class="font-display font-semibold text-xl mb-5">2. Describe the job</h2>
                    <div class="space-y-5">
                        <div>
                            <label for="preferred-date" class="text-sm font-medium block mb-1.5">Preferred
                                date</label>
                            <input id="preferred-date" type="date" wire:model="preferredDate"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            @error('preferredDate')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="quote-details" class="text-sm font-medium block mb-1.5">Details</label>
                            <textarea id="quote-details" wire:model="details" rows="4"
                                placeholder="e.g. We need a mobile-first website for a fashion retailer, with WhatsApp checkout and a simple product catalogue."
                                class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500"></textarea>
                            @error('details')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8">
                        <button type="button" wire:click="$set('step', 1)"
                            class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-6 py-3 text-sm">Back</button>
                        <button type="button" wire:click="continueToContact"
                            class="rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-7 py-3 text-sm">Continue</button>
                    </div>

                    {{-- Step 3 --}}
                @else
                    <h2 class="font-display font-semibold text-xl mb-5">3. Your contact information</h2>
                    <form wire:submit="submitQuote">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label for="quote-name" class="text-sm font-medium block mb-1.5">Full name</label>
                                <input id="quote-name" wire:model="fullName" required type="text"
                                    class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                                @error('fullName')
                                    <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="quote-phone" class="text-sm font-medium block mb-1.5">Phone
                                    number</label>
                                <input id="quote-phone" wire:model="phone" required type="tel"
                                    class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                                @error('phone')
                                    <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-5">
                            <label for="quote-email" class="text-sm font-medium block mb-1.5">Email address</label>
                            <input id="quote-email" wire:model="email" required type="email"
                                class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            @error('email')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-8">
                            <button type="button" wire:click="$set('step', 2)"
                                class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-6 py-3 text-sm">Back</button>
                            <button type="submit" wire:loading.attr="disabled"
                                class="rounded-md bg-copper-500 hover:bg-copper-600 disabled:opacity-50 text-linen-50 font-semibold px-7 py-3 text-sm inline-flex items-center gap-2">
                                <svg wire:loading wire:target="submitQuote" class="animate-spin w-4 h-4"
                                    viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                </svg>
                                <span wire:loading.remove wire:target="submitQuote">Submit request</span>
                                <span wire:loading wire:target="submitQuote">Submitting…</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </section>
</div>
