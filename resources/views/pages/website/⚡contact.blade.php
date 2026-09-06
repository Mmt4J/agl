<?php

use App\Models\BusinessHour;
use App\Models\ContactMessage;
use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Contact')] class extends Component {
    public string $fullName = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    public bool $sent = false;

    #[Computed]
    public function company(): array
    {
        return [
            'name' => Setting::get('company.name', 'ANESMAVISA GLOBAL LTD'),
            'address' => Setting::get('company.address'),
            'email' => Setting::get('company.email'),
            'phone_primary' => Setting::get('company.phone_primary'),
            'phone_secondary' => Setting::get('company.phone_secondary'),
            'whatsapp' => Setting::get('company.whatsapp_number'),
            'whatsapp_message' => Setting::get('company.whatsapp_default_message', 'Hello, I would like to enquire about a service.'),
        ];
    }

    #[Computed]
    public function businessHours()
    {
        return BusinessHour::query()->orderByRaw('CASE WHEN day_of_week = 0 THEN 7 ELSE day_of_week END')->get();
    }

    #[Computed]
    public function isOpen(): bool
    {
        return BusinessHour::isOpenNow();
    }

    public function sendMessage(): void
    {
        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([
            'full_name' => $validated['fullName'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        $this->reset(['fullName', 'email', 'subject', 'message']);
        $this->sent = true;
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-14 sm:pt-20 sm:pb-20 text-center">
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">
                Correspondence</p>
            <h1 class="font-display font-semibold text-3xl sm:text-4xl lg:text-5xl tracking-tight">Let's talk</h1>
            <p class="mt-4 text-ink-900/65 dark:text-linen-100/65 max-w-2xl mx-auto text-base sm:text-lg">
                @if ($this->company['address'])
                    We're based at {{ $this->company['address'] }} and serve clients nationwide.
                @else
                    We serve clients nationwide.
                @endif
            </p>
        </div>
    </section>

    {{-- Info + Form --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid lg:grid-cols-5 gap-10">

        {{-- Left: contact details + map --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-md border border-ink-900/12 dark:border-linen-100/12 p-6">
                <h3 class="font-display font-semibold mb-4">Reach us directly</h3>
                <ul class="space-y-4 text-sm">
                    @if ($this->company['phone_primary'] || $this->company['phone_secondary'])
                        <li class="flex gap-3">
                            <svg class="w-5 h-5 text-copper-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 00-1.052.294l-1.51 1.51a11.25 11.25 0 01-5.516-5.516l1.51-1.51a1.125 1.125 0 00.294-1.052L8.844 3.852A1.125 1.125 0 007.75 3H6a2.25 2.25 0 00-2.25 2.25v1.5z" />
                            </svg>
                            <span>
                                @php
                                    $phones = collect([
                                        $this->company['phone_primary'],
                                        $this->company['phone_secondary'],
                                    ])->filter();
                                @endphp
                                @foreach ($phones as $i => $phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"
                                        class="hover:text-copper-600 dark:hover:text-copper-300">{{ $phone }}</a>{{ !$loop->last ? ' & ' : '' }}
                                @endforeach
                            </span>
                        </li>
                    @endif
                    @if ($this->company['email'])
                        <li class="flex gap-3">
                            <svg class="w-5 h-5 text-copper-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <a href="mailto:{{ $this->company['email'] }}"
                                class="hover:text-copper-600 dark:hover:text-copper-300">{{ $this->company['email'] }}</a>
                        </li>
                    @endif
                    @if ($this->company['address'])
                        <li class="flex gap-3">
                            <svg class="w-5 h-5 text-copper-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>{{ $this->company['address'] }}</span>
                        </li>
                    @endif
                    <li class="flex gap-3">
                        <svg class="w-5 h-5 text-copper-500 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                            <circle cx="12" cy="12" r="9" stroke-width="2" />
                        </svg>
                        <span class="flex items-center gap-2 flex-wrap">
                            @foreach ($this->businessHours as $hours)
                                <span wire:key="contact-hours-inline-{{ $hours->day_of_week }}"
                                    class="whitespace-nowrap">
                                    {{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$hours->day_of_week] }}:
                                    {{ $hours->is_closed ? 'Closed' : substr($hours->opens_at, 0, 5) . ' - ' . substr($hours->closes_at, 0, 5) }}{{ !$loop->last ? ',' : '' }}
                                </span>
                            @endforeach
                            <span
                                class="inline-flex items-center gap-1.5 font-mono text-[11px] px-2 py-0.5 rounded-full border
                                {{ $this->isOpen ? 'border-sage-500/40 text-sage-600 dark:text-sage-500' : 'border-ink-900/15 dark:border-linen-100/15 text-ink-900/40 dark:text-linen-100/40' }}">
                                <span
                                    class="w-1.5 h-1.5 rounded-full {{ $this->isOpen ? 'bg-sage-500' : 'bg-ink-900/30 dark:bg-linen-100/30' }}"></span>
                                <span>{{ $this->isOpen ? 'Open now' : 'Closed now' }}</span>
                            </span>
                        </span>
                    </li>
                </ul>

                @if ($this->company['whatsapp'])
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $this->company['whatsapp']) }}?text={{ urlencode($this->company['whatsapp_message']) }}"
                        target="_blank" rel="noopener"
                        class="mt-5 inline-flex items-center gap-2 font-semibold text-sm text-sage-600 dark:text-sage-400 hover:underline">
                        Message us on WhatsApp <span aria-hidden="true">→</span>
                    </a>
                @endif
            </div>

            <div
                class="rounded-md overflow-hidden border border-ink-900/12 dark:border-linen-100/12 h-56 bg-linen-100 dark:bg-ink-900/60 grid place-items-center text-ink-900/40 dark:text-linen-100/30 text-sm font-mono text-center px-6">
                Map placeholder {{ $this->company['address'] ? '— ' . $this->company['address'] : '' }}
            </div>
        </div>

        {{-- Right: form --}}
        <div class="lg:col-span-3">
            @if ($sent)
                <div class="rounded-md border-2 border-sage-500 bg-sage-500/10 p-6 sm:p-8" role="status">
                    <p class="font-mono text-xs uppercase tracking-widest text-sage-600 dark:text-sage-500 mb-2">
                        Entry filed</p>
                    <h2 class="font-display font-semibold text-2xl">Thanks for getting in touch.</h2>
                    <p class="mt-3 text-sm text-ink-900/70 dark:text-linen-100/70">Your message is on record. We will
                        reply within one business day.</p>
                    <button type="button" wire:click="$set('sent', false)"
                        class="mt-6 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-5 py-2.5 text-sm">
                        Send another message
                    </button>
                </div>
            @else
                <form wire:submit="sendMessage"
                    class="rounded-md border border-ink-900/12 dark:border-linen-100/12 p-6 sm:p-8 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="c-name" class="text-sm font-medium block mb-1.5">Full name</label>
                            <input id="c-name" wire:model="fullName" required type="text"
                                class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            @error('fullName')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="c-email" class="text-sm font-medium block mb-1.5">Email address</label>
                            <input id="c-email" wire:model="email" required type="email"
                                class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            @error('email')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="c-subject" class="text-sm font-medium block mb-1.5">Subject</label>
                        <input id="c-subject" wire:model="subject" required type="text"
                            class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                        @error('subject')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="c-message" class="text-sm font-medium block mb-1.5">Message</label>
                        <textarea id="c-message" wire:model="message" required rows="5"
                            class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-transparent px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500"></textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full sm:w-auto rounded-md bg-ink-900 dark:bg-copper-500 hover:bg-ink-800 dark:hover:bg-copper-600 disabled:opacity-60 text-linen-50 dark:text-ink-950 font-semibold px-7 py-3 text-sm transition-colors inline-flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="sendMessage" class="animate-spin w-4 h-4" viewBox="0 0 24 24"
                            fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        <span wire:loading.remove wire:target="sendMessage">Send message</span>
                        <span wire:loading wire:target="sendMessage">Sending…</span>
                    </button>
                </form>
            @endif
        </div>
    </section>
</div>
