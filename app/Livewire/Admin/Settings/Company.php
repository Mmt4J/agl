<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Company Settings')]
class Company extends Component
{
    public string $companyName = '';
    public string $rcNumber = '';
    public string $scumlNumber = '';
    public string $tin = '';
    public string $incorporatedAt = '';
    public string $address = '';
    public string $email = '';
    public string $phonePrimary = '';
    public string $phoneSecondary = '';
    public string $whatsappNumber = '';
    public string $whatsappDefaultMessage = '';

    public bool $justSaved = false;

    // Single source of truth for which Livewire property maps to which
    // settings.key row - used by both mount() (read) and save() (write).
    protected function keyMap(): array
    {
        return [
            'companyName' => 'company.name',
            'rcNumber' => 'company.rc_number',
            'scumlNumber' => 'company.scuml_number',
            'tin' => 'company.tin',
            'incorporatedAt' => 'company.incorporated_at',
            'address' => 'company.address',
            'email' => 'company.email',
            'phonePrimary' => 'company.phone_primary',
            'phoneSecondary' => 'company.phone_secondary',
            'whatsappNumber' => 'company.whatsapp_number',
            'whatsappDefaultMessage' => 'company.whatsapp_default_message',
        ];
    }

    public function mount(): void
    {
        foreach ($this->keyMap() as $property => $key) {
            $this->$property = Setting::get($key, '');
        }
    }

    public function save(): void
    {
        $this->justSaved = false;

        $validated = $this->validate([
            'companyName' => ['required', 'string', 'max:255'],
            'rcNumber' => ['required', 'string', 'max:50'],
            'scumlNumber' => ['nullable', 'string', 'max:50'],
            'tin' => ['nullable', 'string', 'max:50'],
            'incorporatedAt' => ['nullable', 'date'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phonePrimary' => ['required', 'string', 'max:30'],
            'phoneSecondary' => ['nullable', 'string', 'max:30'],
            'whatsappNumber' => ['nullable', 'string', 'max:30'],
            'whatsappDefaultMessage' => ['nullable', 'string'],
        ]);

        // Setting::get() caches forever, so every write here must also
        // bust that same cache key - otherwise the change is in the
        // database but every page reading via Setting::get() keeps
        // serving the stale cached value indefinitely.
        foreach ($this->keyMap() as $property => $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $validated[$property] ?? '', 'type' => 'string']
            );

            Cache::forget("setting.$key");
        }

        $this->justSaved = true;
    }

    public function render()
    {
        return view('livewire.admin.settings.company');
    }
}
