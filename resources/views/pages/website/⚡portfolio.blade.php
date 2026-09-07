<?php

use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::website')] #[Title('Portfolio')] class extends Component {
    public ?string $activeCategory = null;

    #[Computed]
    public function categories(): Collection
    {
        return PortfolioCategory::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    #[Computed]
    public function projects(): Collection
    {
        return PortfolioProject::query()->with('category')->when($this->activeCategory, fn($query) => $query->whereHas('category', fn($categoryQuery) => $categoryQuery->where('slug', $this->activeCategory)))->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('created_at')->get();
    }

    public function selectCategory(?string $category): void
    {
        $this->activeCategory = $category;
    }

    public function imageUrl(?string $imagePath): ?string
    {
        return $imagePath ? asset('storage/' . $imagePath) : null;
    }
};
?>

<div class="bg-linen-50 dark:bg-ink-950">

    {{-- Hero --}}
    <section class="bg-linen-100 dark:bg-ink-900/40 border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-14 sm:pt-20 sm:pb-20 text-center">
            <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">Case Files
            </p>
            <h1 class="font-display font-semibold text-3xl sm:text-4xl lg:text-5xl tracking-tight">Selected work on
                record</h1>
            <p class="mt-4 text-ink-900/65 dark:text-linen-100/65 max-w-2xl mx-auto text-base sm:text-lg">A sample of
                recent engagements across websites, software, devices, brands and places.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Category filter --}}
        <div class="flex justify-center flex-wrap gap-2 mb-10">
            <button type="button" wire:click="selectCategory(null)"
                class="px-4 py-2 rounded-md text-sm font-medium border transition-colors {{ $activeCategory === null ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500' : 'border-ink-900/20 dark:border-linen-100/20 hover:bg-ink-900/5' }}">
                All
            </button>
            @foreach ($this->categories as $category)
                <button type="button" wire:key="portfolio-filter-{{ $category->id }}"
                    wire:click="selectCategory('{{ $category->slug }}')"
                    class="px-4 py-2 rounded-md text-sm font-medium border transition-colors {{ $activeCategory === $category->slug ? 'bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 border-ink-900 dark:border-copper-500' : 'border-ink-900/20 dark:border-linen-100/20 hover:bg-ink-900/5' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Grid --}}
        @if ($this->projects->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->projects as $project)
                    <article wire:key="portfolio-project-{{ $project->id }}"
                        class="group rounded-md overflow-hidden border border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40">
                        <div class="overflow-hidden relative">
                            @if ($this->imageUrl($project->image_path))
                                <img src="{{ $this->imageUrl($project->image_path) }}" alt="{{ $project->title }}"
                                    loading="lazy"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div
                                    class="w-full h-48 grid place-items-center bg-ink-900 dark:bg-linen-100 text-linen-50 dark:text-ink-950">
                                    <span
                                        class="font-display text-xl font-semibold px-4 text-center">{{ $project->title }}</span>
                                </div>
                            @endif

                            <span
                                class="absolute top-3 left-3 font-mono text-[10px] uppercase tracking-widest bg-ink-900/80 text-linen-50 px-2.5 py-1 rounded">
                                {{ $project->category->name }}
                            </span>

                            @if ($project->is_featured)
                                <span
                                    class="absolute top-3 right-3 font-mono text-[10px] uppercase tracking-widest bg-copper-500 text-ink-950 px-2.5 py-1 rounded">
                                    Featured
                                </span>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="font-display font-semibold">{{ $project->title }}</h3>
                            <p class="text-sm text-ink-900/60 dark:text-linen-100/60 mt-1">{{ $project->summary }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="text-center text-sm text-ink-900/50 dark:text-linen-100/50 py-16">No entries in this category yet.
            </p>
        @endif
    </section>

    {{-- CTA --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20">
        <div class="rounded-md border-2 border-copper-500 px-6 py-12 text-center sm:px-14 sm:py-16">
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300">Open a new
                entry</p>
            <h2 class="mx-auto mt-3 max-w-2xl font-display text-2xl font-semibold sm:text-4xl">Have a project that
                belongs here?</h2>
            <p class="mx-auto mt-3 max-w-xl text-ink-900/60 dark:text-linen-100/60">Bring us the brief, the constraint
                or the rough idea. We will help turn it into useful work.</p>
            <a href="{{ route('website.quote') }}" wire:navigate
                class="mt-8 inline-block rounded-md bg-ink-900 px-8 py-3.5 font-semibold text-linen-50 transition-colors hover:bg-ink-800 dark:bg-copper-500 dark:text-ink-950 dark:hover:bg-copper-600">Start
                a project</a>
        </div>
    </section>
</div>
