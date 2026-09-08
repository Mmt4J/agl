<?php

use App\Models\Service;
use App\Livewire\Concerns\HasNumberInWords;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Services')] class extends Component {
    use HasNumberInWords;

    #[Computed]
    public function services(): Collection
    {
        return Service::featured()->with('features')->ordered()->get();
    }

    public function getServiceIconSvg($code)
    {
        // Map service codes to inline SVG strings (use `currentColor` for dynamic coloring)
        $icons = [
            'device-phone' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="7" y="3" width="10" height="18" rx="2"/><path stroke-linecap="round" d="M11 18h2"/></svg>',
            'code-bracket' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L21 10.5l-3.75 3.75M6.75 6.75L3 10.5l3.75 3.75M14 4l-4 16"/></svg>',
            'globe-alt' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M3 12h18M12 3a15 15 0 010 18 15 15 0 010-18z"/></svg>',
            'home-modern' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6"/></svg>',
            'academic-cap' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m-9-7v5l9 3 9-3v-5"/></svg>',
            'branding' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>',
            'presentation-chart' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5h8m-8 3h5M21 12a9 9 0 11-9-9 9 9 0 019 9z"/></svg>',
            'tech-training' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m-9-7v5l9 3 9-3v-5"/></svg>',
            'paint-brush' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21l5-1 9-9-4-4-9 9-1 5zm10-14l3 3"/></svg>',
            'scissors' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3l6 6 6-6M6 3v6l6 6m0 0l6-6V3m-6 12v6"/></svg>',
            'Property sourcing, sales and development advisory.' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6"/></svg>',

            // Add more mappings as needed
        ];

        // Fallback icon (a simple cube)
        $fallback = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>';

        return $icons[$code] ?? $fallback;
    }

    public function getDivisionCountInWordsProperty()
    {
        return $this->numberInWords(count($this->services()));
    }
}; ?>

<div>
    {{-- ================================================================
         SERVICES HERO
         ================================================================ --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-14 sm:pt-20 sm:pb-20 text-center">
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Schedule A
            </p>
            <h1 class="font-display font-semibold text-3xl sm:text-4xl lg:text-5xl tracking-tight">
                {{ ucfirst($this->divisionCountInWords) }} divisions, one point of contact
            </h1>

            <p class="mt-4 text-ink-900/65 dark:text-linen-100/65 max-w-2xl mx-auto text-base sm:text-lg">
                Select an entry to see what's included.
                Every job is scoped and quoted before we start.
            </p>
        </div>
    </section>


    {{-- ================================================================
         SERVICES REGISTER
         ================================================================ --}}
    @island(name: 'services-register')
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data="{ open: 0 }">
            <div class="grid lg:grid-cols-3 gap-8">

                {{-- ====================================================
                     LEFT: SERVICE INDEX
                     ==================================================== --}}
                <div
                    class="lg:col-span-1 divide-y divide-ink-900/10 dark:divide-linen-100/10 border-y border-ink-900/10 dark:border-linen-100/10">
                    @forelse ($this->services as $index => $service)
                        <button type="button" wire:key="service-selector-{{ $service->id }}"
                            @click="open = {{ $index }}"
                            class="w-full text-left
                                   flex items-center gap-3
                                   px-4 py-4
                                   border-l-4
                                   transition-colors"
                            :class="open === {{ $index }} ?
                                'border-copper-500 bg-ink-900/[0.03] dark:bg-linen-100/[0.05]' :
                                'border-transparent hover:bg-ink-900/[0.02]'">
                            {{-- Service Code --}}
                            <span class="font-mono text-xs text-copper-600 dark:text-copper-300 w-8 shrink-0">
                                {{ $service->code }}
                            </span>

                            {{-- Service Name --}}
                            <span class="text-sm font-medium flex-1">
                                {{ $service->name }}
                            </span>

                            {{-- Arrow --}}
                            <svg class="w-4 h-4 shrink-0 text-ink-900/30 dark:text-linen-100/30 transition-transform"
                                :class="open === {{ $index }} ?
                                    'rotate-90 text-copper-500' :
                                    ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>

                    @empty
                        <div class="px-4 py-8">
                            <p class="text-sm text-ink-900/50 dark:text-linen-100/50">
                                No services are currently available.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- ====================================================
                     RIGHT: SERVICE DETAIL
                     ==================================================== --}}
                <div class="lg:col-span-2">
                    @foreach ($this->services as $index => $service)
                        <article wire:key="service-panel-{{ $service->id }}" x-show="open === {{ $index }}" x-cloak
                            x-transition.opacity.duration.200ms
                            class="rounded-md border border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40 p-6 sm:p-8">
                            {{-- Top row --}}
                            <div class="flex items-center justify-between mb-5">
                                {{-- Icon --}}
                                <div
                                    class="w-12 h-12 rounded-md bg-ink-900/5 dark:bg-linen-100/5 text-ink-900 dark:text-copper-300 grid place-items-center">
                                    @if ($service->icon)
                                        {!! $this->getServiceIconSvg($service->icon) !!}
                                    @else
                                        {{-- Fallback icon --}}
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    @endif
                                </div>

                                {{-- Entry number --}}
                                <span class="font-mono text-xs text-ink-900/40 dark:text-linen-100/40">
                                    Entry {{ $service->code }}
                                </span>
                            </div>

                            {{-- Service title --}}
                            <h2 class="font-display font-semibold text-2xl sm:text-3xl">
                                {{ $service->name }}
                            </h2>

                            {{-- Description --}}
                            <p class="text-ink-900/70 dark:text-linen-100/70 mt-3 leading-relaxed">
                                {{ $service->description }}
                            </p>

                            {{-- Features --}}
                            @if ($service->features->isNotEmpty())
                                <ul class="mt-6 grid sm:grid-cols-2 gap-3">

                                    @foreach ($service->features as $feature)
                                        <li wire:key="service-feature-{{ $feature->id }}"
                                            class="flex items-start gap-2 text-sm">
                                            <svg class="w-4 h-4 mt-0.5 shrink-0 text-sage-600 dark:text-sage-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>

                                            <span>
                                                {{ $feature->feature }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ route('website.quote', ['service' => $service->slug]) }}" wire:navigate
                                    class="rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-5 py-2.5 text-sm hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">
                                    Request this service
                                </a>

                                <a href="{{ route('website.pricing') }}" wire:navigate
                                    class="rounded-md border border-ink-900/20 dark:border-linen-100/20 font-semibold px-5 py-2.5 text-sm hover:bg-ink-900/5 dark:hover:bg-linen-100/5 transition-colors">
                                    See pricing
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endisland


    {{-- ================================================================
         BOTTOM LINK
         ================================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 text-center">
        <a href="{{ route('website.industries') }}" wire:navigate
            class="text-ink-900
                   dark:text-linen-50
                   font-semibold
                   text-sm
                   underline
                   decoration-copper-500
                   decoration-2
                   underline-offset-4">
            See devices &amp; industries we support →
        </a>
    </div>

    {{-- ================================================================
         CTA
         ================================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="rounded-md border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">
                Open a new entry
            </p>

            <h2 class="font-display font-semibold text-2xl sm:text-4xl max-w-2xl mx-auto">
                Not sure which service you need?
            </h2>

            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">
                Tell us what you need and we'll route your request
                to the right division.
            </p>

            <a href="{{ route('website.quote') }}" wire:navigate
                class="inline-block mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">
                Request a Quote
            </a>
        </div>
    </section>

</div>
