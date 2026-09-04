<?php

use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Home')] class extends Component
{
    // No Division model exists in the schema, so the registry index remains static.
    public array $divisions = [
        ['code' => '01', 'name' => 'Electronics', 'description' => 'Repairs and sales for phones, laptops, and home appliances - genuine parts, honest diagnostics.'],
        ['code' => '02', 'name' => 'Software', 'description' => 'Custom software built for real Nigerian business workflows, from POS systems to internal tools.'],
        ['code' => '03', 'name' => 'Web & Apps', 'description' => 'Websites and mobile apps designed to convert, built on modern, maintainable stacks.'],
        ['code' => '04', 'name' => 'Real Estate', 'description' => 'Property sourcing, sales facilitation and development advisory across Osun State and beyond.'],
        ['code' => '05', 'name' => 'Consulting', 'description' => 'Practical technology strategy for small businesses that need clarity, not jargon.'],
        ['code' => '06', 'name' => 'ICT Training', 'description' => 'Hands-on tech training and ICT upskilling for individuals, schools and teams.'],
        ['code' => '07', 'name' => 'Branding', 'description' => 'Digital branding, graphic design and fashion design that make small businesses look established.'],
    ];

    #[Computed]
    public function services(): Collection
    {
        return Service::ordered()->take(8)->get();
    }

    #[Computed]
    public function portfolioProjects(): Collection
    {
        return PortfolioProject::with('category')->orderBy('sort_order')->take(3)->get();
    }

    #[Computed]
    public function testimonials(): Collection
    {
        return Testimonial::approved()->orderBy('sort_order')->get();
    }

    #[Computed]
    public function companySettings(): array
    {
        return [
            'rc_number' => Setting::get('company.rc_number'),
            'scuml_number' => Setting::get('company.scuml_number'),
            'address' => Setting::get('company.address'),
        ];
    }

}; ?>

