<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::website')] #[Title('The Journal')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $category = 'all';
    public string $newsletterEmail = '';
    public bool $subscribed = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return BlogCategory::query()
            ->withCount(['posts' => fn($query) => $query->published()])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function featured(): ?BlogPost
    {
        return BlogPost::published()
            ->with(['category', 'author'])
            ->featured()
            ->latest('published_at')
            ->first() ??
            BlogPost::published()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->first();
    }

    #[Computed]
    public function posts()
    {
        return BlogPost::published()
            ->with(['category', 'author', 'tags'])
            ->when($this->category !== 'all', fn($query) => $query->whereHas('category', fn($category) => $category->where('slug', $this->category)))
            ->when($this->search !== '', fn($query) => $query->where(fn($search) => $search->where('title', 'like', "%{$this->search}%")->orWhere('excerpt', 'like', "%{$this->search}%")))
            ->latest('published_at')
            ->paginate(6);
    }

    #[Computed]
    public function popular()
    {
        return BlogPost::published()
            ->with(['category', 'author'])
            ->orderByDesc('view_count')
            ->latest('published_at')
            ->take(3)
            ->get();
    }

    #[Computed]
    public function tags()
    {
        return \App\Models\Tag::query()->whereHas('posts', fn($query) => $query->published())->orderBy('name')->get();
    }

    public function subscribe(): void
    {
        $validated = $this->validate(['newsletterEmail' => ['required', 'email', 'max:255']]);

        NewsletterSubscriber::updateOrCreate(['email' => $validated['newsletterEmail']], ['status' => 'subscribed', 'subscribed_at' => now(), 'unsubscribed_at' => null]);

        $this->reset('newsletterEmail');
        $this->subscribed = true;
    }
}; ?>

