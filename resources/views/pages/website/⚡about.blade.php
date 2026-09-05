<?php

use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('About')] class extends Component
{
    #[Computed]
    public function companySettings(): array
    {
        return [
            'rc_number' => Setting::get('company.rc_number', 'Pending'),
            'scuml_number' => Setting::get('company.scuml_number', 'Pending'),
            'address' => Setting::get('company.address', 'Osogbo, Osun State'),
        ];
    }

    #[Computed]
    public function featuredServices(): Collection
    {
        return Service::featured()->ordered()->take(4)->get();
    }

    #[Computed]
    public function testimonials(): Collection
    {
        return Testimonial::approved()->take(3)->get();
    }
}; ?>

<div>
    <section class="bg-linen-100 dark:bg-ink-900/40 border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32 text-center">
            <div class="max-w-3xl mx-auto">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-copper-600 dark:text-copper-300 mb-4">Certificate of record</p>
                <h1 class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.02]">A registered partner, not a side hustle.</h1>
                <p class="mt-6 mx-auto text-lg text-ink-900/70 dark:text-linen-100/70 max-w-2xl leading-relaxed">ANESMAVISA GLOBAL LTD brings practical technology, property, training, repair and design services under one accountable name.</p>
            </div>
            <div class="max-w-3xl mx-auto mt-12 pt-8 border-t border-ink-900/15 dark:border-linen-100/15">
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50 mb-5">Company register</p>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                    <div class="space-y-1"><dt class="block text-ink-900/50 dark:text-linen-100/50">RC number</dt><dd class="block font-mono">{{ $this->companySettings['rc_number'] }}</dd></div>
                    <div class="space-y-1"><dt class="block text-ink-900/50 dark:text-linen-100/50">SCUML number</dt><dd class="block font-mono">{{ $this->companySettings['scuml_number'] }}</dd></div>
                    <div class="sm:col-span-2 space-y-1"><dt class="block text-ink-900/50 dark:text-linen-100/50">Registered office</dt><dd class="block wrap-break-word">{{ $this->companySettings['address'] }}</dd></div>
                </dl>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-12">
        <div>
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Why the company exists</p>
            <h2 class="font-display font-semibold text-3xl sm:text-4xl">Seven trades, one standard of care.</h2>
        </div>
        <div class="space-y-5 text-ink-900/70 dark:text-linen-100/70 leading-relaxed">
            <p>Small businesses should not have to choose between a specialist who disappears after payment and a large agency that cannot see the work at street level. We built ANESMAVISA to make capable, practical help easier to find and easier to trust.</p>
            <p>Every engagement is scoped clearly, routed to the right division and kept on the record. That means honest diagnostics for a device, a maintainable system for a business, and a real person accountable for the result.</p>
            <div class="border-l-2 border-copper-500 pl-5 pt-1 text-ink-900 dark:text-linen-50 font-display text-xl">Good work should leave a clear paper trail.</div>
        </div>
    </section>

    <section class="bg-ink-900 text-linen-50 border-y border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-10">
                <div><p class="font-mono text-xs uppercase tracking-widest text-copper-300 mb-2">Our operating brief</p><h2 class="font-display font-semibold text-3xl sm:text-4xl">What we bring to the table</h2></div>
                <a href="{{ route('website.services') }}" wire:navigate class="font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">See all services →</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse ($this->featuredServices as $service)
                    <article wire:key="about-service-{{ $service->id }}" class="border border-linen-100/15 p-5">
                        <span class="font-mono text-xs text-copper-300">{{ $service->code }}</span>
                        <h3 class="font-display font-semibold text-xl mt-8">{{ $service->name }}</h3>
                        <p class="text-sm text-linen-100/65 mt-2 leading-relaxed">{{ $service->short_description }}</p>
                    </article>
                @empty
                    <p class="text-linen-100/60">Our service register is being updated.</p>
                @endforelse
            </div>
        </div>
    </section>

    @if ($this->testimonials->isNotEmpty())
        <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-8">Statements on file</p>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($this->testimonials as $testimonial)
                    <figure wire:key="about-testimonial-{{ $testimonial->id }}" class="border-t-2 border-copper-500 pt-5">
                        <blockquote class="font-display text-xl leading-relaxed">“{{ $testimonial->quote }}”</blockquote>
                        <figcaption class="mt-5 text-sm"><span class="font-semibold">{{ $testimonial->client_name }}</span><span class="block text-ink-900/50 dark:text-linen-100/50">{{ $testimonial->client_role }}</span></figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="rounded-md border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Open a new entry</p>
            <h2 class="font-display font-semibold text-2xl sm:text-4xl max-w-2xl mx-auto">Tell us what needs fixing, building, training, selling, or designing.</h2>
            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">One form, routed to the right division. We reply within one business day.</p>
            <a href="{{ route('website.quote') }}" wire:navigate class="inline-block mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Request a Quote</a>
        </div>
    </section>
