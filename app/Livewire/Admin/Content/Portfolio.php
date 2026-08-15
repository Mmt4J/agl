<?php

namespace App\Livewire\Admin\Content;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Portfolio')]
class Portfolio extends Component
{
    public function render()
    {
        return view('livewire.admin.content.portfolio');
    }
}
