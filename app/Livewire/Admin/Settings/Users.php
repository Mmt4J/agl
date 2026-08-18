<?php

namespace App\Livewire\Admin\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Users')]
class Users extends Component
{
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public string $role = 'editor';

    // Set right after creating a user, shown once in the modal, then
    // cleared - this is the ONLY time this password is ever visible again.
    public ?string $generatedPassword = null;

    // Which user's delete is pending confirmation in the popup modal -
    // null means no delete is in progress.
    public ?int $confirmingDeleteUserId = null;

    public function render()
    {
        return view('livewire.admin.settings.users', [
            'users' => User::orderBy('name')->paginate(10),
        ]);
    }

    public function createUser(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,editor'],
        ]);

        $password = Str::password(16);

        User::create([
            ...$validated,
            'password' => Hash::make($password),
        ]);

        $this->generatedPassword = $password;
        $this->reset('name', 'email', 'role');
    }

    public function closeCreateModal(): void
    {
        $this->reset('name', 'email', 'role', 'generatedPassword');
        $this->resetErrorBag();
    }

    public function updateRole(User $user, string $role): void
    {
        abort_if($user->id === auth()->id(), 403);

        $user->update(['role' => $role]);

        $this->dispatch('toast', message: "{$user->name}'s role updated.");
    }

    // Opens the confirmation popup for a specific user - doesn't delete
    // anything yet, just records WHICH user the modal is asking about.
    public function confirmDelete(User $user): void
    {
        abort_if($user->id === auth()->id(), 403);

        $this->confirmingDeleteUserId = $user->id;

        $this->dispatch('open-modal', name: 'confirm-delete-user');
    }

    // Actually deletes - only ever called from the modal's Confirm button,
    // never directly from the card, so there's always a confirmation step.
    public function deleteUser(): void
    {
        if (! $this->confirmingDeleteUserId) {
            return;
        }

        abort_if($this->confirmingDeleteUserId === auth()->id(), 403);

        User::findOrFail($this->confirmingDeleteUserId)->delete();

        $this->dispatch('toast', message: 'User removed.', type: 'danger');
        $this->dispatch('close-modal', name: 'confirm-delete-user');
        $this->confirmingDeleteUserId = null;
    }

    // Covers closing the modal via backdrop/Escape without confirming -
    // without this, confirmingDeleteUserId would stay set and the next
    // "Cancel" click would look like it did nothing.
    #[On('modal-closed')]
    public function onModalClosed(string $name): void
    {
        if ($name === 'confirm-delete-user') {
            $this->confirmingDeleteUserId = null;
        }
    }
}