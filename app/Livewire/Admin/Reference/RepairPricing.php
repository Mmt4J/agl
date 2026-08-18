<?php

namespace App\Livewire\Admin\Reference;

use App\Models\RepairDeviceType;
use App\Models\RepairIssueType;
use App\Models\RepairPricing as RepairPricingModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Repair Pricing')]
class RepairPricing extends Component
{
    // Lookup form state (device types / issue types) - simple, same
    // pattern as DevicesIndustries, just two more small lists.
    public ?int $deviceTypeId = null;
    public string $deviceTypeName = '';

    public ?int $issueTypeId = null;
    public string $issueTypeName = '';

    // The matrix cell currently being edited - identified by BOTH ids,
    // since a cell only exists where a repair_pricing row exists for
    // that exact device/issue pair.
    public ?int $cellDeviceTypeId = null;
    public ?int $cellIssueTypeId = null;
    public string $priceMin = '';
    public string $priceMax = '';

    public ?string $confirmingDeleteType = null;
    public ?int $confirmingDeleteId = null;

    public function render()
    {
        $deviceTypes = RepairDeviceType::orderBy('sort_order')->get();
        $issueTypes = RepairIssueType::orderBy('sort_order')->get();

        // Flatten existing prices into [deviceTypeId][issueTypeId] => price
        // row, so the view can do a simple isset() check per cell instead
        // of an N+1 query per cell.
        $matrix = RepairPricingModel::all()
            ->groupBy('repair_device_type_id')
            ->map(fn ($rows) => $rows->keyBy('repair_issue_type_id'));

        return view('livewire.admin.reference.repair-pricing', [
            'deviceTypes' => $deviceTypes,
            'issueTypes' => $issueTypes,
            'matrix' => $matrix,
        ]);
    }

    public function newDeviceType(): void
    {
        $this->reset('deviceTypeId', 'deviceTypeName');
        $this->resetErrorBag();
    }

    public function editDeviceType(RepairDeviceType $type): void
    {
        $this->deviceTypeId = $type->id;
        $this->deviceTypeName = $type->name;

        $this->dispatch('open-modal', name: 'device-type-form');
    }

    public function saveDeviceType(): void
    {
        $validated = $this->validate(['deviceTypeName' => ['required', 'string', 'max:255']]);

        $type = $this->deviceTypeId ? RepairDeviceType::findOrFail($this->deviceTypeId) : new RepairDeviceType();
        $type->name = $validated['deviceTypeName'];
        $type->save();

        $this->dispatch('toast', message: 'Device type saved.');
        $this->dispatch('close-modal', name: 'device-type-form');
        $this->newDeviceType();
    }

    public function newIssueType(): void
    {
        $this->reset('issueTypeId', 'issueTypeName');
        $this->resetErrorBag();
    }

    public function editIssueType(RepairIssueType $type): void
    {
        $this->issueTypeId = $type->id;
        $this->issueTypeName = $type->name;

        $this->dispatch('open-modal', name: 'issue-type-form');
    }

    public function saveIssueType(): void
    {
        $validated = $this->validate(['issueTypeName' => ['required', 'string', 'max:255']]);

        $type = $this->issueTypeId ? RepairIssueType::findOrFail($this->issueTypeId) : new RepairIssueType();
        $type->name = $validated['issueTypeName'];
        $type->save();

        $this->dispatch('toast', message: 'Issue type saved.');
        $this->dispatch('close-modal', name: 'issue-type-form');
        $this->newIssueType();
    }

    // Opens the price editor for one cell. Pre-fills from an existing row
    // if one exists for this exact pair, otherwise starts blank - same
    // form handles both "add a price" and "edit a price".
    public function editCell(int $deviceTypeId, int $issueTypeId): void
    {
        $this->cellDeviceTypeId = $deviceTypeId;
        $this->cellIssueTypeId = $issueTypeId;

        $existing = RepairPricingModel::where('repair_device_type_id', $deviceTypeId)
            ->where('repair_issue_type_id', $issueTypeId)
            ->first();

        $this->priceMin = $existing?->price_min ?? '';
        $this->priceMax = $existing?->price_max ?? '';

        // Opening the modal HERE, after the data above is set, is the
        // actual fix - guarantees the form never renders before its
        // values are ready, unlike an instant client-side open would.
        $this->dispatch('open-modal', name: 'cell-form');
    }

    public function saveCell(): void
    {
        $validated = $this->validate([
            'priceMin' => ['required', 'integer', 'min:0'],
            'priceMax' => ['required', 'integer', 'gte:priceMin'],
        ]);

        RepairPricingModel::updateOrCreate(
            [
                'repair_device_type_id' => $this->cellDeviceTypeId,
                'repair_issue_type_id' => $this->cellIssueTypeId,
            ],
            [
                'price_min' => $validated['priceMin'],
                'price_max' => $validated['priceMax'],
            ]
        );

        $this->dispatch('toast', message: 'Price saved.');
        $this->dispatch('close-modal', name: 'cell-form');
        $this->reset('cellDeviceTypeId', 'cellIssueTypeId', 'priceMin', 'priceMax');
    }

    // Clears a cell back to "not a common combination" - deletes the
    // row entirely rather than storing a zero/empty price.
    public function clearCell(): void
    {
        RepairPricingModel::where('repair_device_type_id', $this->cellDeviceTypeId)
            ->where('repair_issue_type_id', $this->cellIssueTypeId)
            ->delete();

        $this->dispatch('toast', message: 'Price cleared.', type: 'danger');
        $this->dispatch('close-modal', name: 'cell-form');
        $this->reset('cellDeviceTypeId', 'cellIssueTypeId', 'priceMin', 'priceMax');
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
            'device-type' => RepairDeviceType::findOrFail($this->confirmingDeleteId)->delete(),
            'issue-type' => RepairIssueType::findOrFail($this->confirmingDeleteId)->delete(),
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

        if ($name === 'cell-form') {
            $this->reset('cellDeviceTypeId', 'cellIssueTypeId', 'priceMin', 'priceMax');
        }
    }
}