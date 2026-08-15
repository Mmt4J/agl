<?php

namespace App\Livewire\Admin\Content;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Services')]
class Services extends Component
{
    public function render()
    {
        return view('livewire.admin.content.services');
    }
}
