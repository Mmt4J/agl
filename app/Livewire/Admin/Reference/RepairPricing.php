<?php

namespace App\Livewire\Admin\Reference;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Repair Pricing')]
class RepairPricing extends Component
{
    public function render()
    {
        return view('livewire.admin.reference.repair-pricing');
    }
}
