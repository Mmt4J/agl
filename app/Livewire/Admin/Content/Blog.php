<?php

namespace App\Livewire\Admin\Content;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

    // Category lookup form state
    public ?int $categoryId = null;
    public string $categoryName = '';

    // Tag lookup form state
    public ?int $tagId = null;
    public string $tagName = '';

    // Post form state
    public ?int $postId = null;
    public ?int $blogCategoryId = null;
    public string $title = '';
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

    public function render()
    {
        return view('livewire.admin.content.blog', [
            'categories' => BlogCategory::orderBy('name')->withCount('posts')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'posts' => BlogPost::with(['category', 'author'])->latest()->paginate(10),
        ]);
    }

    public function newCategory(): void
    {
        $this->reset('categoryId', 'categoryName');
        $this->resetErrorBag();
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

        $this->dispatch('toast', message: 'Category saved.');
        $this->dispatch('close-modal', name: 'category-form');
        $this->newCategory();
    }

    public function newTag(): void
    {
        $this->reset('tagId', 'tagName');
        $this->resetErrorBag();
    }

    public function editTag(Tag $tag): void
    {
        $this->tagId = $tag->id;
        $this->tagName = $tag->name;

        $this->dispatch('open-modal', name: 'tag-form');
    }

    public function saveTag(): void
    {
        $validated = $this->validate(['tagName' => ['required', 'string', 'max:255']]);

        $tag = $this->tagId ? Tag::findOrFail($this->tagId) : new Tag();
        $tag->name = $validated['tagName'];
        $tag->slug = $tag->slug ?: Str::slug($validated['tagName']);
        $tag->save();

        $this->dispatch('toast', message: 'Tag saved.');
        $this->dispatch('close-modal', name: 'tag-form');
        $this->newTag();
    }

    public function newPost(): void
    {
        $this->reset(
            'postId', 'blogCategoryId', 'title', 'slug', 'excerpt', 'body',
            'featuredImage', 'readTimeMinutes', 'publishedAt', 'selectedTagIds'
        );
        $this->isFeatured = false;
        $this->status = 'draft';
        $this->readTimeMinutes = 5;
        $this->resetErrorBag();
    }

    public function editPost(BlogPost $post): void
    {
        $this->postId = $post->id;
        $this->blogCategoryId = $post->blog_category_id;
        $this->title = $post->title;
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
            'title' => ['required', 'string', 'max:255'],
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

        // Publishing without an explicit date/time defaults to right now -
        // a manually-set FUTURE date instead schedules it, since
        // scopePublished() already checks published_at <= now().
        $publishedAt = $validated['publishedAt']
            ?: ($validated['status'] === 'published' ? now() : null);

        $post->fill([
            'blog_category_id' => $validated['blogCategoryId'],
            'author_id' => $post->author_id ?? auth()->id(),
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'excerpt' => $validated['excerpt'],
            'body' => $validated['body'],
            'featured_image' => $validated['featuredImage'] ?: null,
            'is_featured' => $validated['isFeatured'],
            'read_time_minutes' => $validated['readTimeMinutes'],
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ])->save();

        $post->tags()->sync($this->selectedTagIds);

        $this->dispatch('toast', message: 'Post saved.');
        $this->dispatch('close-modal', name: 'post-form');
        $this->newPost();
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->confirmingDeleteType = $type;
        $this->confirmingDeleteId = $id;
    }

    public function deleteConfirmed(): void
    {
        // blog_category_id uses restrictOnDelete (not cascade) - a category
        // with posts still attached would otherwise throw a raw database
        // exception here instead of a message the admin can actually act on.
        if ($this->confirmingDeleteType === 'category') {
            $category = BlogCategory::findOrFail($this->confirmingDeleteId);

            if ($category->posts()->exists()) {
                $this->dispatch('toast', message: 'Move or delete its posts first.', type: 'danger');
                $this->dispatch('close-modal', name: 'confirm-delete');
                $this->confirmingDeleteType = null;
                $this->confirmingDeleteId = null;

                return;
            }
        }

        match ($this->confirmingDeleteType) {
            'category' => BlogCategory::findOrFail($this->confirmingDeleteId)->delete(),
            'tag' => Tag::findOrFail($this->confirmingDeleteId)->delete(),
            // SoftDeletes - hides it, doesn't erase the content.
            'post' => BlogPost::findOrFail($this->confirmingDeleteId)->delete(),
            default => null,
        };

        $this->dispatch('toast', message: 'Deleted.', type: 'danger');
        $this->dispatch('close-modal', name: 'confirm-delete');
        $this->confirmingDeleteType = null;
        $this->confirmingDeleteId = null;
    }

    #[On('modal-closed')]
    public function onModalClosed(string $name): void
    {
        if ($name === 'confirm-delete') {
            $this->confirmingDeleteType = null;
            $this->confirmingDeleteId = null;
        }
    }
}