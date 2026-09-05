<?php

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Services')] class extends Component
{
    #[Computed]
    public function services(): Collection
    {
        return Service::with('features')->ordered()->get();
    }
}; ?>

<div>
    <section class="border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32 grid lg:grid-cols-10 gap-12 lg:gap-20 items-center">
            <div class="lg:col-span-8">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-copper-600 dark:text-copper-300 mb-4">Schedule A · Services</p>
                <h1 class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.02]">Work entered under our name.</h1>
                <p class="mt-6 text-lg text-ink-900/70 dark:text-linen-100/70 max-w-2xl leading-relaxed">From a broken screen to a new operating system for your business, each service is scoped for useful outcomes and delivered by the right division.</p>
            </div>
            <div class="lg:col-span-4 lg:border-l lg:border-ink-900/15 dark:lg:border-linen-100/15 lg:pl-8">
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">How it works</p>
                <ol class="mt-4 space-y-3 text-sm">
                    <li class="flex gap-3"><span class="font-mono text-copper-600 dark:text-copper-300">01</span><span>Tell us what needs doing.</span></li>
                    <li class="flex gap-3"><span class="font-mono text-copper-600 dark:text-copper-300">02</span><span>We route it to the right specialist.</span></li>
                    <li class="flex gap-3"><span class="font-mono text-copper-600 dark:text-copper-300">03</span><span>You get a clear next step.</span></li>
                </ol>
            </div>
        </div>
    </section>

    @island(name: 'services-register')
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div><p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Schedule A - Services</p><h2 class="font-display font-semibold text-3xl sm:text-4xl">What's entered under our name</h2></div>
                <span class="self-start sm:self-auto font-mono text-xs text-ink-900/45 dark:text-linen-100/45">{{ $this->services->count() }} entries</span>
            </div>
            <div class="grid lg:grid-cols-2 border-t border-ink-900/15 dark:border-linen-100/15">
                @forelse ($this->services as $service)
                    <article wire:key="service-file-{{ $service->id }}" class="group border-b border-ink-900/15 dark:border-linen-100/15 lg:even:border-l lg:even:pl-8 lg:odd:pr-8 py-8">
                        <div class="flex items-start justify-between gap-5">
                            <div class="flex items-center gap-3"><span class="font-mono text-xs text-copper-600 dark:text-copper-300">{{ $service->code }}</span><h3 class="font-display font-semibold text-2xl">{{ $service->name }}</h3></div>
                            <span class="font-mono text-[10px] uppercase tracking-widest text-sage-600 dark:text-sage-500">Available</span>
                        </div>
                        <p class="mt-4 text-sm leading-relaxed text-ink-900/65 dark:text-linen-100/65">{{ $service->description }}</p>
                        @if ($service->features->isNotEmpty())
                            <ul class="mt-5 grid sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                @foreach ($service->features as $feature)
                                    <li class="flex gap-2"><span class="text-copper-500">+</span><span>{{ $feature->feature }}</span></li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('website.quote', ['service' => $service->slug]) }}" wire:navigate class="inline-block mt-6 text-sm font-semibold underline decoration-copper-500 decoration-2 underline-offset-4">Discuss {{ $service->name }} →</a>
                    </article>
                @empty
                    <p class="py-10 text-ink-900/50 dark:text-linen-100/50">The service register is being updated. Contact us and we will route your request.</p>
                @endforelse
            </div>
        </section>
    @endisland

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="rounded-md border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Open a new entry</p>
            <h2 class="font-display font-semibold text-2xl sm:text-4xl max-w-2xl mx-auto">Tell us what needs fixing, building, training, selling, or designing.</h2>
            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">One form, routed to the right division. We reply within one business day.</p>
            <a href="{{ route('website.quote') }}" wire:navigate class="inline-block mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Request a Quote</a>
        </div>
    </section>
</div>
