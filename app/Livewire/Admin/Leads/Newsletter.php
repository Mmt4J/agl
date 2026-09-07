<?php

namespace App\Livewire\Admin\Leads;

use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts::admin')]
#[Title('Newsletter')]
class Newsletter extends Component
{
    use WithPagination;

    public ?string $statusFilter = null;

    #[Computed]
    public function subscribers()
    {
        return NewsletterSubscriber::query()
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('subscribed_at')
            ->paginate(20);
    }

    public function filterByStatus(?string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    // Streams the full filtered result set (not just the current page)
    // directly to the browser as a CSV download. Returning a response
    // object from a Livewire action triggers a real navigation, so this
    // downloads a file instead of re-rendering the component.
    public function exportCsv(): StreamedResponse
    {
        $statusFilter = $this->statusFilter;

        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($statusFilter) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Email', 'Status', 'Subscribed']);

            NewsletterSubscriber::query()
                ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
                ->latest('subscribed_at')
                ->chunk(500, function ($chunk) use ($handle) {
                    foreach ($chunk as $subscriber) {
                        fputcsv($handle, [
                            $subscriber->email,
                            $subscriber->status,
                            optional($subscriber->subscribed_at)->format('M d, Y H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.leads.newsletter');
    }
}