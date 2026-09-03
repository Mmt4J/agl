<?php

use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Home')] class extends Component
{
    // Not admin-managed - matches the prototype's own hardcoded array,
    // no Division model exists in the schema.
    public array $divisions = [
        ['code' => 'A', 'name' => 'Electronics & Devices', 'description' => 'Phone, laptop and appliance sales, service and repair.'],
        ['code' => 'B', 'name' => 'Software & ICT', 'description' => 'Web apps, business systems and IT support.'],
        ['code' => 'C', 'name' => 'Real Estate', 'description' => 'Property sales, letting and management.'],
        ['code' => 'D', 'name' => 'Trading & Supplies', 'description' => 'General merchandise and procurement.'],
        ['code' => 'E', 'name' => 'Logistics', 'description' => 'Haulage, dispatch and delivery services.'],
        ['code' => 'F', 'name' => 'Consulting', 'description' => 'Business advisory and process improvement.'],
        ['code' => 'G', 'name' => 'Training', 'description' => 'ICT and vocational skills training.'],
    ];

    public array $aboutStats = [
        ['value' => '2026', 'label' => 'Founded'],
        ['value' => '7', 'label' => 'Trade divisions'],
        ['value' => '2', 'label' => 'Co-founders'],
        ['value' => 'Lagos', 'label' => 'Headquartered'],
    ];

    public function services()
    {
        return Service::ordered()->take(6)->get();
    }

    public function portfolioProjects()
    {
        return PortfolioProject::with('category')->orderBy('sort_order')->take(3)->get();
    }

    public function testimonials()
    {
        return Testimonial::approved()->orderBy('sort_order')->get();
    }

    public function companySetting(string $key): ?string
    {
        return Setting::get($key);
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pt-14 pb-16 grid lg:grid-cols-2 gap-12 items-start">
        <div class="space-y-6">
            <span class="inline-block font-mono text-[11px] tracking-widest text-copper-600 dark:text-copper-300 uppercase">
                RC {{ $this->companySetting('company.rc_number') }}
            </span>
            <h1 class="font-display text-4xl sm:text-5xl font-semibold leading-tight text-ink-950 dark:text-linen-50">
                Multi-trade services, one trusted partner.
            </h1>
            <p class="text-ink-700 dark:text-linen-200 text-lg leading-relaxed">
                From electronics repair to real estate and ICT, Anesmavisa Global brings seven trade divisions under one roof - so you deal with one team you already trust, not seven different contractors.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('website.quote') }}" wire:navigate class="inline-flex items-center rounded-md bg-copper-500 hover:bg-copper-600 text-ink-950 font-semibold px-6 py-3 text-sm transition-colors">
                    Request a Quote
                </a>
                <a href="{{ route('website.services') }}" wire:navigate class="inline-flex items-center rounded-md border border-ink-900/15 dark:border-linen-100/15 hover:bg-ink-900/5 dark:hover:bg-linen-100/10 px-6 py-3 text-sm transition-colors">
                    View Services
                </a>
            </div>
        </div>

        {{-- Register of Divisions - tabbed panel, purely client-side
             switching (no server round-trip needed for a static list). --}}
        <div x-data="{ active: 0 }" class="rounded-lg border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 overflow-hidden">
            <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40 px-5 pt-5">Register of Divisions</p>

            <div class="flex flex-wrap gap-1 px-5 pt-3 pb-4 border-b border-ink-900/10 dark:border-linen-100/10">
                @foreach ($divisions as $i => $division)
                    <button
                        type="button"
                        @click="active = {{ $i }}"
                        class="w-8 h-8 rounded-md font-mono text-xs font-semibold transition-colors"
                        :class="active === {{ $i }} ? 'bg-copper-500 text-ink-950' : 'bg-ink-900/5 dark:bg-linen-100/10 text-ink-900/60 dark:text-linen-100/60 hover:bg-ink-900/10'"
                    >
                        {{ $division['code'] }}
                    </button>
                @endforeach
            </div>

            @foreach ($divisions as $i => $division)
                <div x-show="active === {{ $i }}" x-cloak class="p-5 space-y-2">
                    <h3 class="font-display font-semibold text-lg">{{ $division['name'] }}</h3>
                    <p class="text-sm text-ink-700 dark:text-linen-200">{{ $division['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Trust strip --}}
    <section class="border-y border-ink-900/10 dark:border-linen-100/10 bg-linen-100/50 dark:bg-ink-900/30">
        <div
            x-data="{ copied: false, copy() { navigator.clipboard.writeText('{{ $this->companySetting('company.rc_number') }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }"
            class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-wrap items-center gap-x-8 gap-y-2 font-mono text-xs text-ink-700 dark:text-linen-200"
        >
            <button type="button" @click="copy()" class="flex items-center gap-1.5 hover:text-copper-600 dark:hover:text-copper-300">
                <span x-show="!copied">RC {{ $this->companySetting('company.rc_number') }} (click to copy)</span>
                <span x-show="copied" x-cloak class="text-sage-600 dark:text-sage-400">Copied!</span>
            </button>
            @if ($this->companySetting('company.scuml_number'))
                <span>SCUML {{ $this->companySetting('company.scuml_number') }}</span>
            @endif
            @if ($this->companySetting('company.address'))
                <span>{{ $this->companySetting('company.address') }}</span>
            @endif
            @if ($this->companySetting('company.incorporated_at'))
                <span>Incorporated {{ \Illuminate\Support\Carbon::parse($this->companySetting('company.incorporated_at'))->format('F Y') }}</span>
            @endif
        </div>
    </section>

    {{-- Services preview --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="flex items-end justify-between mb-8">
            <h2 class="font-display text-2xl font-semibold">What we do</h2>
            <a href="{{ route('website.services') }}" wire:navigate class="text-sm text-copper-600 dark:text-copper-300 hover:text-copper-700">View all →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->services() as $service)
                <div wire:key="service-{{ $service->id }}" class="rounded-lg border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-5 space-y-2">
                    <span class="font-mono text-[10px] text-copper-600 dark:text-copper-300">{{ $service->code }}</span>
                    <h3 class="font-display font-semibold">{{ $service->name }}</h3>
                    <p class="text-sm text-ink-700 dark:text-linen-200">{{ $service->short_description }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- About preview --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid lg:grid-cols-2 gap-12 items-center">
        <div class="rounded-lg bg-ink-900/5 dark:bg-linen-100/5 aspect-video grid place-items-center">
            <span class="font-mono text-xs text-ink-900/30 dark:text-linen-100/30">Photo coming soon</span>
        </div>
        <div class="space-y-6">
            <h2 class="font-display text-2xl font-semibold">Built by two founders who wanted one number to call.</h2>
            <p class="text-ink-700 dark:text-linen-200 leading-relaxed">
                Anesmavisa Global started as a way to stop juggling different vendors for every kind of trade work - electronics, IT, property, logistics. Today it's a single, registered company covering all of it.
            </p>
            <div class="grid grid-cols-2 gap-4">
                @foreach ($aboutStats as $stat)
                    <div>
                        <p class="font-display text-2xl font-semibold text-copper-600 dark:text-copper-300">{{ $stat['value'] }}</p>
                        <p class="text-xs text-ink-900/50 dark:text-linen-100/50">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Portfolio preview --}}
    @if ($this->portfolioProjects()->isNotEmpty())
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <div class="flex items-end justify-between mb-8">
                <h2 class="font-display text-2xl font-semibold">Recent work</h2>
                <a href="{{ route('website.portfolio') }}" wire:navigate class="text-sm text-copper-600 dark:text-copper-300 hover:text-copper-700">View portfolio →</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($this->portfolioProjects() as $project)
                    <div wire:key="project-{{ $project->id }}" class="rounded-lg border border-ink-900/10 dark:border-linen-100/10 overflow-hidden">
                        @if ($project->image_path)
                            <img src="{{ $project->image_path }}" alt="{{ $project->title }}" class="w-full h-40 object-cover" loading="lazy" />
                        @else
                            <div class="w-full h-40 bg-ink-900/5 dark:bg-linen-100/5"></div>
                        @endif
                        <div class="p-4">
                            <span class="font-mono text-[10px] uppercase text-copper-600 dark:text-copper-300">{{ $project->category->name }}</span>
                            <h3 class="font-display font-semibold text-sm mt-1">{{ $project->title }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Testimonials carousel --}}
    @if ($this->testimonials()->isNotEmpty())
        <section class="bg-linen-100/50 dark:bg-ink-900/30 border-y border-ink-900/10 dark:border-linen-100/10">
            <div
                x-data="{
                    items: @js($this->testimonials()->values()),
                    active: 0,
                    interval: null,
                    init() {
                        this.interval = setInterval(() => {
                            this.active = (this.active + 1) % this.items.length;
                        }, 6000);
                    },
                }"
                class="max-w-3xl mx-auto px-4 sm:px-6 py-16 text-center"
            >
                <template x-for="(testimonial, i) in items" :key="testimonial.id">
                    <div x-show="active === i" x-cloak x-transition.opacity>
                        <p class="font-display text-xl leading-relaxed" x-text="testimonial.quote"></p>
                        <p class="mt-4 font-mono text-xs text-ink-900/50 dark:text-linen-100/50" x-text="testimonial.client_name + ' — ' + testimonial.client_role"></p>
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
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16 text-center space-y-5">
        <h2 class="font-display text-2xl sm:text-3xl font-semibold">Have something you need handled?</h2>
        <p class="text-ink-700 dark:text-linen-200">Tell us what you need and we'll get back to you with a quote, usually within a business day.</p>
        <a href="{{ route('website.quote') }}" wire:navigate class="inline-flex items-center rounded-md bg-copper-500 hover:bg-copper-600 text-ink-950 font-semibold px-8 py-3 text-sm transition-colors">
            Request a Quote
        </a>
    </section>
</div>
