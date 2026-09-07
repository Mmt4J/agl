<?php

use App\Models\PricingCategory;
use App\Models\RepairDeviceType;
use App\Models\RepairIssueType;
use App\Models\RepairPricing;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Pricing')] class extends Component {
    public string $category = '';
    public ?int $deviceTypeId = null;
    public ?int $issueTypeId = null;

    #[Computed]
    public function categories()
    {
        return PricingCategory::query()->with('plans.features')->orderBy('sort_order')->get();
    }

    #[Computed]
    public function selectedCategory()
    {
        return $this->categories->firstWhere('slug', $this->category) ?? $this->categories->first();
    }

    #[Computed]
    public function deviceTypes()
    {
        return RepairDeviceType::query()->orderBy('sort_order')->get();
    }

    #[Computed]
    public function issueTypes()
    {
        return RepairIssueType::query()->orderBy('sort_order')->get();
    }

    #[Computed]
    public function estimate(): ?RepairPricing
    {
        if (!$this->deviceTypeId || !$this->issueTypeId) {
            return null;
        }

        return RepairPricing::query()->where('repair_device_type_id', $this->deviceTypeId)->where('repair_issue_type_id', $this->issueTypeId)->first();
    }

    public function mount(): void
    {
        $this->category = $this->categories->first()?->slug ?? '';
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-14 sm:pt-20 sm:pb-20 text-center">
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Schedule
                C</p>
            <h1 class="font-display font-semibold text-3xl sm:text-4xl lg:text-5xl tracking-tight">Transparent
                starting rates</h1>
            <p class="mt-4 text-ink-900/65 dark:text-linen-100/65 max-w-2xl mx-auto text-base sm:text-lg">Every
                project is scoped individually — these are realistic starting points.</p>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Category tabs --}}
        <div class="flex justify-center gap-2 mb-12 flex-wrap" role="tablist" aria-label="Pricing categories">
            @foreach ($this->categories as $pricingCategory)
                <button type="button" wire:click="$set('category', '{{ $pricingCategory->slug }}')" role="tab"
                    aria-selected="{{ $this->selectedCategory?->id === $pricingCategory->id ? 'true' : 'false' }}"
                    class="px-4 py-2 rounded-md text-sm font-medium border transition-colors {{ $this->selectedCategory?->id === $pricingCategory->id ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500' : 'border-ink-900/20 dark:border-linen-100/20 hover:bg-ink-900/5' }}">
                    {{ $pricingCategory->name }}
                </button>
            @endforeach
        </div>

        {{-- Plans grid --}}
        @if ($this->selectedCategory)
            <div class="grid md:grid-cols-3 gap-6">
                @foreach ($this->selectedCategory->plans as $plan)
                    <div wire:key="pricing-plan-{{ $plan->id }}"
                        class="rounded-md border p-7 flex flex-col {{ $plan->is_highlighted ? 'border-copper-500 border-2 bg-white dark:bg-ink-900/50' : 'border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40' }}">
                        @if ($plan->is_highlighted)
                            <span
                                class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Most
                                requested</span>
                        @endif
                        <h3 class="font-display font-semibold text-xl">{{ $plan->name }}</h3>
                        <p class="text-sm mt-1 text-ink-900/60 dark:text-linen-100/60">{{ $plan->tagline }}</p>
                        <p class="mt-5">
                            <span class="font-display font-semibold text-3xl">{{ $plan->price_label }}</span>
                            <span
                                class="text-sm text-ink-900/50 dark:text-linen-100/50">{{ $plan->period_label }}</span>
                        </p>
                        <ul class="mt-6 space-y-2.5 text-sm flex-1">
                            @foreach ($plan->features as $feature)
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-sage-600 dark:text-sage-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span>{{ $feature->feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('website.quote') }}" wire:navigate
                            class="mt-7 inline-flex justify-center rounded-md font-semibold px-5 py-2.5 text-sm bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Get
                            started</a>
                    </div>
                @endforeach
            </div>
        @endif

        <p class="text-center text-xs text-ink-900/50 dark:text-linen-100/50 mt-8 font-mono">Rates in Nigerian Naira.
            Vary by scope, parts and location.</p>

        {{-- Repair cost estimator --}}
        <div class="mt-12 rounded-md border-2 border-copper-500 bg-linen-100 dark:bg-ink-900/40 p-6 sm:p-8">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">
                Instant Estimator</p>
            <h3 class="font-display font-semibold text-xl mb-1">What might my repair cost?</h3>
            <p class="text-sm text-ink-900/60 dark:text-linen-100/60 mb-6">A rough starting range — your confirmed
                quote comes after a free diagnostic.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="repair-device" class="text-sm font-medium block mb-1.5">Device</label>
                    <select id="repair-device" wire:model.live="deviceTypeId"
                        class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/60 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                        <option value="">Select a device…</option>
                        @foreach ($this->deviceTypes as $deviceType)
                            <option value="{{ $deviceType->id }}">{{ $deviceType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="repair-issue" class="text-sm font-medium block mb-1.5">Issue</label>
                    <select id="repair-issue" wire:model.live="issueTypeId"
                        class="w-full rounded-md border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/60 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                        <option value="">Select an issue…</option>
                        @foreach ($this->issueTypes as $issueType)
                            <option value="{{ $issueType->id }}">{{ $issueType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($deviceTypeId && $issueTypeId)
                <div class="mt-6">
                    @if ($this->estimate)
                        <div
                            class="flex items-center gap-3 rounded-md bg-ink-900 dark:bg-linen-100/10 text-linen-50 px-5 py-4">
                            <svg class="w-5 h-5 text-sage-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-mono text-[10px] uppercase tracking-widest text-copper-300">Estimated
                                    range</p>
                                <p class="font-display font-semibold text-lg">{{ $this->estimate->formatted_range }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p
                            class="text-sm text-ink-900/60 dark:text-linen-100/60 rounded-md border border-ink-900/15 dark:border-linen-100/15 px-5 py-4">
                            This combination isn't common for this device — request a free diagnostic and we'll quote
                            it directly.</p>
                    @endif
                </div>
            @endif

            <p class="mt-5 text-xs text-ink-900/50 dark:text-linen-100/50 font-mono">Illustrative only, not a binding
                quote.
                <a href="{{ route('website.quote') }}" wire:navigate
                    class="underline decoration-copper-500 decoration-2 underline-offset-4 text-ink-900 dark:text-linen-50">Book
                    a diagnostic →</a>
            </p>
        </div>
    </section>
</div>
