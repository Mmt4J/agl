<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-ink-900/60 dark:text-linen-100/60">Staff accounts with back-office access.</p>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-md border border-ink-900/15 dark:border-linen-100/15 text-ink-900/40 dark:text-linen-100/40 font-semibold px-4 py-2 text-sm cursor-not-allowed">
                Invite <span class="font-mono text-[10px]">(coming soon)</span>
            </span>

            <x-forms.button
                type="button"
                variant="primary"
                @click="$dispatch('open-modal', { name: 'create-user' })"
            >
                Create user
            </x-forms.button>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        @foreach ($users as $user)
            @php $isSelf = $user->id === auth()->id(); @endphp

            <div wire:key="user-{{ $user->id }}" class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-4 flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-ink-900 dark:bg-copper-500 text-linen-50 dark:text-ink-950 grid place-items-center font-display font-semibold text-sm shrink-0">
                    {{ $user->initials() }}
                </span>

                <div class="min-w-0 flex-1">
                    <p class="font-medium text-sm truncate">
                        {{ $user->name }}
                        @if ($isSelf) <span class="text-ink-900/40 dark:text-linen-100/40 font-normal">(you)</span> @endif
                    </p>
                    <p class="font-mono text-xs text-ink-900/50 dark:text-linen-100/50 truncate">{{ $user->email }}</p>
                </div>

                @if ($isSelf)
                    <span class="font-mono text-[10px] px-2 py-1 rounded-full capitalize shrink-0 bg-copper-500/15 text-copper-600 dark:text-copper-300">
                        {{ $user->role }}
                    </span>
                @else
                    <select
                        wire:change="updateRole({{ $user->id }}, $event.target.value)"
                        class="font-mono text-[10px] rounded-full capitalize shrink-0 border-none bg-ink-900/8 dark:bg-linen-100/10 text-ink-900/70 dark:text-linen-100/70 py-1 pl-2 pr-6 focus:outline-none focus:ring-2 focus:ring-copper-500"
                    >
                        <option value="editor" @selected($user->role === 'editor')>editor</option>
                        <option value="admin" @selected($user->role === 'admin')>admin</option>
                    </select>

                    <button
                        type="button"
                        wire:click="confirmDelete({{ $user->id }})"
                        @click="$dispatch('open-modal', { name: 'confirm-delete-user' })"
                        class="text-danger-500 hover:text-danger-600 shrink-0"
                        aria-label="Remove {{ $user->name }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Uses vendor/pagination/custom.blade.php (set as the app default
         in AppServiceProvider) so this matches the palette instead of
         Laravel's default Tailwind-blue pagination styling. --}}
    {{ $users->links() }}

    <x-forms.modal name="create-user">
        @if ($generatedPassword)
            <div class="space-y-4">
                <div>
                    <h2 class="font-display text-lg font-semibold text-ink-950 dark:text-linen-50">User created</h2>
                    <p class="text-sm text-ink-600 dark:text-linen-300 mt-1">
                        Share this password with them directly - it won't be shown again.
                    </p>
                </div>

                <div
                    x-data="{
                        copied: false,
                        async copy() {
                            await navigator.clipboard.writeText('{{ $generatedPassword }}');
                            this.copied = true;
                            setTimeout(() => this.copied = false, 1500);
                        },
                    }"
                    class="flex items-stretch border rounded-lg border-ink-200 dark:border-ink-700"
                >
                    <input type="text" readonly value="{{ $generatedPassword }}" class="w-full p-3 bg-transparent outline-none font-mono text-sm text-ink-900 dark:text-linen-100" />
                    <button @click="copy()" type="button" class="px-3 border-l border-ink-200 dark:border-ink-700">
                        <span x-show="!copied" class="text-xs text-ink-600 dark:text-linen-300">Copy</span>
                        <span x-show="copied" x-cloak class="text-xs text-sage-500">Copied</span>
                    </button>
                </div>

                <x-forms.button type="button" variant="primary" class="w-full" wire:click="closeCreateModal" @click="close()">
                    Done
                </x-forms.button>
            </div>
        @else
            <form wire:submit="createUser" class="space-y-6">
                <h2 class="font-display text-lg font-semibold text-ink-950 dark:text-linen-50">Create user</h2>

                <x-forms.input wire:model="fullName" name="fullName" label="Full name" type="text" required autofocus />
                <x-forms.input wire:model="email" name="email" label="Email" type="email" required />

                <div class="flex flex-col gap-1.5">
                    <label for="role" class="text-sm font-medium text-ink-800 dark:text-linen-100">Role</label>
                    <select wire:model="role" id="role" class="w-full rounded-md border px-3 py-2 text-sm bg-white dark:bg-ink-900 border-ink-200 dark:border-ink-700 capitalize focus:outline-none focus:ring-2 focus:ring-copper-400">
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <x-forms.button type="button" variant="secondary" class="flex-1" wire:click="closeCreateModal" @click="close()">Cancel</x-forms.button>
                    <x-forms.button type="submit" variant="primary" class="flex-1">Create</x-forms.button>
                </div>
            </form>
        @endif
    </x-forms.modal>

    <x-forms.modal name="confirm-delete-user">
        <div class="space-y-6">
            <div>
                <h2 class="font-display text-lg font-semibold text-ink-950 dark:text-linen-50">Remove access?</h2>
                <p class="text-sm text-ink-600 dark:text-linen-300 mt-1">
                    This removes their account entirely and cannot be undone.
                </p>
            </div>

            <div class="flex gap-3">
                <x-forms.button type="button" variant="secondary" class="flex-1" wire:click="$set('confirmingDeleteUserId', null)" @click="close()">
                    Cancel
                </x-forms.button>
                <x-forms.button type="button" variant="danger" class="flex-1" wire:click="deleteUser">
                    Remove
                </x-forms.button>
            </div>
        </div>
    </x-forms.modal>
</div>