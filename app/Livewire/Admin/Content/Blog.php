<?php

namespace App\Livewire\Admin\Content;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Blog Posts')]
class Blog extends Component
{
    use WithPagination;

    public ?int $categoryId = null;
    public string $categoryName = '';

    public ?int $tagId = null;
    public string $tagLabel = '';

    public ?int $postId = null;
    public ?int $blogCategoryId = null;
    public string $postTitle = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $body = '';
    public string $featuredImage = '';
    public bool $isFeatured = false;
    public int $readTimeMinutes = 5;
    public string $status = 'draft';
    public string $publishedAt = '';
    public array $selectedTagIds = [];

    public ?string $confirmingDeleteType = null;
    public ?int $confirmingDeleteId = null;

    #[Computed]
    public function paginatedCategories()
    {
        return BlogCategory::orderBy('name')->withCount('posts')->paginate(5, ['*'], 'categoriesPage');
    }

    // Full, unpaginated - for the post-form's category <select> and the
    // "add a category first" empty-state check.
    #[Computed]
    public function categories()
    {
        return BlogCategory::orderBy('name')->get();
    }

    #[Computed]
    public function paginatedTags()
    {
        return Tag::orderBy('name')->paginate(5, ['*'], 'tagsPage');
    }

    #[Computed]
    public function tags()
    {
        return Tag::orderBy('name')->get();
    }

    #[Computed]
    public function posts()
    {
        return BlogPost::with(['category', 'author'])->latest()->paginate(10, ['*'], 'postsPage');
    }

    public function render()
    {
        return view('livewire.admin.content.blog');
    }