<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full text-ink-900/[0.05] dark:text-linen-100/[0.04] seal-ring" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-16 sm:pt-20 sm:pb-24 grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-6">
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-copper-600 dark:text-copper-300 mb-4">Entry No. {{ $this->companySettings['rc_number'] }} · Registered 15 Mar 2026</p>
                <h1 class="font-display font-semibold text-4xl sm:text-5xl lg:text-[3.4rem] leading-[1.08] tracking-tight text-ink-900 dark:text-linen-50">Seven trades.<br> One registered company.<br> <span class="text-copper-500 dark:text-copper-300">Every job on the record.</span></h1>
                <p class="mt-6 text-ink-900/70 dark:text-linen-100/70 text-base sm:text-lg max-w-lg leading-relaxed">ANESMAVISA GLOBAL LTD keeps electronics, software, property, training and design work under one CAC-registered, SCUML-compliant name - so nothing you commission is off the books.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('website.quote') }}" wire:navigate class="rounded-md bg-ink-900 dark:bg-copper-500 hover:bg-ink-800 dark:hover:bg-copper-600 text-linen-50 dark:text-ink-950 font-semibold px-6 py-3.5 text-sm sm:text-base transition-colors">Book a Service</a>
                    <a href="{{ route('website.services') }}" wire:navigate class="rounded-md border border-ink-900/25 dark:border-linen-100/25 hover:border-ink-900/60 dark:hover:border-linen-100/60 font-semibold px-6 py-3.5 text-sm sm:text-base transition-colors">Open the Services File</a>
                </div>
                <dl class="mt-12 grid grid-cols-3 border-t border-ink-900/15 dark:border-linen-100/15 divide-x divide-ink-900/15 dark:divide-linen-100/15 max-w-md">
                    <div class="pt-4 pr-4"><dt class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">Divisions</dt><dd class="font-display font-semibold text-2xl mt-1">07</dd></div>
                    <div class="pt-4 px-4"><dt class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">Status</dt><dd class="font-display font-semibold text-2xl mt-1 text-sage-600 dark:text-sage-500">Active</dd></div>
                    <div class="pt-4 pl-4"><dt class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">SCUML</dt><dd class="font-display font-semibold text-2xl mt-1">Yes</dd></div>
                </dl>
            </div>

        {{-- Register of Divisions - tabbed panel, purely client-side
             switching (no server round-trip needed for a static list). --}}
        <div x-data="{ active: 0 }" class="lg:col-span-6 rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white/60 dark:bg-linen-100/[0.04] overflow-hidden">
            <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40 px-5 pt-5">Register of Divisions</p>

            <div role="tablist" aria-label="Business divisions">
                @foreach ($divisions as $i => $division)
                    <button
                        type="button"
                        @click="active = {{ $i }}"
                        role="tab"
                        :aria-pressed="active === {{ $i }}"
                        class="entry-row w-full flex items-center gap-4 text-left px-5 py-3.5 border-l-4 border-transparent border-t border-ink-900/10 dark:border-linen-100/10 first:border-t-0 hover:bg-ink-900/[0.03] dark:hover:bg-linen-100/[0.05] transition-colors"
                    >
                        <span class="font-mono text-xs text-copper-600 dark:text-copper-300 w-6 shrink-0">{{ $division['code'] }}</span>
                        <span class="text-sm font-medium flex-1">{{ $division['name'] }}</span>
                        <svg class="w-4 h-4 shrink-0 text-ink-900/30 dark:text-linen-100/30" :class="active === {{ $i }} ? 'rotate-90 text-copper-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </button>
                @endforeach
            </div>

            @foreach ($divisions as $i => $division)
                <div x-show="active === {{ $i }}" x-cloak class="p-6 bg-ink-900 dark:bg-linen-100/[0.06] text-linen-50 min-h-[150px]">
                    <div class="flex items-center gap-2 mb-3"><span class="text-sage-500">✓</span><span class="font-mono text-[10px] uppercase tracking-widest text-sage-500">Verified division</span></div>
                    <h3 class="font-display font-semibold text-lg">{{ $division['name'] }}</h3>
                    <p class="text-linen-100/75 text-sm mt-2 leading-relaxed">{{ $division['description'] }}</p>
                </div>
            @endforeach
        </div>
        </div>
    </section>

    {{-- Trust strip --}}
    <section class="border-b border-ink-900/10 dark:border-linen-100/10 bg-linen-100 dark:bg-ink-900/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-wrap items-center justify-center gap-x-10 gap-y-2 text-xs sm:text-sm font-mono text-ink-900/60 dark:text-linen-100/60">
            <span>CAC · RC {{ $this->companySettings['rc_number'] }}</span><span class="text-ink-900/20 dark:text-linen-100/20">/</span>
            <span>SCUML · RN {{ $this->companySettings['scuml_number'] }}</span><span class="text-ink-900/20 dark:text-linen-100/20">/</span>
            <span>{{ $this->companySettings['address'] ?: 'Osogbo, Osun State' }}</span><span class="text-ink-900/20 dark:text-linen-100/20">/</span><span>Incorporated 15 Mar 2026</span>
        </div>
    </section>

    {{-- Services preview --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div><p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Schedule A - Services</p><h2 class="font-display font-semibold text-3xl sm:text-4xl">What's entered under our name</h2></div>
            <a href="{{ route('website.services') }}" wire:navigate class="self-start sm:self-auto font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">Open full schedule →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($this->services as $service)
                <article wire:key="service-{{ $service->id }}" class="group rounded-md border border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40 p-6 hover:border-copper-500/60 transition-colors"><div class="flex items-start justify-between mb-4"><span class="w-10 h-10 rounded-md bg-ink-900/5 dark:bg-linen-100/5 text-ink-900 dark:text-copper-300 grid place-items-center">{{ $service->code }}</span><span class="font-mono text-[10px] text-ink-900/30 dark:text-linen-100/30">{{ $service->code }}</span></div><h3 class="font-display font-semibold text-base">{{ $service->name }}</h3><p class="text-sm text-ink-900/60 dark:text-linen-100/60 mt-2 leading-relaxed">{{ $service->short_description }}</p></article>
            @endforeach
        </div>
    </section>

    {{-- About preview --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-y border-ink-900/10 dark:border-linen-100/10"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-12 items-center"><div class="order-2 lg:order-1"><p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Certificate of record</p><h2 class="font-display font-semibold text-3xl sm:text-4xl mb-5">A registered partner, not a side hustle</h2><p class="text-ink-900/70 dark:text-linen-100/70 leading-relaxed">Founded by Matthew Ayodele Alabi and Samuel Gift Alabi, ANESMAVISA GLOBAL LTD was incorporated in March 2026 as a private company limited by shares, and registered with SCUML two months later. We consolidate services individuals and small businesses usually source from six unregistered vendors.</p><a href="{{ route('website.about') }}" wire:navigate class="inline-block mt-8 font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">Read the full record →</a></div><img src="https://placehold.co/640x520/16213a/eef0e2?text=ANESMAVISA+GLOBAL+LTD" alt="ANESMAVISA GLOBAL LTD team" class="order-1 lg:order-2 rounded-md w-full h-full object-cover" loading="lazy"></div></section>
    </section>

    {{-- Portfolio preview --}}
    @if ($this->portfolioProjects->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div><p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Case file</p><h2 class="font-display font-semibold text-3xl sm:text-4xl">Work on record</h2></div>
                <a href="{{ route('website.portfolio') }}" wire:navigate class="font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">View full portfolio →</a>
            </div>
            <div class="grid sm:grid-cols-3 gap-6">
                @foreach ($this->portfolioProjects as $project)
                    <article wire:key="project-{{ $project->id }}" class="rounded-md border border-ink-900/12 dark:border-linen-100/12 overflow-hidden bg-white dark:bg-ink-900/40">
                        @if ($project->image_path)
                            <img src="{{ $project->image_path }}" alt="{{ $project->title }}" class="w-full h-40 object-cover" loading="lazy" />
                        @else
                            <div class="w-full h-44 bg-ink-900/5 dark:bg-linen-100/5"></div>
                        @endif
                        <div class="p-5">
                            <span class="font-mono text-[10px] uppercase text-copper-600 dark:text-copper-300">{{ $project->category->name }}</span>
                            <h3 class="font-display font-semibold text-sm mt-1">{{ $project->title }}</h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Testimonials carousel --}}
    @if ($this->testimonials->isNotEmpty())
        <section class="bg-ink-900 dark:bg-ink-950 text-linen-50 border-y border-ink-900/10 dark:border-linen-100/10">
            <div
                x-data="{
                    items: @js($this->testimonials->values()),
                    active: 0,
                    interval: null,
                    init() {
                        this.interval = setInterval(() => {
                            this.active = (this.active + 1) % this.items.length;
                        }, 6000);
                    },
                }"
                class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center"
            >
                <p class="font-mono text-xs uppercase tracking-widest text-copper-300 mb-6">Statements on file</p>
                <template x-for="(testimonial, i) in items" :key="testimonial.id">
                    <div x-show="active === i" x-cloak x-transition.opacity>
                        <p class="font-display italic text-xl sm:text-2xl leading-relaxed">&ldquo;<span x-text="testimonial.quote"></span>&rdquo;</p>
                        <p class="mt-6 font-semibold text-copper-300 font-mono text-sm" x-text="testimonial.client_name"></p>
                        <p class="text-sm text-linen-100/60" x-text="testimonial.client_role"></p>
                    </div>
                </template>

                <div class="flex justify-center gap-1.5 mt-6">
                    <template x-for="(testimonial, i) in items" :key="'dot-' + testimonial.id">
                        <button @click="active = i" class="w-1.5 h-1.5 rounded-full" :class="active === i ? 'bg-copper-500' : 'bg-ink-900/20 dark:bg-linen-100/20'" :aria-label="'Show testimonial ' + (i + 1)"></button>
                    </template>
                </div>
            </div>
        </section>
    @endif

    {{-- CTA banner --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="rounded-md border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Open a new entry</p>
            <h2 class="font-display font-semibold text-2xl sm:text-4xl max-w-2xl mx-auto">Tell us what needs fixing, building, training, selling, or designing.</h2>
            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">One form, routed to the right division. We reply within one business day.</p>
            <a href="{{ route('website.quote') }}" wire:navigate class="inline-block mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Request a Quote</a>
        </div>
    </section>
</div>
