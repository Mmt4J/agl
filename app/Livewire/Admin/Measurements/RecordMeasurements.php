<?php

namespace App\Livewire\Admin\Measurements;

use App\Models\CustomerMeasurement;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Record Measurements')]
class RecordMeasurements extends Component
{
    // When set, the form edits this existing record instead of creating.
    public ?int $recordId = null;

    public string $findCustomer = '';

    public string $customerCode = '';

    public string $fullName = '';

    public string $phone = '';

    public string $email = '';

    public string $unit = 'in';

    // Upper body
    public ?float $chest = null;

    public ?float $bust = null;

    public ?float $waist = null;

    public ?float $shoulder = null;

    public ?float $armLength = null;

    public ?float $topLength = null;

    public ?float $halfBust = null;

    public ?float $halfLength = null;

    public ?float $roundSleeve = null;

    public ?float $lengthSleeve = null;

    // Lower body
    public ?float $hip = null;

    public ?float $inseam = null;

    public ?float $thigh = null;

    public ?float $ankle = null;

    public ?float $trouserSkirtLength = null;

    // Overall
    public ?float $height = null;

    public ?float $weight = null;

    public ?float $gownLength = null;

    public string $dressSize = '';

    public string $notes = '';

    public function render()
    {
        return view('livewire.admin.measurements.record-measurements');
    }

    public function mount(?CustomerMeasurement $record = null): void
    {
        if ($record) {
            $this->loadRecord($record);
        }
    }

    /** Live list of existing customers while the staff searches, like global search. */
    #[Computed]
    public function lookupResults(): Collection
    {
        if (mb_strlen(trim($this->findCustomer)) < 2) {
            return collect();
        }

        return CustomerMeasurement::search($this->findCustomer)
            ->orderBy('customer_code')
            ->limit(6)
            ->get();
    }

    public function selectCustomer(int $customerMeasurementId): void
    {
        $this->loadRecord(CustomerMeasurement::findOrFail($customerMeasurementId));
        $this->findCustomer = '';
    }

    public function startNew(): void
    {
        $this->reset(
            'recordId',
            'customerCode',
            'fullName',
            'phone',
            'email',
            'chest',
            'bust',
            'waist',
            'shoulder',
            'armLength',
            'topLength',
            'halfBust',
            'halfLength',
            'roundSleeve',
            'lengthSleeve',
            'hip',
            'inseam',
            'thigh',
            'ankle',
            'trouserSkirtLength',
            'height',
            'weight',
            'gownLength',
            'dressSize',
            'notes'
        );
        $this->unit = 'in';
        $this->findCustomer = '';
        $this->resetErrorBag();
    }

    public function switchUnit(string $unit): void
    {
        if (in_array($unit, ['in', 'cm'], true)) {
            $this->unit = $unit;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'unit' => ['required', 'in:in,cm'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'bust' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'shoulder' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'armLength' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'topLength' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'halfBust' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'halfLength' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'roundSleeve' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'lengthSleeve' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'hip' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'inseam' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'thigh' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'ankle' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'trouserSkirtLength' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'gownLength' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'dressSize' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);

        $record = $this->recordId ? CustomerMeasurement::findOrFail($this->recordId) : new CustomerMeasurement;
        $record->fill([
            'full_name' => $validated['fullName'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?: null,
            'unit' => $validated['unit'],
            'chest' => $validated['chest'],
            'bust' => $validated['bust'],
            'waist' => $validated['waist'],
            'shoulder' => $validated['shoulder'],
            'arm_length' => $validated['armLength'],
            'top_length' => $validated['topLength'],
            'half_bust' => $validated['halfBust'],
            'half_length' => $validated['halfLength'],
            'round_sleeve' => $validated['roundSleeve'],
            'length_sleeve' => $validated['lengthSleeve'],
            'hip' => $validated['hip'],
            'inseam' => $validated['inseam'],
            'thigh' => $validated['thigh'],
            'ankle' => $validated['ankle'],
            'trouser_skirt_length' => $validated['trouserSkirtLength'],
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'gown_length' => $validated['gownLength'],
            'dress_size' => $validated['dressSize'] ?: null,
            'notes' => $validated['notes'] ?: null,
        ])->save();

        if (! $record->customer_code) {
            $record->update([
                'customer_code' => CustomerMeasurement::CODE_PREFIX.str_pad((string) $record->id, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $this->loadRecord($record->fresh());
        $this->dispatch('toast', message: 'Measurements saved.');
    }

    private function loadRecord(CustomerMeasurement $record): void
    {
        $this->recordId = $record->id;
        $this->customerCode = $record->customer_code ?? '';
        $this->fullName = $record->full_name;
        $this->phone = $record->phone;
        $this->email = (string) $record->email;
        $this->unit = $record->unit;
        $this->chest = $record->chest;
        $this->bust = $record->bust;
        $this->waist = $record->waist;
        $this->shoulder = $record->shoulder;
        $this->armLength = $record->arm_length;
        $this->topLength = $record->top_length;
        $this->halfBust = $record->half_bust;
        $this->halfLength = $record->half_length;
        $this->roundSleeve = $record->round_sleeve;
        $this->lengthSleeve = $record->length_sleeve;
        $this->hip = $record->hip;
        $this->inseam = $record->inseam;
        $this->thigh = $record->thigh;
        $this->ankle = $record->ankle;
        $this->trouserSkirtLength = $record->trouser_skirt_length;
        $this->height = $record->height;
        $this->weight = $record->weight;
        $this->gownLength = $record->gown_length;
        $this->dressSize = (string) $record->dress_size;
        $this->notes = (string) $record->notes;
    }
}
