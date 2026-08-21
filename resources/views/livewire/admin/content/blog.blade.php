<div class="space-y-6">

    {{-- Categories & Tags - side by side, same grid shape as the original
         non-island layout. Each is still its own independent island;
         wrapping them in a shared grid container is purely visual and
         doesn't affect island boundaries or re-render scoping. --}}
    <div class="grid lg:grid-cols-2 gap-6">

        @island(name: 'categories-island')
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="font-display font-semibold">Categories</h2>
                    <x-forms.button type="button" variant="secondary" wire:click="newCategory">
                        Add
                    </x-forms.button>
                </div>

                <div class="space-y-2">
                    @foreach ($this->paginatedCategories as $category)
                        <div wire:key="category-{{ $category->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-2.5">
                            <p class="flex-1 text-sm">{{ $category->name }} <span class="text-ink-900/40 dark:text-linen-100/40">({{ $category->posts_count }})</span></p>
                            <button type="button" wire:click="editCategory({{ $category->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                            <button type="button" wire:click="confirmDelete('category', {{ $category->id }})" class="text-danger-500" aria-label="Delete {{ $category->name }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                @if ($this->paginatedCategories->hasPages())
                    <div class="flex items-center justify-between text-sm pt-1">
                        @if ($this->paginatedCategories->onFirstPage())
                            <span class="text-ink-900/30 dark:text-linen-100/30">‹ Previous</span>
                        @else
                            <button type="button" wire:click="previousPage('categoriesPage')" wire:island="categories-island" class="text-copper-600 dark:text-copper-300">‹ Previous</button>
                        @endif

                        <span class="text-ink-900/50 dark:text-linen-100/50 text-xs">
                            Page {{ $this->paginatedCategories->currentPage() }} of {{ $this->paginatedCategories->lastPage() }}
                        </span>

                        @if ($this->paginatedCategories->hasMorePages())
                            <button type="button" wire:click="nextPage('categoriesPage')" wire:island="categories-island" class="text-copper-600 dark:text-copper-300">Next ›</button>
                        @else
                            <span class="text-ink-900/30 dark:text-linen-100/30">Next ›</span>
                        @endif
                    </div>
                @endif
            </div>
        @endisland

        @island(name: 'tags-island')
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="font-display font-semibold">Tags</h2>
                    <x-forms.button type="button" variant="secondary" wire:click="newTag">
                        Add
                    </x-forms.button>
                </div>

                <div class="space-y-2">
                    @foreach ($this->paginatedTags as $tag)
                        <div wire:key="tag-{{ $tag->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-2.5">
                            <p class="flex-1 text-sm">{{ $tag->name }}</p>
                            <button type="button" wire:click="editTag({{ $tag->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                            <button type="button" wire:click="confirmDelete('tag', {{ $tag->id }})" class="text-danger-500" aria-label="Delete {{ $tag->name }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                @if ($this->paginatedTags->hasPages())
                    <div class="flex items-center justify-between text-sm pt-1">
                        @if ($this->paginatedTags->onFirstPage())
                            <span class="text-ink-900/30 dark:text-linen-100/30">‹ Previous</span>
                        @else
                            <button type="button" wire:click="previousPage('tagsPage')" wire:island="tags-island" class="text-copper-600 dark:text-copper-300">‹ Previous</button>
                        @endif

                        <span class="text-ink-900/50 dark:text-linen-100/50 text-xs">
                            Page {{ $this->paginatedTags->currentPage() }} of {{ $this->paginatedTags->lastPage() }}
                        </span>

                        @if ($this->paginatedTags->hasMorePages())
                            <button type="button" wire:click="nextPage('tagsPage')" wire:island="tags-island" class="text-copper-600 dark:text-copper-300">Next ›</button>
                        @else
                            <span class="text-ink-900/30 dark:text-linen-100/30">Next ›</span>
                        @endif
                    </div>
                @endif
            </div>
        @endisland

    </div>

    {{-- Posts - full width, below the two-column row. Already its own
         island; only the surrounding wrapper changed. $this->categories
         (the FULL, unpaginated list) is a component computed prop, so
         it's reachable here even though it's "owned" by the categories
         section above - normal Livewire, unrelated to islands. --}}
    @island(name: 'posts-island')
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold">Posts</h2>
                @if ($this->categories->isEmpty())
                    <span class="text-xs text-ink-900/40 dark:text-linen-100/40">
                        Add a category above before writing a post.
                    </span>
                @else
                    <x-forms.button type="button" variant="primary" wire:click="newPost">
                        Write post
                    </x-forms.button>
                @endif
            </div>

            <div class="space-y-2">
                @foreach ($this->posts as $post)
                    <div wire:key="post-{{ $post->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-4">
                        <span class="font-mono text-[10px] px-2 py-1 rounded-full shrink-0 {{ $post->status === 'published' ? 'bg-sage-500/15 text-sage-600 dark:text-sage-400' : 'bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/40 dark:text-linen-100/40' }}">
                            {{ $post->status }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-sm truncate">{{ $post->title }}</p>
                            <p class="text-xs text-ink-900/50 dark:text-linen-100/50">{{ $post->category->name }} · {{ $post->author->name }} · {{ $post->read_time_minutes }} min read</p>
                        </div>

                        @if ($post->is_featured)
                            <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300 shrink-0">featured</span>
                        @endif

                        <button type="button" wire:click="editPost({{ $post->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit</button>
                        <button type="button" wire:click="confirmDelete('post', {{ $post->id }})" class="text-danger-500 shrink-0" aria-label="Delete {{ $post->title }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>

            @if ($this->posts->hasPages())
                <div class="flex items-center justify-between text-sm pt-1">
                    @if ($this->posts->onFirstPage())
                        <span class="text-ink-900/30 dark:text-linen-100/30">‹ Previous</span>
                    @else
                        <button type="button" wire:click="previousPage('postsPage')" wire:island="posts-island" class="text-copper-600 dark:text-copper-300">‹ Previous</button>
                    @endif

                    <span class="text-ink-900/50 dark:text-linen-100/50 text-xs">
                        Page {{ $this->posts->currentPage() }} of {{ $this->posts->lastPage() }}
                    </span>

                    @if ($this->posts->hasMorePages())
                        <button type="button" wire:click="nextPage('postsPage')" wire:island="posts-island" class="text-copper-600 dark:text-copper-300">Next ›</button>
                    @else
                        <span class="text-ink-900/30 dark:text-linen-100/30">Next ›</span>
                    @endif
                </div>
            @endif
        </div>
    @endisland

    {{-- Modals live outside every island - same reasoning as before:
         each form/action carries wire:island explicitly so submitting
         it only refreshes the ONE island it belongs to. --}}
    <x-forms.modal name="category-form">
        <form wire:submit="saveCategory" wire:island="categories-island" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $categoryId ? 'Edit category' : 'Add category' }}</h2>
            <x-forms.input wire:model="categoryName" name="categoryName" label="Name" type="text" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="tag-form">
        <form wire:submit="saveTag" wire:island="tags-island" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $tagId ? 'Edit tag' : 'Add tag' }}</h2>
            <x-forms.input wire:model="tagLabel" name="tagLabel" label="Name" type="text" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="post-form">
        <form wire:submit="savePost" wire:island="posts-island" class="space-y-6">
            <div class="max-h-[65vh] overflow-y-auto pr-1 space-y-6">
                <h2 class="font-display text-lg font-semibold">{{ $postId ? 'Edit post' : 'Write post' }}</h2>

                <x-forms.input wire:model="postTitle" name="postTitle" label="Title" type="text" required autofocus />
                <x-forms.input wire:model="slug" name="slug" label="Slug (leave blank to auto-generate)" type="text" />
                <x-forms.input wire:model="excerpt" name="excerpt" label="Excerpt (shown on the blog listing)" type="text" required />

                <div class="flex flex-col gap-1.5">
                    <label for="body" class="text-sm font-medium text-ink-800 dark:text-linen-100">Body</label>
                    <textarea wire:model="body" id="body" rows="10" class="w-full rounded-md border px-3 py-2 text-sm font-mono bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"></textarea>
                </div>

                <x-forms.input wire:model="featuredImage" name="featuredImage" label="Featured image URL" type="text" placeholder="https://…" />

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="blogCategoryId" class="text-sm font-medium text-ink-800 dark:text-linen-100">Category</label>
                        <select wire:model="blogCategoryId" id="blogCategoryId" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                            <option value="">— Select a category —</option>
                            @foreach ($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('blogCategoryId') <p class="text-xs text-danger-500">{{ $message }}</p> @enderror
                    </div>
                    <x-forms.input wire:model="readTimeMinutes" name="readTimeMinutes" label="Read time (minutes)" type="number" required />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-ink-800 dark:text-linen-100">Tags</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->tags as $tag)
                            <label wire:key="tag-checkbox-{{ $tag->id }}" class="flex items-center gap-1.5 text-xs rounded-full border px-3 py-1.5 cursor-pointer border-ink-900/15 dark:border-linen-100/15">
                                <input type="checkbox" wire:model="selectedTagIds" value="{{ $tag->id }}" class="accent-copper-500" />
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="status" class="text-sm font-medium text-ink-800 dark:text-linen-100">Status</label>
                        <select wire:model="status" id="status" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <x-forms.input wire:model="publishedAt" name="publishedAt" label="Publish at (blank = now)" type="datetime-local" />
                </div>

                <x-forms.checkbox wire:model="isFeatured" name="isFeatured" label="Featured" />
            </div>

            <div class="flex gap-3 pt-2 border-t border-ink-900/10 dark:border-linen-100/10">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">This can't be undone.</p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button
                    type="button"
                    variant="danger"
                    class="flex-1"
                    wire:click="deleteConfirmed"
                    wire:island="{{ match ($confirmingDeleteType) {
                        'category' => 'categories-island',
                        'tag' => 'tags-island',
                        'post' => 'posts-island',
                        default => '',
                    } }}"
                >
                    Delete
                </x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>
