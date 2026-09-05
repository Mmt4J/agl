<?php

use App\Models\Device;
use App\Models\Industry;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Industries')] class extends Component
{
    #[Computed]
    public function industries(): Collection
    {
        return Industry::orderBy('sort_order')->get();
    }

    #[Computed]
    public function devices(): Collection
    {
        return Device::orderBy('sort_order')->get();
    }
}; ?>

<div>
    <section class="border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32 grid lg:grid-cols-10 gap-12 lg:gap-20 items-center">
            <div class="lg:col-span-7">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-copper-600 dark:text-copper-300 mb-4">Field notes · Industries</p>
                <h1 class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.02]">Technology should fit the work already happening.</h1>
                <p class="mt-6 text-lg text-ink-900/70 dark:text-linen-100/70 max-w-2xl leading-relaxed">We work with the people behind the counter, the classroom, the property file and the production floor. That context is where useful solutions begin.</p>
            </div>
            <div class="lg:col-span-3 border-l border-ink-900/15 dark:border-linen-100/15 pl-8">
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50">Our rule</p>
                <p class="font-display text-2xl mt-3">Listen first. Build only what earns its place.</p>
            </div>
        </div>
    </section>

    @island(name: 'industry-register')
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div><p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">Field register</p><h2 class="font-display font-semibold text-3xl sm:text-4xl">Who we build for</h2></div>
                <span class="self-start sm:self-auto font-mono text-xs text-ink-900/45 dark:text-linen-100/45">{{ $this->industries->count() }} sectors</span>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($this->industries as $index => $industry)
                    <article wire:key="industry-{{ $industry->id }}" class="relative border border-ink-900/15 dark:border-linen-100/15 p-6 min-h-48 hover:border-copper-500/70 transition-colors"><span class="font-mono text-xs text-copper-600 dark:text-copper-300">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><h3 class="font-display font-semibold text-2xl mt-8">{{ $industry->name }}</h3><p class="text-sm text-ink-900/60 dark:text-linen-100/60 mt-2 leading-relaxed">{{ $industry->description }}</p></article>
                @empty
                    <p class="text-ink-900/50 dark:text-linen-100/50">Our industry register is being updated.</p>
                @endforelse
            </div>
        </section>

        <section class="bg-ink-900 text-linen-50 border-y border-ink-900/10 dark:border-linen-100/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10"><div><p class="font-mono text-xs uppercase tracking-widest text-copper-300 mb-2">Serviceable equipment</p><h2 class="font-display font-semibold text-3xl">What we keep running</h2></div><a href="{{ route('website.quote') }}" wire:navigate class="font-semibold text-sm underline decoration-copper-500 decoration-2 underline-offset-4">Ask for a diagnosis →</a></div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-8">
                    @forelse ($this->devices as $device)
                        <div wire:key="device-{{ $device->id }}" class="border-t border-linen-100/20 pt-4"><p class="font-display text-xl">{{ $device->name }}</p><p class="text-sm text-linen-100/60 mt-2 leading-relaxed">{{ $device->examples }}</p></div>
                    @empty
                        <p class="text-linen-100/60">Device records are being updated.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endisland

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="rounded-md border-2 border-copper-500 px-6 sm:px-14 py-12 sm:py-16 text-center">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Open a new entry</p>
            <h2 class="font-display font-semibold text-2xl sm:text-4xl max-w-2xl mx-auto">Tell us how the work actually happens.</h2>
            <p class="mt-3 text-ink-900/60 dark:text-linen-100/60 max-w-xl mx-auto">One form, routed to the right division. We reply within one business day.</p>
            <a href="{{ route('website.quote') }}" wire:navigate class="inline-block mt-8 rounded-md bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 font-semibold px-8 py-3.5 hover:bg-ink-800 dark:hover:bg-copper-600 transition-colors">Request a Quote</a>
        </div>
    </section>
</div>
