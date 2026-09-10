<?php

namespace App\Livewire\Admin\Measurements;

use App\Models\CustomerMeasurement;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Measurement Records')]
class Records extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $viewingRecordId = null;

    public ?int $confirmingDeleteId = null;

    #[Computed]
    public function records()
    {
        return CustomerMeasurement::query()
            ->when(trim($this->search) !== '', fn ($q) => $q->search($this->search))
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function viewingRecord()
    {
        return $this->viewingRecordId ? CustomerMeasurement::find($this->viewingRecordId) : null;
    }

    public function render()
    {
        return view('livewire.admin.measurements.records');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function viewRecord(CustomerMeasurement $record): void
    {
        $this->viewingRecordId = $record->id;

        $this->dispatch('open-modal', name: 'measurement-detail');
    }

    public function editRecord(?int $id = null): void
    {
        $id ??= $this->viewingRecordId;

        if (! $id) {
            return;
        }

        $this->redirectRoute('admin.measurements.record-measurements', ['record' => $id]);
    }

    public function confirmDelete(CustomerMeasurement $record): void
    {
        $this->confirmingDeleteId = $record->id;

        $this->dispatch('open-modal', name: 'confirm-delete');
    }

    public function deleteConfirmed(): void
    {
        CustomerMeasurement::findOrFail($this->confirmingDeleteId)->delete();
        $this->dispatch('toast', message: 'Record deleted.', type: 'danger');
        $this->dispatch('close-modal', name: 'confirm-delete');
        $this->confirmingDeleteId = null;
        $this->viewingRecordId = null;
    }

    #[On('modal-closed')]
    public function onModalClosed(string $name): void
    {
        if ($name === 'confirm-delete') {
            $this->confirmingDeleteId = null;
        }

        if ($name === 'measurement-detail') {
            $this->viewingRecordId = null;
        }
    }
}
