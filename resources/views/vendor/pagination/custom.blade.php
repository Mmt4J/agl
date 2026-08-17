@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <p class="text-sm text-ink-900/50 dark:text-linen-100/50">
            {{ __('Showing') }} {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} {{ __('of') }} {{ $paginator->total() }}
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 grid place-items-center rounded-md text-ink-900/30 dark:text-linen-100/30 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="w-9 h-9 grid place-items-center rounded-md text-ink-900 dark:text-linen-100 hover:bg-ink-900/5 dark:hover:bg-linen-100/10">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-9 h-9 grid place-items-center text-sm text-ink-900/40 dark:text-linen-100/40">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 grid place-items-center rounded-md text-sm font-semibold bg-copper-500 text-ink-950">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="w-9 h-9 grid place-items-center rounded-md text-sm text-ink-900 dark:text-linen-100 hover:bg-ink-900/5 dark:hover:bg-linen-100/10">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="w-9 h-9 grid place-items-center rounded-md text-ink-900 dark:text-linen-100 hover:bg-ink-900/5 dark:hover:bg-linen-100/10">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
            @else
                <span class="w-9 h-9 grid place-items-center rounded-md text-ink-900/30 dark:text-linen-100/30 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif