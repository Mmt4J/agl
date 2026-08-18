<?php

namespace App\Livewire\Admin\Content;

use App\Models\Testimonial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Testimonials')]
class Testimonials extends Component
{
    public ?int $testimonialId = null;
    public string $clientName = '';
    public string $clientRole = '';
    public string $quote = '';
    public int $rating = 5;
    public bool $isApproved = false;
    public int $sortOrder = 0;

    public ?int $confirmingDeleteId = null;

    public function render()
    {
        return view('livewire.admin.content.testimonials', [
            'testimonials' => Testimonial::orderBy('sort_order')->get(),
        ]);
    }

    public function newTestimonial(): void
    {
        $this->reset('testimonialId', 'clientName', 'clientRole', 'quote', 'sortOrder');
        $this->rating = 5;
        $this->isApproved = false;
        $this->resetErrorBag();
    }

    public function editTestimonial(Testimonial $testimonial): void
    {
        $this->testimonialId = $testimonial->id;
        $this->clientName = $testimonial->client_name;
        $this->clientRole = $testimonial->client_role;
        $this->quote = $testimonial->quote;
        $this->rating = $testimonial->rating;
        $this->isApproved = $testimonial->is_approved;
        $this->sortOrder = $testimonial->sort_order;

        $this->dispatch('open-modal', name: 'testimonial-form');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'clientName' => ['required', 'string', 'max:255'],
            'clientRole' => ['required', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'isApproved' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $testimonial = $this->testimonialId ? Testimonial::findOrFail($this->testimonialId) : new Testimonial();
        $testimonial->fill([
            'client_name' => $validated['clientName'],
            'client_role' => $validated['clientRole'],
            'quote' => $validated['quote'],
            'rating' => $validated['rating'],
            'is_approved' => $validated['isApproved'],
            'sort_order' => $validated['sortOrder'],
        ])->save();

        $this->dispatch('toast', message: 'Testimonial saved.');
        $this->dispatch('close-modal', name: 'testimonial-form');
        $this->newTestimonial();
    }

    // Quick toggle from the list - approval status changes often enough
    // that a full modal round-trip for just this one flag would be friction.
    public function toggleApproved(Testimonial $testimonial): void
    {
        $testimonial->update(['is_approved' => ! $testimonial->is_approved]);
    }

    public function confirmDelete(Testimonial $testimonial): void
    {
        $this->confirmingDeleteId = $testimonial->id;
    
        $this->dispatch('open-modal', name: 'confirm-delete');
    }

    public function deleteConfirmed(): void
    {
        Testimonial::findOrFail($this->confirmingDeleteId)->delete();

        $this->dispatch('toast', message: 'Testimonial deleted.', type: 'danger');
        $this->dispatch('close-modal', name: 'confirm-delete');
        $this->confirmingDeleteId = null;
    }

    #[On('modal-closed')]
    public function onModalClosed(string $name): void
    {
        if ($name === 'confirm-delete') {
            $this->confirmingDeleteId = null;
        }
    }
}