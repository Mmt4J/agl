<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts::admin')]
#[Title('Overview')]
class Overview extends Component
{
    public function render()
    {
        return view('livewire.admin.overview');
    }
}
