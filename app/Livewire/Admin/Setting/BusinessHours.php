<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Business Hours')]
class BusinessHours extends Component
{
    public function render()
    {
        return view('livewire.admin.setting.business-hours');
    }
}
