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
    <section class="relative overflow-hidden border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="absolute -right-24 -top-24 size-96 rounded-full text-ink-900/[0.05] dark:text-linen-100/[0.04] seal-ring"
            aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="grid lg:grid-cols-12 gap-10 items-end">
                <div class="lg:col-span-8">
                    <p class="font-mono text-xs uppercase tracking-[0.2em] text-copper-600 dark:text-copper-300 mb-5">
                        Schedule C · Pricing register</p>
                    <h1
                        class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.05] tracking-tight">
                        Clear starting rates.<br><span class="text-copper-500 dark:text-copper-300">No mystery
                            charges.</span></h1>
                    <p
                        class="mt-6 max-w-2xl text-base sm:text-lg text-ink-900/70 dark:text-linen-100/70 leading-relaxed">
                        Every project is scoped individually. These are realistic starting points for the services most
                        often entered in our register.</p>
                </div>
                <div class="lg:col-span-4 lg:border-l lg:border-ink-900/15 dark:lg:border-linen-100/15 lg:pl-8">
                    <p
                        class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50 mb-3">
                        Rate card status</p>
                    <p class="font-display text-2xl font-semibold">Active schedule</p>
                    <p class="mt-2 text-sm text-ink-900/60 dark:text-linen-100/60">Rates in Nigerian Naira. Final quotes
                        follow scope, parts, and location.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-ink-900/10 dark:border-linen-100/10 bg-linen-100 dark:bg-ink-900/40">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap items-center gap-x-8 gap-y-2 text-xs font-mono text-ink-900/60 dark:text-linen-100/60">
            <span class="text-copper-600 dark:text-copper-300">ENTRY 03 / RATE CARD</span><span
                class="hidden sm:inline text-ink-900/20 dark:text-linen-100/20">/</span><span>Scope first</span><span
                class="hidden sm:inline text-ink-900/20 dark:text-linen-100/20">/</span><span>Quote before work
                begins</span>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Choose
                    a schedule</p>
                <h2 class="font-display font-semibold text-3xl sm:text-4xl">What are you pricing?</h2>
            </div>
            <span
                class="font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40">{{ $this->categories->count() }}
                schedules on file</span>
        </div>

        <div class="flex flex-wrap gap-2 border-b border-ink-900/10 dark:border-linen-100/10 pb-5 mb-10" role="tablist"
            aria-label="Pricing categories">
            @foreach ($this->categories as $pricingCategory)
                <button type="button" wire:click="$set('category', '{{ $pricingCategory->slug }}')" role="tab"
                    aria-selected="{{ $this->selectedCategory?->id === $pricingCategory->id ? 'true' : 'false' }}"
                    class="px-4 py-2.5 text-sm font-medium border transition-colors {{ $this->selectedCategory?->id === $pricingCategory->id ? 'bg-ink-900 text-linen-50 dark:bg-copper-500 dark:text-ink-950 border-ink-900 dark:border-copper-500' : 'border-ink-900/20 dark:border-linen-100/20 hover:border-copper-500' }}">{{ $pricingCategory->name }}</button>
            @endforeach
        </div>

        @if ($this->selectedCategory)
            <div class="grid md:grid-cols-3 gap-6">
                @foreach ($this->selectedCategory->plans as $plan)
                    <article wire:key="pricing-plan-{{ $plan->id }}"
                        class="relative flex flex-col border p-7 {{ $plan->is_highlighted ? 'border-copper-500 border-2 bg-white dark:bg-ink-900/50' : 'border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40' }}">
                        @if ($plan->is_highlighted)
                            <span
                                class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Most
                                requested</span>
                        @endif
                        <p class="font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40">
                            {{ $this->selectedCategory->slug === 'electronics-repair' ? 'C.' : 'A.' }}{{ $loop->iteration }}
                        </p>
                        <h3 class="font-display font-semibold text-2xl mt-2">{{ $plan->name }}</h3>
                        <p class="text-sm mt-2 text-ink-900/60 dark:text-linen-100/60">{{ $plan->tagline }}</p>
                        <p class="mt-6"><span
                                class="font-display font-semibold text-3xl">{{ $plan->price_label }}</span> <span
                                class="text-xs text-ink-900/50 dark:text-linen-100/50">{{ $plan->period_label }}</span>
                        </p>
                        <ul
                            class="mt-7 space-y-3 text-sm flex-1 border-t border-ink-900/10 dark:border-linen-100/10 pt-5">
                            @foreach ($plan->features as $feature)
                                <li class="flex items-start gap-2"><span class="text-sage-600 dark:text-sage-500"
                                        aria-hidden="true">✓</span><span>{{ $feature->feature }}</span></li>
                            @endforeach
                        </ul>
                        <a href="{{ route('website.quote') }}" wire:navigate
                            class="mt-8 inline-flex justify-center rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-5 py-3 text-sm hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Get
                            a confirmed quote -></a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="border-y border-ink-900/10 dark:border-linen-100/10 bg-linen-100 dark:bg-ink-900/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-2xl mb-8">
                <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Repair
                    schedule · C.04</p>
                <h2 class="font-display font-semibold text-3xl sm:text-4xl">What might my repair cost?</h2>
                <p class="mt-3 text-sm text-ink-900/65 dark:text-linen-100/65">A rough starting range from our current
                    repair matrix. Your confirmed quote follows a free diagnostic.</p>
            </div>
            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7 grid sm:grid-cols-2 gap-5">
                    <div><label for="repair-device"
                            class="font-mono text-[10px] uppercase tracking-widest block mb-2">Device</label><select
                            id="repair-device" wire:model.live="deviceTypeId"
                            class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/60 px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            <option value="">Select a device...</option>
                            @foreach ($this->deviceTypes as $deviceType)
                                <option value="{{ $deviceType->id }}">{{ $deviceType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="repair-issue"
                            class="font-mono text-[10px] uppercase tracking-widest block mb-2">Issue</label><select
                            id="repair-issue" wire:model.live="issueTypeId"
                            class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/60 px-3.5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
                            <option value="">Select an issue...</option>
                            @foreach ($this->issueTypes as $issueType)
                                <option value="{{ $issueType->id }}">{{ $issueType->name }}</option>
                            @endforeach
                        </select></div>
                </div>
                <div class="lg:col-span-5">
                    @if ($this->estimate)
                        <div class="bg-ink-900 dark:bg-linen-100/10 text-linen-50 p-6">
                            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-300">Estimated range
                            </p>
                            <p class="font-display font-semibold text-2xl mt-2">{{ $this->estimate->formatted_range }}
                            </p>
                            <p class="text-xs text-linen-100/60 mt-2">Illustrative only, not a binding quote.</p>
                        </div>
                    @elseif ($deviceTypeId && $issueTypeId)
                        <div
                            class="border border-ink-900/15 dark:border-linen-100/15 p-6 text-sm text-ink-900/60 dark:text-linen-100/60">
                            This combination is not currently listed. Request a free diagnostic and we will quote it
                            directly.</div>
                    @else
                        <div
                            class="border border-dashed border-ink-900/20 dark:border-linen-100/20 p-6 text-sm text-ink-900/50 dark:text-linen-100/50">
                            Select a device and issue to open the estimate.</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Open a
                new entry</p>
            <h2 class="font-display font-semibold text-3xl sm:text-4xl max-w-2xl mx-auto">Need something outside the
                schedule?</h2>
            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">Tell us what you need. We will scope
                it, price it, and put the details in writing.</p><a href="{{ route('website.quote') }}" wire:navigate
                class="mt-8 inline-flex rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Request
                a quote -></a>
        </div>
    </section>
</div>
