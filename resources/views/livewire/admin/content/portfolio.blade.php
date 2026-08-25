<div class="space-y-6">

    {{-- Categories - paginated management island --}}
    @island(name: 'categories-island')
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold">Categories</h2>
                <x-forms.button type="button" variant="secondary" wire:click="newCategory">
                    Add category
                </x-forms.button>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($this->paginatedCategories as $category)
                    <div wire:key="category-{{ $category->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-2.5">
                        <p class="flex-1 text-sm">{{ $category->name }} <span class="text-ink-900/40 dark:text-linen-100/40">({{ $category->projects_count }})</span></p>
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

    {{-- Projects - filter chips + card grid, matching the prototype's
         own admin layout, backed by real category ids instead of a
         hardcoded array. $this->categories (full list) drives the
         chips regardless of what page the categories island is on. --}}
    @island(name: 'projects-island')
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold">Projects</h2>
                @if ($this->categories->isEmpty())
                    <span class="text-xs text-ink-900/40 dark:text-linen-100/40">Add a category above before adding a project.</span>
                @else
                    <x-forms.button type="button" variant="primary" wire:click="newProject">
                        Add project
                    </x-forms.button>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="filterByCategory(null)"
                    wire:island="projects-island"
                    class="font-mono text-[10px] uppercase tracking-wide px-3 py-1.5 rounded-full border transition-colors
                        {{ is_null($categoryFilter)
                            ? 'bg-copper-500 border-copper-500 text-ink-950 font-semibold'
                            : 'border-ink-900/15 dark:border-linen-100/15 text-ink-900/60 dark:text-linen-100/60 hover:border-copper-400' }}"
                >
                    All
                </button>
                @foreach ($this->categories as $category)
                    <button
                        type="button"
                        wire:click="filterByCategory({{ $category->id }})"
                        wire:island="projects-island"
                        class="font-mono text-[10px] uppercase tracking-wide px-3 py-1.5 rounded-full border transition-colors
                            {{ $categoryFilter === $category->id
                                ? 'bg-copper-500 border-copper-500 text-ink-950 font-semibold'
                                : 'border-ink-900/15 dark:border-linen-100/15 text-ink-900/60 dark:text-linen-100/60 hover:border-copper-400' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->projects as $project)
                    <div wire:key="project-{{ $project->id }}" class="rounded-md border border-ink-900/10 dark:border-linen-100/10 overflow-hidden">
                        @if ($project->image_path)
                            <img src="{{ $project->image_path }}" alt="{{ $project->title }}" class="w-full h-32 object-cover" loading="lazy" />
                        @else
                            <div class="w-full h-32 bg-ink-900/5 dark:bg-linen-100/5 grid place-items-center">
                                <span class="font-mono text-[10px] text-ink-900/30 dark:text-linen-100/30">No image</span>
                            </div>
                        @endif

                        <div class="p-3 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[10px] uppercase tracking-wide text-copper-600 dark:text-copper-300">{{ $project->category->name }}</span>
                                @if ($project->is_featured)
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300">featured</span>
                                @endif
                            </div>
                            <p class="font-medium text-sm truncate">{{ $project->title }}</p>
                            <p class="text-xs text-ink-900/50 dark:text-linen-100/50 line-clamp-2">{{ $project->summary }}</p>

                            <div class="flex items-center gap-3 pt-1">
                                <button type="button" wire:click="editProject({{ $project->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                                <button type="button" wire:click="confirmDelete('project', {{ $project->id }})" class="text-xs text-danger-500">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-900/40 dark:text-linen-100/40 col-span-full">No projects in this category yet.</p>
                @endforelse
            </div>

            @if ($this->projects->hasPages())
                <div class="flex items-center justify-between text-sm pt-1">
                    @if ($this->projects->onFirstPage())
                        <span class="text-ink-900/30 dark:text-linen-100/30">‹ Previous</span>
                    @else
                        <button type="button" wire:click="previousPage('projectsPage')" wire:island="projects-island" class="text-copper-600 dark:text-copper-300">‹ Previous</button>
                    @endif

                    <span class="text-ink-900/50 dark:text-linen-100/50 text-xs">
                        Page {{ $this->projects->currentPage() }} of {{ $this->projects->lastPage() }}
                    </span>

                    @if ($this->projects->hasMorePages())
                        <button type="button" wire:click="nextPage('projectsPage')" wire:island="projects-island" class="text-copper-600 dark:text-copper-300">Next ›</button>
                    @else
                        <span class="text-ink-900/30 dark:text-linen-100/30">Next ›</span>
                    @endif
                </div>
            @endif
        </div>
    @endisland

    <x-forms.modal name="category-form">
        <form wire:submit="saveCategory" wire:island="categories-island" class="space-y-6">
            <h2 class="font-display text-lg font-semibold">{{ $categoryId ? 'Edit category' : 'Add category' }}</h2>
            <x-forms.input wire:model="categoryName" name="categoryName" label="Name" type="text" required />
            <x-forms.input wire:model="categorySortOrder" name="categorySortOrder" label="Sort order" type="number" required />
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="project-form">
        <form wire:submit="saveProject" wire:island="projects-island" class="space-y-6 max-h-[65vh] overflow-y-auto pr-1">
            <h2 class="font-display text-lg font-semibold">{{ $projectId ? 'Edit project' : 'Add project' }}</h2>

            <div class="flex flex-col gap-1.5">
                <label for="projectCategoryId" class="text-sm font-medium text-ink-800 dark:text-linen-100">Category</label>
                <select wire:model="projectCategoryId" id="projectCategoryId" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                    <option value="">— Select a category —</option>
                    @foreach ($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('projectCategoryId') <p class="text-xs text-danger-500">{{ $message }}</p> @enderror
            </div>

            <x-forms.input wire:model="projectTitle" name="projectTitle" label="Title" type="text" required autofocus />
            <x-forms.input wire:model="slug" name="slug" label="Slug (leave blank to auto-generate)" type="text" />
            <x-forms.input wire:model="summary" name="summary" label="Summary (shown on the Portfolio grid)" type="text" required />

            <div class="flex flex-col gap-1.5">
                <label for="body" class="text-sm font-medium text-ink-800 dark:text-linen-100">Full case study (optional)</label>
                <textarea wire:model="body" id="body" rows="6" class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400"></textarea>
            </div>

            <x-forms.input wire:model="imagePath" name="imagePath" label="Image URL" type="text" placeholder="https://…" />

            <div class="flex items-center gap-6">
                <x-forms.input wire:model="projectSortOrder" name="projectSortOrder" label="Sort order" type="number" required class="flex-1" />
                <x-forms.checkbox wire:model="isFeatured" name="isFeatured" label="Featured" />
            </div>

            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button type="submit" variant="primary" class="flex-1">Save</x-forms.button>
            </div>
        </form>
    </x-forms.modal>

    <x-forms.modal name="confirm-delete">
        <div class="space-y-6">
            <h2 class="font-display text-lg font-semibold">Delete this {{ $confirmingDeleteType }}?</h2>
            <p class="text-sm text-ink-600 dark:text-linen-300">
                @if ($confirmingDeleteType === 'category')
                    This also deletes every project in this category. This can't be undone.
                @else
                    This can't be undone.
                @endif
            </p>
            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" @click="close()">Cancel</x-forms.button>
                <x-forms.button
                    type="button"
                    variant="danger"
                    class="flex-1"
                    wire:click="deleteConfirmed"
                    wire:island="{{ match ($confirmingDeleteType) {
                        'category' => 'categories-island',
                        'project' => 'projects-island',
                        default => '',
                    } }}"
                >
                    Delete
                </x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>
