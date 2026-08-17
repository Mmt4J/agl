<?php

namespace App\Livewire\Admin\Reference;

use App\Models\Device;
use App\Models\Industry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Devices & Industries')]
class DevicesIndustries extends Component
{
    // Device form state - null deviceId means "creating new"
    public ?int $deviceId = null;
    public string $deviceName = '';
    public string $deviceExamples = '';
    public string $deviceIcon = '';
    public int $deviceSortOrder = 0;

    // Industry form state - same pattern, separate properties
    public ?int $industryId = null;
    public string $industryName = '';
    public string $industryDescription = '';
    public int $industrySortOrder = 0;

    // One shared delete-confirmation modal for both lists, discriminated by type
    public ?string $confirmingDeleteType = null;
    public ?int $confirmingDeleteId = null;

    public function render()
    {
        return view('livewire.admin.reference.devices-industries', [
            'devices' => Device::orderBy('sort_order')->get(),
            'industries' => Industry::orderBy('sort_order')->get(),
        ]);
    }

    public function newDevice(): void
    {
        $this->reset('deviceId', 'deviceName', 'deviceExamples', 'deviceIcon', 'deviceSortOrder');
        $this->resetErrorBag();
    }

    public function editDevice(Device $device): void
    {
        $this->deviceId = $device->id;
        $this->deviceName = $device->name;
        $this->deviceExamples = $device->examples;
        $this->deviceIcon = $device->icon;
        $this->deviceSortOrder = $device->sort_order;

        $this->dispatch('open-modal', name: 'device-form');
    }

    public function saveDevice(): void
    {
        $validated = $this->validate([
            'deviceName' => ['required', 'string', 'max:255'],
            'deviceExamples' => ['required', 'string', 'max:255'],
            'deviceIcon' => ['required', 'string', 'max:100'],
            'deviceSortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $device = $this->deviceId ? Device::findOrFail($this->deviceId) : new Device();
        $device->fill([
            'name' => $validated['deviceName'],
            'examples' => $validated['deviceExamples'],
            'icon' => $validated['deviceIcon'],
            'sort_order' => $validated['deviceSortOrder'],
        ])->save();

        $this->dispatch('toast', message: 'Device saved.');
        $this->dispatch('close-modal', name: 'device-form');
        $this->newDevice();
    }

    public function newIndustry(): void
    {
        $this->reset('industryId', 'industryName', 'industryDescription', 'industrySortOrder');
        $this->resetErrorBag();
    }

    public function editIndustry(Industry $industry): void
    {
        $this->industryId = $industry->id;
        $this->industryName = $industry->name;
        $this->industryDescription = $industry->description;
        $this->industrySortOrder = $industry->sort_order;

        $this->dispatch('open-modal', name: 'industry-form');
    }

    public function saveIndustry(): void
    {
        $validated = $this->validate([
            'industryName' => ['required', 'string', 'max:255'],
            'industryDescription' => ['required', 'string', 'max:255'],
            'industrySortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $industry = $this->industryId ? Industry::findOrFail($this->industryId) : new Industry();
        $industry->fill([
            'name' => $validated['industryName'],
            'description' => $validated['industryDescription'],
            'sort_order' => $validated['industrySortOrder'],
        ])->save();

        $this->dispatch('toast', message: 'Industry saved.');
        $this->dispatch('close-modal', name: 'industry-form');
        $this->newIndustry();
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->confirmingDeleteType = $type;
        $this->confirmingDeleteId = $id;
    }

    public function deleteConfirmed(): void
    {
        match ($this->confirmingDeleteType) {
            'device' => Device::findOrFail($this->confirmingDeleteId)->delete(),
            'industry' => Industry::findOrFail($this->confirmingDeleteId)->delete(),
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