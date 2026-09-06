<?php

use App\Models\BlogPost;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::website')] class extends Component {
    public int $postId;

    public function mount(BlogPost $post): void
    {
        abort_unless($post->status === 'published' && $post->published_at?->isPast(), 404);

        $this->postId = $post->id;
        $post->increment('view_count');
    }

    #[Computed]
    public function post(): BlogPost
    {
        return BlogPost::with(['category', 'author', 'tags'])->findOrFail($this->postId);
    }

    public function relatedPosts()
    {
        return $this->post->related(2);
    }
}; ?>

<div>
    <section class="border-b border-ink-900/10 dark:border-linen-100/10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10">
            <a href="{{ route('website.blog') }}" wire:navigate
                class="inline-flex items-center gap-2 text-sm font-semibold text-copper-600 dark:text-copper-300 hover:underline mb-8">&lt;-
                Back to journal</a>
            <p class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300">
                {{ $this->post->category?->name }} · {{ $this->post->read_time_minutes }} min read</p>
            <h1 class="font-display font-semibold text-4xl sm:text-5xl leading-tight mt-4">{{ $this->post->title }}</h1>
            <p class="mt-5 text-lg text-ink-900/65 dark:text-linen-100/65 leading-relaxed">{{ $this->post->excerpt }}</p>
            <div
                class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-mono text-ink-900/50 dark:text-linen-100/50">
                <span>{{ $this->post->author?->name ?? 'ANESMAVISA Team' }}</span><span>/</span><span>{{ $this->post->published_at?->format('M d, Y') }}</span><span>/</span><span>Entry
                    {{ str_pad((string) $this->post->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="border-y-2 border-copper-500 py-8 sm:py-10">
            <div class="max-w-none text-lg text-ink-900/80 dark:text-linen-100/80 leading-relaxed whitespace-pre-line">
                {{ $this->post->body }}</div>
        </div>

        <div
            class="flex flex-wrap items-center justify-between gap-4 mt-8 pt-6 border-t border-ink-900/10 dark:border-linen-100/10">
            <div class="flex flex-wrap gap-2">
                @foreach ($this->post->tags as $tag)
                    <span
                        class="font-mono text-[11px] px-2.5 py-1 bg-ink-900/5 dark:bg-linen-100/5">#{{ $tag->name }}</span>
                @endforeach
            </div>
            <span
                class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50">{{ number_format($this->post->view_count) }}
                views</span>
        </div>

        @if ($this->relatedPosts()->isNotEmpty())
            <div class="mt-16">
                <p class="font-mono text-xs uppercase tracking-widest text-copper-600 dark:text-copper-300 mb-3">
                    Continue reading</p>
                <h2 class="font-display font-semibold text-3xl mb-6">Related entries</h2>
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach ($this->relatedPosts() as $related)
                        <a wire:key="related-post-{{ $related->id }}"
                            href="{{ route('website.blog.show', $related) }}" wire:navigate
                            class="group border border-ink-900/12 dark:border-linen-100/12 p-5 hover:border-copper-500/60 transition-colors">
                            <span
                                class="font-mono text-[10px] uppercase tracking-widest text-copper-600 dark:text-copper-300">{{ $related->category?->name }}</span>
                            <h3
                                class="font-display font-semibold text-lg mt-2 group-hover:text-copper-600 dark:group-hover:text-copper-300">
                                {{ $related->title }}</h3>
                            <p class="text-sm text-ink-900/60 dark:text-linen-100/60 mt-2">{{ $related->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>
