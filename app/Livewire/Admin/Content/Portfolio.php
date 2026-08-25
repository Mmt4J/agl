<?php

namespace App\Livewire\Admin\Content;

use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Portfolio')]
class Portfolio extends Component
{
    use WithPagination;

    // Category form state
    public ?int $categoryId = null;
    public string $categoryName = '';
    public int $categorySortOrder = 0;

    // Project form state
    public ?int $projectId = null;
    public ?int $projectCategoryId = null;
    public string $projectTitle = '';
    public string $slug = '';
    public string $summary = '';
    public string $body = '';
    public string $imagePath = '';
    public bool $isFeatured = false;
    public int $projectSortOrder = 0;

    public ?string $confirmingDeleteType = null;
    public ?int $confirmingDeleteId = null;

    public function render()
    {
        return view('livewire.admin.content.portfolio', [
            // Small, fixed list (5-ish categories per the migration's own
            // comment) - no pagination needed here, unlike projects below.
            'categories' => PortfolioCategory::orderBy('sort_order')->withCount('projects')->get(),
            'projects' => PortfolioProject::with('category')->orderBy('sort_order')->paginate(10),
        ]);
    }

    public function newCategory(): void
    {
        $this->reset('categoryId', 'categoryName', 'categorySortOrder');
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'category-form');
    }

    public function editCategory(PortfolioCategory $category): void
    {
        $this->categoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categorySortOrder = $category->sort_order;

        $this->dispatch('open-modal', name: 'category-form');
    }

    public function saveCategory(): void
    {
        $validated = $this->validate([
            'categoryName' => ['required', 'string', 'max:255'],
            'categorySortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $category = $this->categoryId ? PortfolioCategory::findOrFail($this->categoryId) : new PortfolioCategory();
        $category->name = $validated['categoryName'];
        $category->slug = $category->slug ?: Str::slug($validated['categoryName']);
        $category->sort_order = $validated['categorySortOrder'];
        $category->save();

        $this->dispatch('toast', message: 'Category saved.');
        $this->dispatch('close-modal', name: 'category-form');
        $this->newCategory();
    }

    public function newProject(): void
    {
        $this->reset(
            'projectId', 'projectCategoryId', 'projectTitle', 'slug',
            'summary', 'body', 'imagePath', 'projectSortOrder'
        );
        $this->isFeatured = false;
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'project-form');
    }

    public function editProject(PortfolioProject $project): void
    {
        $this->projectId = $project->id;
        $this->projectCategoryId = $project->portfolio_category_id;
        $this->projectTitle = $project->title;
        $this->slug = $project->slug;
        $this->summary = $project->summary;
        $this->body = $project->body ?? '';
        $this->imagePath = $project->image_path ?? '';
        $this->isFeatured = $project->is_featured;
        $this->projectSortOrder = $project->sort_order;

        $this->dispatch('open-modal', name: 'project-form');
    }

    public function saveProject(): void
    {
        $validated = $this->validate([
            'projectCategoryId' => ['required', 'exists:portfolio_categories,id'],
            'projectTitle' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('portfolio_projects', 'slug')->ignore($this->projectId)],
            'summary' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'imagePath' => ['nullable', 'url', 'max:255'],
            'isFeatured' => ['boolean'],
            'projectSortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $project = $this->projectId ? PortfolioProject::findOrFail($this->projectId) : new PortfolioProject();
        $project->fill([
            'portfolio_category_id' => $validated['projectCategoryId'],
            'title' => $validated['projectTitle'],
            'slug' => $validated['slug'] ?: Str::slug($validated['projectTitle']),
            'summary' => $validated['summary'],
            'body' => $validated['body'] ?: null,
            'image_path' => $validated['imagePath'] ?: null,
            'is_featured' => $validated['isFeatured'],
            'sort_order' => $validated['projectSortOrder'],
        ])->save();

        // New/re-sorted project can shift which page it lands on -
        // reset to page 1 so it's actually visible after saving.
        $this->resetPage();

        $this->dispatch('toast', message: 'Project saved.');
        $this->dispatch('close-modal', name: 'project-form');
        $this->newProject();
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->confirmingDeleteType = $type;
        $this->confirmingDeleteId = $id;

        $this->dispatch('open-modal', name: 'confirm-delete');
    }

    public function deleteConfirmed(): void
    {
        match ($this->confirmingDeleteType) {
            // Unlike blog categories (which block deletion if posts
            // exist), portfolio_category_id cascades - deleting a
            // category here genuinely deletes every project under it
            // too. The confirmation copy in the view says this plainly.
            'category' => PortfolioCategory::findOrFail($this->confirmingDeleteId)->delete(),
            'project' => PortfolioProject::findOrFail($this->confirmingDeleteId)->delete(),
            default => null,
        };

        $this->resetPage();

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