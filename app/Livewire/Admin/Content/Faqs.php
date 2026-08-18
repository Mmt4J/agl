<?php

namespace App\Livewire\Admin\Content;

use App\Models\Faq;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('FAQs')]
class Faqs extends Component
{
    public ?int $faqId = null;
    public string $question = '';
    public string $answer = '';
    public bool $isActive = true;
    public int $sortOrder = 0;

    public ?int $confirmingDeleteId = null;

    public function render()
    {
        return view('livewire.admin.content.faqs', [
            'faqs' => Faq::orderBy('sort_order')->get(),
        ]);
    }

    public function newFaq(): void
    {
        $this->reset('faqId', 'question', 'answer', 'sortOrder');
        $this->isActive = true;
        $this->resetErrorBag();
    }

    public function editFaq(Faq $faq): void
    {
        $this->faqId = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->isActive = $faq->is_active;
        $this->sortOrder = $faq->sort_order;

        $this->dispatch('open-modal', name: 'faq-form');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'isActive' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $faq = $this->faqId ? Faq::findOrFail($this->faqId) : new Faq();
        $faq->fill([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'is_active' => $validated['isActive'],
            'sort_order' => $validated['sortOrder'],
        ])->save();

        $this->dispatch('toast', message: 'FAQ saved.');
        $this->dispatch('close-modal', name: 'faq-form');
        $this->newFaq();
    }

    // Quick toggle straight from the list - doesn't need the full modal
    // for something this small (one boolean flip).
    public function toggleActive(Faq $faq): void
    {
        $faq->update(['is_active' => ! $faq->is_active]);
    }

    public function confirmDelete(Faq $faq): void
    {
        $this->confirmingDeleteId = $faq->id;

        $this->dispatch('open-modal', name: 'confirm-delete');
    }

    public function deleteConfirmed(): void
    {
        Faq::findOrFail($this->confirmingDeleteId)->delete();
        $this->dispatch('toast', message: 'FAQ deleted.', type: 'danger');
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