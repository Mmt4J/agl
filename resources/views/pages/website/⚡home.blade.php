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

new #[Layout('layouts::website')] #[Title('Home')] class extends Component {
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

    public function getServiceIconSvg($code)
    {
        // Map service codes to inline SVG strings (use `currentColor` for dynamic coloring)
        $icons = [
            'Electronics Repairs & Sales' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="7" y="3" width="10" height="18" rx="2"/><path stroke-linecap="round" d="M11 18h2"/></svg>',
            'Software Development' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L21 10.5l-3.75 3.75M6.75 6.75L3 10.5l3.75 3.75M14 4l-4 16"/></svg>',
            'Web & Mobile Development' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M3 12h18M12 3a15 15 0 010 18 15 15 0 010-18z"/></svg>',
            'Real Estate' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18m-9-9v18"/></svg>',
            'ICT Training' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18m-9-9v18"/></svg>',
            'Branding & Design' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>',
            'Tech Consulting' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5h8m-8 3h5M21 12a9 9 0 11-9-9 9 9 0 019 9z"/></svg>',
            'Tech Training & ICT' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m-9-7v5l9 3 9-3v-5"/></svg>',
            'Digital Branding & Graphic Design' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21l5-1 9-9-4-4-9 9-1 5zm10-14l3 3"/></svg>',
            'Fashion Design' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3l6 6 6-6M6 3v6l6 6m0 0l6-6V3m-6 12v6"/></svg>',
            'Property sourcing, sales and development advisory.' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6"/></svg>',

            // Add more mappings as needed
        ];

        // Fallback icon (a simple cube)
        $fallback = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>';

        return $icons[$code] ?? $fallback;
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

    public function companySettings(): array
    {
        $settings = Setting::whereIn('key', ['company.rc_number', 'company.scuml_number', 'company.address'])->pluck('value', 'key');

        return [
            'rc_number' => $settings['company.rc_number'] ?? null,
            'scuml_number' => $settings['company.scuml_number'] ?? null,
            'address' => $settings['company.address'] ?? null,
        ];
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full text-ink-900/[0.05] dark:text-linen-100/[0.04] seal-ring"
            aria-hidden="true"></div>
        <div
            class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-16 sm:pt-20 sm:pb-24 grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-6">
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-copper-600 dark:text-copper-300 mb-4">
                    Entry No. {{ $this->companySettings()['rc_number'] }} · Registered 15 Mar 2026
                </p>
                <h1
                    class="font-display font-semibold text-4xl sm:text-5xl lg:text-[3.4rem] leading-[1.08] tracking-tight text-ink-900 dark:text-linen-50">
                    Seven trades.<br> One registered company.<br> <span
                        class="text-copper-500 dark:text-copper-300">Every job on the record.</span>
                </h1>
                <p class="mt-6 text-ink-900/70 dark:text-linen-100/70 text-base sm:text-lg max-w-lg leading-relaxed">
                    ANESMAVISA GLOBAL LTD keeps electronics, software, property, training and design work under one
                    CAC-registered, SCUML-compliant name - so nothing you commission is off the books.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('website.quote') }}" wire:navigate
                        class="rounded-md bg-ink-900 dark:bg-copper-500 hover:bg-ink-800 dark:hover:bg-copper-600 text-linen-50 dark:text-ink-950 font-semibold px-6 py-3.5 text-sm sm:text-base transition-colors">Book
                        a Service</a>
                    <a href="{{ route('website.services') }}" wire:navigate
                        class="rounded-md border border-ink-900/25 dark:border-linen-100/25 hover:border-ink-900/60 dark:hover:border-linen-100/60 font-semibold px-6 py-3.5 text-sm sm:text-base transition-colors">Open
                        the Services File</a>
                </div>
                <dl
                    class="mt-12 grid grid-cols-3 border-t border-ink-900/15 dark:border-linen-100/15 divide-x divide-ink-900/15 dark:divide-linen-100/15 max-w-md">
                    <div class="pt-4 pr-4">
                        <dt
                            class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">
                            Divisions</dt>
                        <dd class="font-display font-semibold text-2xl mt-1">{{ count($divisions) }}</dd>
                    </div>
                    <div class="pt-4 px-4">
                        <dt
                            class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">
                            Status</dt>
                        <dd class="font-display font-semibold text-2xl mt-1 text-sage-600 dark:text-sage-500">Active
                        </dd>
                    </div>
                    <div class="pt-4 pl-4">
                        <dt
                            class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">
                            SCUML</dt>
                        <dd class="font-display font-semibold text-2xl mt-1">Yes</dd>
                    </div>
                </dl>
            </div>

            {{-- Register of Divisions - tabbed panel, purely client-side
             switching (no server round-trip needed for a static list). --}}
            <div x-data="{ active: 0 }"
                class="lg:col-span-6 rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white/60 dark:bg-linen-100/[0.04] overflow-hidden">
                <p
                    class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40 px-5 pt-5">
                    Register of Divisions</p>

                <div role="tablist" aria-label="Business divisions">
                    @foreach ($divisions as $i => $division)
                        <button type="button" @click="active = {{ $i }}" role="tab"
                            :aria-pressed="active === {{ $i }}"
                            class="entry-row w-full flex items-center gap-4 text-left px-5 py-3.5 border-l-4 border-transparent border-t border-ink-900/10 dark:border-linen-100/10 first:border-t-0 hover:bg-ink-900/[0.03] dark:hover:bg-linen-100/[0.05] transition-colors">
                            <span
                                class="font-mono text-xs text-copper-600 dark:text-copper-300 w-6 shrink-0">{{ $division['code'] }}</span>
                            <span class="text-sm font-medium flex-1">{{ $division['name'] }}</span>
                            <svg class="w-4 h-4 shrink-0 text-ink-900/30 dark:text-linen-100/30"
                                :class="active === {{ $i }} ? 'rotate-90 text-copper-500' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    @endforeach
                </div>

                @foreach ($divisions as $i => $division)
                    <div x-show="active === {{ $i }}" x-cloak
                        class="p-6 bg-ink-900 dark:bg-linen-100/[0.06] text-linen-50 min-h-[150px]">
                        <div class="flex items-center gap-2 mb-3"><span class="text-sage-500">✓</span><span
                                class="font-mono text-[10px] uppercase tracking-widest text-sage-500">Verified
                                division</span></div>
                        <h3 class="font-display font-semibold text-lg">{{ $division['name'] }}</h3>
                        <p class="text-linen-100/75 text-sm mt-2 leading-relaxed">{{ $division['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trust strip --}}
    <section x-data="{
        async copyText(text, label) {
            try {
                await navigator.clipboard.writeText(text);

                $dispatch('toast', {
                    message: `${label} copied successfully`
                });
            } catch (error) {
                console.error('Copy failed:', error);

                $dispatch('toast', {
                    message: `Unable to copy ${label}`
                });
            }
        }
    }"
        class="border-b border-ink-900/10 dark:border-linen-100/10 bg-linen-100 dark:bg-ink-900/40">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5
                text-xs sm:text-sm font-mono
                text-ink-900/60 dark:text-linen-100/60">
            <div class="flex flex-wrap items-center justify-center gap-y-3 sm:gap-y-2">

                {{-- CAC --}}
                <button type="button" @click="copyText(@js($this->companySettings()['rc_number']), 'RC number')"
                    class="group inline-flex items-center justify-center gap-1.5
                        rounded-md px-2 py-1
                        hover:text-copper-600 dark:hover:text-copper-300
                        hover:bg-ink-900/5 dark:hover:bg-linen-100/5
                        focus:outline-none focus-visible:ring-2
                        focus-visible:ring-copper-500/50
                        transition-colors"
                    title="Copy RC number">
                    <span>
                        CAC · RC {{ $this->companySettings()['rc_number'] }}
                    </span>

                    <svg class="w-3.5 h-3.5 shrink-0 opacity-60 group-hover:opacity-100 transition-opacity"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                    </svg>
                </button>

                {{-- Desktop separator --}}
                <span class="hidden sm:inline mx-3 text-ink-900/20 dark:text-linen-100/20" aria-hidden="true">/</span>

                {{-- SCUML --}}
                <button type="button" @click="copyText(@js($this->companySettings()['scuml_number']), 'SCUML number')"
                    class="group inline-flex items-center justify-center gap-1.5
                        rounded-md px-2 py-1
                        hover:text-copper-600 dark:hover:text-copper-300
                        hover:bg-ink-900/5 dark:hover:bg-linen-100/5
                        focus:outline-none focus-visible:ring-2
                        focus-visible:ring-copper-500/50
                        transition-colors"
                    title="Copy SCUML number">
                    <span>
                        SCUML · RN {{ $this->companySettings()['scuml_number'] }}
                    </span>

                    <svg class="w-3.5 h-3.5 shrink-0 opacity-60 group-hover:opacity-100 transition-opacity"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375a1.125 1.125 0 00-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                    </svg>
                </button>

                {{-- Desktop separator --}}
                <span class="hidden sm:inline mx-3 text-ink-900/20 dark:text-linen-100/20" aria-hidden="true">/</span>

                {{-- Location Improved with aplinejs --}}
                <div x-data="{ showFull: false }"
                    class="relative inline-flex items-center justify-center px-2 py-1 text-center">
                    <span @mouseenter="showFull = true" @mouseleave="showFull = false">
                        @php
                            $fullAddress = $this->companySettings()['address'] ?: 'Osogbo, Osun State';
                            $wordLimit = 4;
                            $words = str_word_count($fullAddress, 1);
                            $truncated =
                                count($words) > $wordLimit
                                    ? implode(' ', array_slice($words, 0, $wordLimit)) . '…'
                                    : $fullAddress;
                        @endphp
                        {{ $truncated }}
                    </span>

                    <!-- Tooltip / Popover -->
                    <div x-show="showFull" x-transition
                        class="absolute top-full left-0 w-full mt-1 bg-gray-800 text-white text-sm rounded px-3 py-1 whitespace-normal sm:whitespace-nowrap sm:left-1/2 sm:-translate-x-1/2 sm:w-auto z-10"
                        style="display: none;">
                        {{ $fullAddress }}
                    </div>
                </div>

                {{-- Desktop separator --}}
                <span class="hidden sm:inline mx-3 text-ink-900/20 dark:text-linen-100/20" aria-hidden="true">/</span>

                {{-- Incorporated date --}}
                <span class="inline-flex items-center justify-center px-2 py-1 text-center">
                    Incorporated 15 Mar 2026
                </span>
            </div>
        </div>
    </section>

    {{-- Services preview --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">
                    Schedule A - Services</p>
                <h2 class="font-display font-semibold text-3xl sm:text-4xl">What's entered under our name</h2>
            </div>
            <a href="{{ route('website.services') }}" wire:navigate
                class="self-start sm:self-auto font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">Open
                full schedule →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($this->services as $service)
                <article wire:key="service-{{ $service->id }}"
                    class="group rounded-md border border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40 p-6 hover:border-copper-500/60 transition-colors">
                    <div class="flex items-start justify-between mb-4">
                        <span
                            class="w-10 h-10 rounded-md bg-ink-900/5 dark:bg-linen-100/5 text-ink-900 dark:text-copper-300 grid place-items-center group-hover:bg-copper-500 group-hover:text-linen-50 transition-colors">
                            {!! $this->getServiceIconSvg($service->name) !!}
                        </span>
                        <span class="font-mono text-[10px] text-ink-900/30 dark:text-linen-100/30">
                            {{ $service->code }}
                        </span>
                    </div>
                    <h3 class="font-display font-semibold text-base">{{ $service->name }}</h3>
                    <p class="text-sm text-ink-900/60 dark:text-linen-100/60 mt-2 leading-relaxed">
                        {{ $service->short_description }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- About preview --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-y border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">
                    Certificate of record</p>
                <h2 class="font-display font-semibold text-3xl sm:text-4xl mb-5">A registered partner, not a side
                    hustle</h2>
                <p class="text-ink-900/70 dark:text-linen-100/70 leading-relaxed mb-6">Founded by Matthew Ayodele Alabi
                    and Samuel Gift Alabi, ANESMAVISA GLOBAL LTD was incorporated in March 2026 as a private company
                    limited by shares, and registered with SCUML two months later. We consolidate services individuals
                    and small businesses usually source from six unregistered vendors.</p>
                <dl class="grid grid-cols-2 gap-5">
                    <div class="border-l-2 border-copper-500 pl-3">
                        <dt class="font-semibold text-sm">CAC Registered</dt>
                        <dd class="text-xs text-ink-900/60 dark:text-linen-100/60 mt-0.5">RC
                            {{ $this->companySettings()['rc_number'] }}, incorporated March 2026.</dd>
                    </div>
                    <div class="border-l-2 border-copper-500 pl-3">
                        <dt class="font-semibold text-sm">SCUML Compliant</dt>
                        <dd class="text-xs text-ink-900/60 dark:text-linen-100/60 mt-0.5">RN
                            {{ $this->companySettings()['scuml_number'] }}, anti-money-laundering registered.</dd>
                    </div>
                </dl>
                <a href="{{ route('website.about') }}" wire:navigate
                    class="inline-block mt-8 font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">Read
                    the full record →</a>
            </div>
            <img src="https://res.cloudinary.com/kapposoft-technologies/image/upload/w_1000,ar_16:9,c_fill,g_auto,e_sharpen/v1788609915/ANESMAVISA%20Tech/AGL/AGL5_uy2z5q.png"
                alt="ANESMAVISA GLOBAL LTD team" class="order-1 lg:order-2 rounded-md w-full h-full object-cover"
                loading="lazy">
        </div>
    </section>


    {{-- Portfolio preview --}}
    @if ($this->portfolioProjects->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">
                        Case file</p>
                    <h2 class="font-display font-semibold text-3xl sm:text-4xl">Work on record</h2>
                </div>
                <a href="{{ route('website.portfolio') }}" wire:navigate
                    class="font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">View
                    full portfolio →</a>
            </div>
            <div class="grid sm:grid-cols-3 gap-6">
                @foreach ($this->portfolioProjects as $project)
                    <article wire:key="project-{{ $project->id }}"
                        class="rounded-md border border-ink-900/12 dark:border-linen-100/12 overflow-hidden bg-white dark:bg-ink-900/40">
                        @if ($project->image_path)
                            <img src="{{ $project->image_path }}" alt="{{ $project->title }}"
                                class="w-full h-40 object-cover" loading="lazy" />
                        @else
                            <div class="w-full h-44 bg-ink-900/5 dark:bg-linen-100/5"></div>
                        @endif
                        <div class="p-5">
                            <span
                                class="font-mono text-[10px] uppercase text-copper-600 dark:text-copper-300">{{ $project->category->name }}</span>
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
            <div x-data="{
                items: @js($this->testimonials->values()),
                active: 0,
                interval: null,
                init() {
                    this.interval = setInterval(() => {
                        this.active = (this.active + 1) % this.items.length;
                    }, 6000);
                },
                destroy() {
                    clearInterval(this.interval);
                },
            }" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
                <p class="font-mono text-xs uppercase tracking-widest text-copper-300 mb-6">Statements on file</p>
                <template x-for="(testimonial, i) in items" :key="testimonial.id">
                    <div x-show="active === i" x-cloak x-transition.opacity>
                        <p class="font-display italic text-xl sm:text-2xl leading-relaxed">&ldquo;<span
                                x-text="testimonial.quote"></span>&rdquo;</p>
                        <p class="mt-6 font-semibold text-copper-300 font-mono text-sm"
                            x-text="testimonial.client_name"></p>
                        <p class="text-sm text-linen-100/60" x-text="testimonial.client_role"></p>
                    </div>
                </template>

                <div class="flex justify-center gap-1.5 mt-6">
                    <template x-for="(testimonial, i) in items" :key="'dot-' + testimonial.id">
                        <button @click="active = i" class="w-1.5 h-1.5 rounded-full"
                            :class="active === i ? 'bg-copper-500' : 'bg-ink-900/20 dark:bg-linen-100/20'"
                            :aria-label="'Show testimonial ' + (i + 1)"></button>
                    </template>
                </div>
            </div>
        </section>
    @endif

    {{-- CTA banner --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="rounded-md border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Open a
                new entry</p>
            <h2 class="font-display font-semibold text-2xl sm:text-4xl max-w-2xl mx-auto">Tell us what needs fixing,
                building, training, selling, or designing.</h2>
            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">One form, routed to the right
                division. We reply within one business day.</p>
            <a href="{{ route('website.quote') }}" wire:navigate
                class="inline-block mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Request
                a Quote</a>
        </div>
    </section>

    <x-ui.toast />
</div>
