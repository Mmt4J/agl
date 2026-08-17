<?php

namespace App\Livewire\Admin\Content;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Services')]
class Services extends Component
{
    public ?int $serviceId = null;
    public string $code = '';
    public string $name = '';
    public string $slug = '';
    public string $shortDescription = '';
    public string $blurb = '';
    public string $description = '';
    public string $icon = '';
    public bool $isFeatured = true;
    public int $sortOrder = 0;

    // Repeater: each row is ['feature' => string]. Array index at save
    // time becomes the new sort_order - no separate field needed, the
    // row's position IS its order.
    public array $features = [];

    public ?int $confirmingDeleteId = null;

    public function render()
    {
        return view('livewire.admin.content.services', [
            'services' => Service::ordered()->withCount('features')->get(),
        ]);
    }

    public function newService(): void
    {
        $this->reset('serviceId', 'code', 'name', 'slug', 'shortDescription', 'blurb', 'description', 'icon', 'sortOrder');
        $this->isFeatured = true;
        $this->features = [];
        $this->resetErrorBag();
    }

    public function editService(Service $service): void
    {
        $this->serviceId = $service->id;
        $this->code = $service->code;
        $this->name = $service->name;
        $this->slug = $service->slug;
        $this->shortDescription = $service->short_description;
        $this->blurb = $service->blurb;
        $this->description = $service->description;
        $this->icon = $service->icon;
        $this->isFeatured = $service->is_featured;
        $this->sortOrder = $service->sort_order;

        $this->features = $service->features()
            ->orderBy('sort_order')
            ->pluck('feature')
            ->map(fn ($feature) => ['feature' => $feature])
            ->all();

        $this->dispatch('open-modal', name: 'service-form');
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

    public function save(): void
    {
        $validated = $this->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('services', 'code')->ignore($this->serviceId)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($this->serviceId)],
            'shortDescription' => ['required', 'string', 'max:255'],
            'blurb' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'icon' => ['required', 'string', 'max:100'],
            'isFeatured' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'features.*.feature' => ['required', 'string', 'max:255'],
        ]);

        $service = $this->serviceId ? Service::findOrFail($this->serviceId) : new Service();
        $service->fill([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'short_description' => $validated['shortDescription'],
            'blurb' => $validated['blurb'],
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'is_featured' => $validated['isFeatured'],
            'sort_order' => $validated['sortOrder'],
        ])->save();

        // Simplest correct way to sync a repeater: wipe the old rows,
        // insert the current set fresh. For a handful of short feature
        // strings per service, this is cheaper to reason about than
        // diffing which rows changed/moved/were removed.
        $service->features()->delete();
        foreach (array_values($validated['features']) as $index => $row) {
            $service->features()->create([
                'feature' => $row['feature'],
                'sort_order' => $index,
            ]);
        }

        $this->dispatch('toast', message: 'Service saved.');
        $this->dispatch('close-modal', name: 'service-form');
        $this->newService();
    }

    public function confirmDelete(Service $service): void
    {
        $this->confirmingDeleteId = $service->id;
    }

    public function deleteConfirmed(): void
    {
        // SoftDeletes on Service - this hides it, doesn't erase it.
        // Existing quote_requests referencing it keep working since the
        // foreign key uses nullOnDelete, not a hard cascade.
        Service::findOrFail($this->confirmingDeleteId)->delete();

        $this->dispatch('toast', message: 'Service deleted.', type: 'danger');
        $this->dispatch('close-modal', name: 'confirm-delete');
        $this->confirmingDeleteId = null;
    }

    #[On('modal-closed')]
    public function onModalClosed(string $name): void
    {
        if ($name === 'confirm-delete') {
            $this->confirmingDeleteId = null;
        }
    }
}