<div class="space-y-6">

    {{-- Categories - small fixed list, no pagination needed --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold">Categories</h2>
            <x-forms.button type="button" variant="secondary" wire:click="newCategory">
                Add category
            </x-forms.button>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach ($categories as $category)
                <div wire:key="category-{{ $category->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-2.5">
                    <p class="flex-1 text-sm">{{ $category->name }} <span class="text-ink-900/40 dark:text-linen-100/40">({{ $category->projects_count }})</span></p>
                    <button type="button" wire:click="editCategory({{ $category->id }})" class="text-xs text-copper-600 dark:text-copper-300">Edit</button>
                    <button type="button" wire:click="confirmDelete('category', {{ $category->id }})" class="text-danger-500" aria-label="Delete {{ $category->name }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Projects --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold">Projects</h2>
            @if ($categories->isEmpty())
                <span class="text-xs text-ink-900/40 dark:text-linen-100/40">Add a category above before adding a project.</span>
            @else
                <x-forms.button type="button" variant="primary" wire:click="newProject">
                    Add project
                </x-forms.button>
            @endif
        </div>

        <div class="space-y-2">
            @foreach ($projects as $project)
                <div wire:key="project-{{ $project->id }}" class="flex items-center gap-3 rounded-md border border-ink-900/10 dark:border-linen-100/10 p-4">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm truncate">{{ $project->title }}</p>
                        <p class="text-xs text-ink-900/50 dark:text-linen-100/50">{{ $project->category->name }} · {{ $project->summary }}</p>
                    </div>

                    @if ($project->is_featured)
                        <span class="font-mono text-[10px] px-2 py-1 rounded-full bg-copper-500/15 text-copper-600 dark:text-copper-300 shrink-0">featured</span>
                    @endif

                    <button type="button" wire:click="editProject({{ $project->id }})" class="text-xs text-copper-600 dark:text-copper-300 shrink-0">Edit</button>
                    <button type="button" wire:click="confirmDelete('project', {{ $project->id }})" class="text-danger-500 shrink-0" aria-label="Delete {{ $project->title }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        </div>

        {{ $projects->links() }}
    </div>

    <x-forms.modal name="category-form">
        <form wire:submit="saveCategory" class="space-y-6">
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
        <form wire:submit="saveProject" class="space-y-6 max-h-[65vh] overflow-y-auto pr-1">
            <h2 class="font-display text-lg font-semibold">{{ $projectId ? 'Edit project' : 'Add project' }}</h2>

            <div class="flex flex-col gap-1.5">
                <label for="projectCategoryId" class="text-sm font-medium text-ink-800 dark:text-linen-100">Category</label>
                <select wire:model="projectCategoryId" id="projectCategoryId" class="rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 focus:outline-none focus:ring-2 focus:ring-copper-400">
                    <option value="">— Select a category —</option>
                    @foreach ($categories as $category)
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

    {{-- Copy changes based on type - a category delete is a bigger deal
         than a single project delete, since it cascades to every
         project under it. Saying so plainly here, not glossing over it. --}}
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
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteConfirmed">Delete</x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>