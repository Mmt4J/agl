<?php

namespace App\Livewire\Admin\Leads;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts::admin')]
#[Title('Quote Requests')]
class QuoteRequests extends Component
{
    public function render()
    {
        return view('livewire.admin.leads/quote-requests');
    }
}
