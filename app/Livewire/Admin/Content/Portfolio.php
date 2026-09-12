<?php

namespace App\Livewire\Admin\Content;

use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Portfolio')]
class Portfolio extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $categoryId = null;

    public string $categoryName = '';

    public int $categorySortOrder = 0;

    public ?int $projectId = null;

    public ?int $projectCategoryId = null;

    public string $projectTitle = '';

    public string $slug = '';

    public string $summary = '';

    public string $body = '';

    public string $imagePath = '';

    public $imageFile = null;

    public bool $isFeatured = false;

    public int $projectSortOrder = 0;

    // null = "All" - matches the prototype's filter-chip bar exactly,
    // just backed by a real category id instead of a hardcoded string.
    public ?int $categoryFilter = null;

    public ?string $confirmingDeleteType = null;

    public ?int $confirmingDeleteId = null;

    // Paginated - categories management island's own list.
    #[Computed]
    public function paginatedCategories()
    {
        return PortfolioCategory::orderBy('sort_order')->withCount('projects')->paginate(6, ['*'], 'categoriesPage');
    }

    // Full, unpaginated - for the filter-chip bar and the project-form's
    // category <select>. Same reasoning as Blog: chips/dropdowns
    // shouldn't hide options behind a management-list page number.
    #[Computed]
    public function categories()
    {
        return PortfolioCategory::orderBy('sort_order')->get();
    }

    #[Computed]
    public function projects()
    {
        return PortfolioProject::with('category')
            ->when($this->categoryFilter, fn ($q) => $q->where('portfolio_category_id', $this->categoryFilter))
            ->orderBy('sort_order')
            ->paginate(9, ['*'], 'projectsPage'); // 9 = clean 3x3 grid
    }

    public function render()
    {
        return view('livewire.admin.content.portfolio');
    }

    public function filterByCategory(?int $categoryId): void
    {
        $this->categoryFilter = $categoryId;
        $this->resetPage('projectsPage');
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

        $category = $this->categoryId ? PortfolioCategory::findOrFail($this->categoryId) : new PortfolioCategory;
        $category->name = $validated['categoryName'];
        $category->slug = $category->slug ?: Str::slug($validated['categoryName']);
        $category->sort_order = $validated['categorySortOrder'];
        $category->save();

        $this->resetPage('categoriesPage');

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
        $this->imageFile = null;
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
        $this->imageFile = null;
        $this->isFeatured = $project->is_featured;
        $this->projectSortOrder = $project->sort_order;

        $this->dispatch('open-modal', name: 'project-form');
    }

    // Strictly one image source: picking a file clears a typed link, and
    // typing a link clears a picked file.
    public function updatedImageFile(): void
    {
        $this->imagePath = '';
    }

    public function updatedImagePath(): void
    {
        $this->imageFile = null;
        $this->resetErrorBag('imageFile');
    }

    public function saveProject(): void
    {
        $validated = $this->validate([
            'projectCategoryId' => ['required', 'exists:portfolio_categories,id'],
            'projectTitle' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('portfolio_projects', 'slug')->ignore($this->projectId)],
            'summary' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            // Either an absolute link, a root-relative path, or a stored upload
            // path (portfolio/…). Junk strings and both-fields-at-once are rejected.
            'imagePath' => [
                'nullable', 'string', 'max:255',
                'regex:/^(https?:\/\/|\/|portfolio\/)[^\s]+$/',
                function ($attribute, $value, $fail) {
                    if (filled($value) && filled($this->imageFile)) {
                        $fail('Choose either an image file or an image URL, not both.');
                    }
                },
            ],
            'imageFile' => ['nullable', 'image', 'max:5120'],
            'isFeatured' => ['boolean'],
            'projectSortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $imagePath = $this->imageFile
            ? $this->imageFile->store('portfolio', 'public')
            : ($validated['imagePath'] !== '' ? $validated['imagePath'] : null);

        $project = $this->projectId ? PortfolioProject::findOrFail($this->projectId) : new PortfolioProject;
        $project->fill([
            'portfolio_category_id' => $validated['projectCategoryId'],
            'title' => $validated['projectTitle'],
            'slug' => $validated['slug'] ?: Str::slug($validated['projectTitle']),
            'summary' => $validated['summary'],
            'body' => $validated['body'] ?: null,
            'image_path' => $imagePath,
            'is_featured' => $validated['isFeatured'],
            'sort_order' => $validated['projectSortOrder'],
        ])->save();

        $this->resetPage('projectsPage');

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
            // cascadeOnDelete - deleting a category takes its projects too.
            'category' => PortfolioCategory::findOrFail($this->confirmingDeleteId)->delete(),
            'project' => PortfolioProject::findOrFail($this->confirmingDeleteId)->delete(),
            default => null,
        };

        match ($this->confirmingDeleteType) {
            'category' => $this->resetPage('categoriesPage'),
            'project' => $this->resetPage('projectsPage'),
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
