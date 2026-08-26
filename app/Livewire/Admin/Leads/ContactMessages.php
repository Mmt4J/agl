<?php

namespace App\Livewire\Admin\Leads;

use App\Models\ContactMessage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Contact Messages')]
class ContactMessages extends Component
{
    use WithPagination;

    // null = "All" - matches the prototype's filter chips exactly.
    public ?string $statusFilter = null;

    public ?int $viewingMessageId = null;
    public string $viewingStatus = '';

    #[Computed]
    public function messages()
    {
        return ContactMessage::query()
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(12);
    }

    // Kept separate from the paginated/filtered $this->messages list on
    // purpose - the panel needs to keep showing the record you opened
    // even after its status change makes it drop off the current
    // filter/page, right up until the panel actually closes.
    #[Computed]
    public function viewingMessage()
    {
        return $this->viewingMessageId ? ContactMessage::find($this->viewingMessageId) : null;
    }

    public function render()
    {
        return view('livewire.admin.leads.contact-messages');
    }

    public function filterByStatus(?string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function viewMessage(ContactMessage $message): void
    {
        $this->viewingMessageId = $message->id;
        $this->viewingStatus = $message->status;

        $this->dispatch('open-modal', name: 'message-detail');
    }

    public function updateStatus(): void
    {
        $this->validate([
            'viewingStatus' => ['required', 'in:unread,read,replied'],
        ]);

        $this->viewingMessage?->update(['status' => $this->viewingStatus]);

        $this->dispatch('toast', message: 'Message updated.');
        $this->dispatch('close-modal', name: 'message-detail');
    }
}