<div>
    <section class="relative overflow-hidden border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="absolute -right-24 -top-24 size-96 rounded-full text-ink-900/[0.05] dark:text-linen-100/[0.04] seal-ring"
            aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-copper-600 dark:text-copper-300 mb-5">The
                Bulletin</p>
            <div class="grid lg:grid-cols-12 gap-10 items-end">
                <div class="lg:col-span-8">
                    <h1
                        class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.05] tracking-tight">
                        The ANESMAVISA<br><span class="text-copper-500 dark:text-copper-300">Journal.</span></h1>
                    <p
                        class="mt-6 max-w-2xl text-base sm:text-lg text-ink-900/70 dark:text-linen-100/70 leading-relaxed">
                        Practical notes on technology, property, creative work, and building a small business in
                        Nigeria.</p>
                </div>
                <div class="lg:col-span-4 lg:border-l lg:border-ink-900/15 dark:lg:border-linen-100/15 lg:pl-8">
                    <p
                        class="font-mono text-[10px] uppercase tracking-widest text-ink-900/50 dark:text-linen-100/50 mb-3">
                        Archive status</p>
                    <p class="font-display text-2xl font-semibold">{{ $this->posts->total() }} entries on file</p>
                    <p class="mt-2 text-sm text-ink-900/60 dark:text-linen-100/60">Updated by the ANESMAVISA team.</p>
                </div>
            </div>
        </div>
    </section>

    @if ($this->featured)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="flex items-end justify-between gap-6 mb-8">
                <div>
                    <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-2">
                        Featured entry</p>
                    <h2 class="font-display font-semibold text-3xl sm:text-4xl">Worth your attention</h2>
                </div>
                <span
                    class="hidden sm:block font-mono text-[10px] uppercase tracking-widest text-ink-900/40 dark:text-linen-100/40">01
                    / 01</span>
            </div>
            <a href="{{ route('website.blog.show', $this->featured) }}" wire:navigate
                class="group grid lg:grid-cols-2 border border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40 overflow-hidden">
                <div
                    class="min-h-64 lg:min-h-96 bg-ink-900 dark:bg-ink-950 p-8 flex flex-col justify-between text-linen-50">
                    <span
                        class="font-mono text-xs text-copper-300">{{ $this->featured->category?->name ?? 'Journal' }}</span>
                    <span
                        class="font-display text-7xl text-copper-300/30 group-hover:text-copper-300/50 transition-colors">01</span>
                </div>
                <div class="p-7 sm:p-10 flex flex-col justify-center">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300">
                        {{ $this->featured->published_at?->format('M d, Y') }} ·
                        {{ $this->featured->read_time_minutes }} min read</p>
                    <h3
                        class="font-display font-semibold text-2xl sm:text-3xl mt-4 group-hover:text-copper-600 dark:group-hover:text-copper-300 transition-colors">
                        {{ $this->featured->title }}</h3>
                    <p class="mt-4 text-sm text-ink-900/65 dark:text-linen-100/65 leading-relaxed">
                        {{ $this->featured->excerpt }}</p>
                    <span
                        class="mt-8 text-sm font-semibold underline decoration-copper-500 decoration-2 underline-offset-4">Read
                        the entry -></span>
                </div>
            </a>
        </section>
    @endif

    <section class="border-y border-ink-900/10 dark:border-linen-100/10 bg-linen-100 dark:bg-ink-900/40">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-2" role="tablist" aria-label="Journal categories">
                <button type="button" wire:click="$set('category', 'all')"
                    class="px-3 py-2 text-xs font-mono uppercase tracking-wider border border-transparent {{ $category === 'all' ? 'bg-ink-900 text-linen-50 dark:bg-copper-500 dark:text-ink-950' : 'hover:border-ink-900/20 dark:hover:border-linen-100/20' }}">All
                    entries</button>
                @foreach ($this->categories as $blogCategory)
                    <button type="button" wire:click="$set('category', '{{ $blogCategory->slug }}')"
                        class="px-3 py-2 text-xs font-mono uppercase tracking-wider border border-transparent {{ $category === $blogCategory->slug ? 'bg-ink-900 text-linen-50 dark:bg-copper-500 dark:text-ink-950' : 'hover:border-ink-900/20 dark:hover:border-linen-100/20' }}">{{ $blogCategory->name }}</button>
                @endforeach
            </div>
            <label class="relative w-full lg:w-72">
                <span class="sr-only">Search articles</span>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search the archive..."
                    class="w-full border border-ink-900/15 dark:border-linen-100/15 bg-white dark:bg-ink-900/40 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-copper-500">
            </label>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-8">
                <h2 class="font-display font-semibold text-3xl">Latest entries</h2><span
                    class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50">{{ $this->posts->total() }}
                    results</span>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                @forelse ($this->posts as $post)
                    <article wire:key="journal-post-{{ $post->id }}"
                        class="group border border-ink-900/12 dark:border-linen-100/12 bg-white dark:bg-ink-900/40 overflow-hidden">
                        <a href="{{ route('website.blog.show', $post) }}" wire:navigate class="block p-6">
                            <div class="flex items-center justify-between gap-3 mb-5"><span
                                    class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300">{{ $post->category?->name }}</span><span
                                    class="font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40">{{ $post->read_time_minutes }}
                                    min</span></div>
                            <h3
                                class="font-display font-semibold text-xl leading-tight group-hover:text-copper-600 dark:group-hover:text-copper-300 transition-colors">
                                {{ $post->title }}</h3>
                            <p class="mt-3 text-sm text-ink-900/60 dark:text-linen-100/60 leading-relaxed">
                                {{ $post->excerpt }}</p>
                            <div
                                class="mt-6 pt-4 border-t border-ink-900/10 dark:border-linen-100/10 flex items-center justify-between text-xs">
                                <span
                                    class="font-mono text-ink-900/50 dark:text-linen-100/50">{{ $post->published_at?->format('M d, Y') }}</span><span
                                    class="font-semibold">Open file -></span>
                            </div>
                        </a>
                    </article>
                @empty
                    <p class="sm:col-span-2 py-12 text-center text-sm text-ink-900/50 dark:text-linen-100/50">No entries
                        match this search yet.</p>
                @endforelse
            </div>
            <div class="mt-10">{{ $this->posts->links() }}</div>
        </div>

        <aside class="space-y-8">
            <div class="border border-ink-900/12 dark:border-linen-100/12 p-6">
                <h3 class="font-display font-semibold text-xl mb-5">Popular posts</h3>
                <ol class="divide-y divide-ink-900/10 dark:divide-linen-100/10">
                    @foreach ($this->popular as $popularPost)
                        <li wire:key="popular-post-{{ $popularPost->id }}"><a
                                href="{{ route('website.blog.show', $popularPost) }}" wire:navigate
                                class="flex gap-4 py-4 group"><span
                                    class="font-mono text-xs text-copper-600 dark:text-copper-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span
                                    class="text-sm font-medium leading-snug group-hover:text-copper-600 dark:group-hover:text-copper-300">{{ $popularPost->title }}</span></a>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div class="border border-ink-900/12 dark:border-linen-100/12 p-6">
                <h3 class="font-display font-semibold text-xl mb-4">Filed under</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->tags as $tag)
                        <button type="button" wire:click="$set('search', '{{ $tag->name }}')"
                            class="font-mono text-[11px] px-2.5 py-1 border border-ink-900/15 dark:border-linen-100/15 text-ink-900/60 dark:text-linen-100/60 hover:border-copper-500 hover:text-copper-600 dark:hover:text-copper-300">#{{ $tag->name }}</button>
                    @endforeach
                </div>
            </div>
            <div class="bg-ink-900 dark:bg-ink-950 text-linen-50 p-6">
                <p class="font-mono text-[10px] uppercase tracking-widest text-copper-300 mb-2">Monthly dispatch</p>
                <h3 class="font-display font-semibold text-2xl">Get updates from the desk.</h3>
                <p class="text-sm text-linen-100/65 mt-2">One useful note a month. No noise.</p>
                @if ($subscribed)
                <p class="mt-5 text-sm text-sage-500">You are on the list. Thank you.</p>@else<form
                        wire:submit="subscribe" class="mt-5 space-y-2"><input wire:model="newsletterEmail"
                            type="email" required placeholder="you@email.com"
                            class="w-full px-3.5 py-2.5 text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-copper-400"><button
                            type="submit"
                            class="w-full bg-copper-500 hover:bg-copper-600 text-linen-50 font-semibold text-sm py-2.5">Subscribe
                            -></button>
                        @error('newsletterEmail')
                            <p class="text-xs text-copper-300">{{ $message }}</p>
                        @enderror
                    </form>
                @endif
            </div>
        </aside>
    </section>
</div>
