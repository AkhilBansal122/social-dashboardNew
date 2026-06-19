@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <span class="pager-btn disabled">‹ Prev</span>
    @else
        <button class="pager-btn" wire:click="previousPage">‹ Prev</button>
    @endif
    @if ($paginator->hasMorePages())
        <button class="pager-btn" wire:click="nextPage">Next ›</button>
    @else
        <span class="pager-btn disabled">Next ›</span>
    @endif
@endif