    public function newCategory(): void
    {
        $this->reset('categoryId', 'categoryName');
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'category-form');
    }

    public function editCategory(BlogCategory $category): void
    {
        $this->categoryId = $category->id;
        $this->categoryName = $category->name;

        $this->dispatch('open-modal', name: 'category-form');
    }

    public function saveCategory(): void
    {
        $validated = $this->validate(['categoryName' => ['required', 'string', 'max:255']]);

        $category = $this->categoryId ? BlogCategory::findOrFail($this->categoryId) : new BlogCategory();
        $category->name = $validated['categoryName'];
        $category->slug = $category->slug ?: Str::slug($validated['categoryName']);
        $category->save();

        $this->resetPage('categoriesPage');

        $this->dispatch('toast', message: 'Category saved.');
        $this->dispatch('close-modal', name: 'category-form');
        $this->newCategory();
    }

    public function newTag(): void
    {
        $this->reset('tagId', 'tagLabel');
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'tag-form');
    }

    public function editTag(Tag $tag): void
    {
        $this->tagId = $tag->id;
        $this->tagLabel = $tag->name;

        $this->dispatch('open-modal', name: 'tag-form');
    }

    public function saveTag(): void
    {
        $validated = $this->validate(['tagLabel' => ['required', 'string', 'max:255']]);

        $tag = $this->tagId ? Tag::findOrFail($this->tagId) : new Tag();
        $tag->name = $validated['tagLabel'];
        $tag->slug = $tag->slug ?: Str::slug($validated['tagLabel']);
        $tag->save();

        $this->resetPage('tagsPage');

        $this->dispatch('toast', message: 'Tag saved.');
        $this->dispatch('close-modal', name: 'tag-form');
        $this->newTag();
    }

    public function newPost(): void
    {
        $this->reset(
            'postId', 'blogCategoryId', 'postTitle', 'slug', 'excerpt', 'body',
            'featuredImage', 'readTimeMinutes', 'publishedAt', 'selectedTagIds'
        );
        $this->isFeatured = false;
        $this->status = 'draft';
        $this->readTimeMinutes = 5;
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'post-form');
    }

    public function editPost(BlogPost $post): void
    {
        $this->postId = $post->id;
        $this->blogCategoryId = $post->blog_category_id;
        $this->postTitle = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt;
        $this->body = $post->body;
        $this->featuredImage = $post->featured_image ?? '';
        $this->isFeatured = $post->is_featured;
        $this->readTimeMinutes = $post->read_time_minutes;
        $this->status = $post->status;
        $this->publishedAt = $post->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->selectedTagIds = $post->tags()->pluck('tags.id')->all();

        $this->dispatch('open-modal', name: 'post-form');
    }

    public function savePost(): void
    {
        $validated = $this->validate([
            'blogCategoryId' => ['required', 'exists:blog_categories,id'],
            'postTitle' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($this->postId)],
            'excerpt' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'featuredImage' => ['nullable', 'url', 'max:255'],
            'isFeatured' => ['boolean'],
            'readTimeMinutes' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:draft,published'],
            'publishedAt' => ['nullable', 'date'],
        ]);

        $post = $this->postId ? BlogPost::findOrFail($this->postId) : new BlogPost();

        $publishedAt = $validated['publishedAt']
            ?: ($validated['status'] === 'published' ? now() : null);

        $post->fill([
            'blog_category_id' => $validated['blogCategoryId'],
            'author_id' => $post->author_id ?? auth()->id(),
            'title' => $validated['postTitle'],
            'slug' => $validated['slug'] ?: Str::slug($validated['postTitle']),
            'excerpt' => $validated['excerpt'],
            'body' => $validated['body'],
            'featured_image' => $validated['featuredImage'] ?: null,
            'is_featured' => $validated['isFeatured'],
            'read_time_minutes' => $validated['readTimeMinutes'],
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ])->save();

        $post->tags()->sync($this->selectedTagIds);

        $this->resetPage('postsPage');

        $this->dispatch('toast', message: 'Post saved.');
        $this->dispatch('close-modal', name: 'post-form');
        $this->newPost();
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->confirmingDeleteType = $type;
        $this->confirmingDeleteId = $id;

        // Each entity type has its OWN modal name, nested inside its own
        // island (see the view). This is the key fix: the trigger button
        // and the modal it opens now live in the same island, so the
        // island's re-render always includes fresh modal content - no
        // cross-island staleness, no manual wire:island targeting needed.
        $this->dispatch('open-modal', name: $this->deleteModalName());
    }

    public function deleteConfirmed(): void
    {
        if ($this->confirmingDeleteType === 'category') {
            $category = BlogCategory::findOrFail($this->confirmingDeleteId);

            if ($category->posts()->exists()) {
                $this->dispatch('toast', message: 'Move or delete its posts first.', type: 'danger');
                $this->dispatch('close-modal', name: $this->deleteModalName());
                $this->confirmingDeleteType = null;
                $this->confirmingDeleteId = null;

                return;
            }
        }

        match ($this->confirmingDeleteType) {
            'category' => BlogCategory::findOrFail($this->confirmingDeleteId)->delete(),
            'tag' => Tag::findOrFail($this->confirmingDeleteId)->delete(),
            'post' => BlogPost::findOrFail($this->confirmingDeleteId)->delete(),
            default => null,
        };

        match ($this->confirmingDeleteType) {
            'category' => $this->resetPage('categoriesPage'),
            'tag' => $this->resetPage('tagsPage'),
            'post' => $this->resetPage('postsPage'),
            default => null,
        };

        $this->dispatch('toast', message: 'Deleted.', type: 'danger');
        $this->dispatch('close-modal', name: $this->deleteModalName());
        $this->confirmingDeleteType = null;
        $this->confirmingDeleteId = null;
    }

    private function deleteModalName(): string
    {
        return match ($this->confirmingDeleteType) {
            'category' => 'confirm-delete-category',
            'tag' => 'confirm-delete-tag',
            'post' => 'confirm-delete-post',
            default => 'confirm-delete-category',
        };
    }

    #[On('modal-closed')]
    public function onModalClosed(string $name): void
    {
        if (in_array($name, ['confirm-delete-category', 'confirm-delete-tag', 'confirm-delete-post'], true)) {
            $this->confirmingDeleteType = null;
            $this->confirmingDeleteId = null;
        }
    }
}
