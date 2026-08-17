<?php

namespace App\Livewire\Admin\Content;

use App\Models\PricingCategory;
use App\Models\PricingPlan;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Pricing')]
class Pricing extends Component
{
    // Category form state
    public ?int $categoryId = null;
    public string $categoryName = '';
    public int $categorySortOrder = 0;

    // Plan form state
    public ?int $planId = null;
    public ?int $planCategoryId = null;
    public string $planName = '';
    public string $tagline = '';
    public string $priceLabel = '';
    public string $periodLabel = '';
    public bool $isHighlighted = false;
    public int $planSortOrder = 0;
    public array $features = [];

    public ?string $confirmingDeleteType = null;
    public ?int $confirmingDeleteId = null;

    public function render()
    {
        return view('livewire.admin.content.pricing', [
            'categories' => PricingCategory::orderBy('sort_order')->withCount('plans')->get(),
            'plans' => PricingPlan::with('category')->orderBy('sort_order')->get()->groupBy('pricing_category_id'),
        ]);
    }

    public function newCategory(): void
    {
        $this->reset('categoryId', 'categoryName', 'categorySortOrder');
        $this->resetErrorBag();
    }

    public function editCategory(PricingCategory $category): void
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

        $category = $this->categoryId ? PricingCategory::findOrFail($this->categoryId) : new PricingCategory();
        $category->fill([
            'name' => $validated['categoryName'],
            'slug' => $category->slug ?: Str::slug($validated['categoryName']),
            'sort_order' => $validated['categorySortOrder'],
        ])->save();

        $this->dispatch('toast', message: 'Category saved.');
        $this->dispatch('close-modal', name: 'category-form');
        $this->newCategory();
    }

    // Pre-selects the category a "+ Add plan" button was clicked from,
    // so the admin isn't re-picking it from a dropdown every time.
    public function newPlan(int $categoryId): void
    {
        $this->reset('planId', 'planName', 'tagline', 'priceLabel', 'periodLabel', 'planSortOrder');
        $this->planCategoryId = $categoryId;
        $this->isHighlighted = false;
        $this->features = [];
        $this->resetErrorBag();
    }

    public function editPlan(PricingPlan $plan): void
    {
        $this->planId = $plan->id;
        $this->planCategoryId = $plan->pricing_category_id;
        $this->planName = $plan->name;
        $this->tagline = $plan->tagline;
        $this->priceLabel = $plan->price_label;
        $this->periodLabel = $plan->period_label;
        $this->isHighlighted = $plan->is_highlighted;
        $this->planSortOrder = $plan->sort_order;

        $this->features = $plan->features()
            ->orderBy('sort_order')
            ->pluck('feature')
            ->map(fn ($feature) => ['feature' => $feature])
            ->all();

        $this->dispatch('open-modal', name: 'plan-form');
    }

    public function addFeatureRow(): void
    {
        $this->features[] = ['feature' => ''];
    }

    public function removeFeatureRow(int $index): void
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function savePlan(): void
    {
        $validated = $this->validate([
            'planCategoryId' => ['required', 'exists:pricing_categories,id'],
            'planName' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'priceLabel' => ['required', 'string', 'max:255'],
            'periodLabel' => ['required', 'string', 'max:255'],
            'isHighlighted' => ['boolean'],
            'planSortOrder' => ['required', 'integer', 'min:0'],
            'features.*.feature' => ['required', 'string', 'max:255'],
        ]);

        $plan = $this->planId ? PricingPlan::findOrFail($this->planId) : new PricingPlan();
        $plan->fill([
            'pricing_category_id' => $validated['planCategoryId'],
            'name' => $validated['planName'],
            'tagline' => $validated['tagline'],
            'price_label' => $validated['priceLabel'],
            'period_label' => $validated['periodLabel'],
            'is_highlighted' => $validated['isHighlighted'],
            'sort_order' => $validated['planSortOrder'],
        ])->save();

        $plan->features()->delete();
        foreach (array_values($validated['features']) as $index => $row) {
            $plan->features()->create(['feature' => $row['feature'], 'sort_order' => $index]);
        }

        $this->dispatch('toast', message: 'Plan saved.');
        $this->dispatch('close-modal', name: 'plan-form');
        $this->newPlan($validated['planCategoryId']);
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->confirmingDeleteType = $type;
        $this->confirmingDeleteId = $id;
    }

    public function deleteConfirmed(): void
    {
        match ($this->confirmingDeleteType) {
            // Deleting a category cascades to its plans (migration's
            // cascadeOnDelete) - and each plan's own features cascade
            // from there too. One delete, three tables cleaned up.
            'category' => PricingCategory::findOrFail($this->confirmingDeleteId)->delete(),
            'plan' => PricingPlan::findOrFail($this->confirmingDeleteId)->delete(),
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