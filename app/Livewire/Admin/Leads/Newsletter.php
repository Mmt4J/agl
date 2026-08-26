<?php

namespace App\Livewire\Admin\Leads;

use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Newsletter')]
class Newsletter extends Component
{
    #[Computed]
    public function subscribers()
    {
        return NewsletterSubscriber::query()
            ->latest('subscribed_at')
            ->get()
            ->map(fn ($subscriber) => [
                'id' => $subscriber->id,
                'email' => $subscriber->email,
                'status' => $subscriber->status,
                'subscribedAt' => optional($subscriber->subscribed_at)->format('M d, Y H:i'),
            ])
            ->values();
    }

    public function render()
    {
        return view('livewire.admin.leads.newsletter');
    }
}