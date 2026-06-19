@if ($paginator->hasPages())
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pager-btn disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="pager-btn current">{{ $page }}</span>
                @else
                    <button class="pager-btn" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ $page }}</button>
                @endif
            @endforeach
        @endif
    @endforeach
    @if ($paginator->onFirstPage())
        <span class="pager-btn disabled">‹</span>
    @else
        <button class="pager-btn" wire:click="previousPage('{{ $paginator->getPageName() }}')">‹</button>
    @endif
    @if ($paginator->hasMorePages())
        <button class="pager-btn" wire:click="nextPage('{{ $paginator->getPageName() }}')">›</button>
    @else
        <span class="pager-btn disabled">›</span>
    @endif
@endif
