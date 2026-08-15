<?php

namespace App\Livewire\Admin\Reference;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;


#[Layout('layouts.admin')]
#[Title('Devices & Industries')]
class DevicesIndustries extends Component
{
    public function render()
    {
        return view('livewire.admin.reference.devices-industries');
    }
}
