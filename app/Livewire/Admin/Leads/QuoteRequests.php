<?php

namespace App\Livewire\Admin\Leads;

use App\Models\QuoteRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Quote Requests')]
class QuoteRequests extends Component
{
    use WithPagination;

    public ?string $statusFilter = null;

    public ?int $viewingRequestId = null;
    public string $viewingStatus = '';
    public string $viewingNotes = '';

    #[Computed]
    public function requests()
    {
        return QuoteRequest::with('service')
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(12);
    }

    // Separate from the paginated/filtered list, same reasoning as
    // Contact Messages: the panel keeps showing its record even after
    // a status change makes it drop off the current filter/page.
    #[Computed]
    public function viewingRequest()
    {
        return $this->viewingRequestId ? QuoteRequest::with('service')->find($this->viewingRequestId) : null;
    }

    public function render()
    {
        return view('livewire.admin.leads.quote-requests');
    }

    public function filterByStatus(?string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function viewRequest(QuoteRequest $request): void
    {
        $this->viewingRequestId = $request->id;
        $this->viewingStatus = $request->status;
        $this->viewingNotes = $request->internal_notes ?? '';

        $this->dispatch('open-modal', name: 'quote-detail');
    }

    public function saveRequest(): void
    {
        $this->validate([
            'viewingStatus' => ['required', 'in:new,contacted,quoted,won,lost'],
            'viewingNotes' => ['nullable', 'string'],
        ]);

        $this->viewingRequest?->update([
            'status' => $this->viewingStatus,
            'internal_notes' => $this->viewingNotes ?: null,
        ]);

        $this->dispatch('toast', message: 'Quote request updated.');
        $this->dispatch('close-modal', name: 'quote-detail');
    }
}