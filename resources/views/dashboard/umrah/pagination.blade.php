@if($paginator->hasPages())
    <nav class="umrah-pagination no-print" aria-label="{{ __('umrah.page_navigation') }}">
        @if($paginator->onFirstPage())
            <span class="btn btn-outline-secondary disabled" aria-disabled="true">{{ __('umrah.previous_page') }}</span>
        @else
            <a class="btn btn-outline-primary" rel="prev"
                href="{{ $paginator->previousPageUrl() }}">{{ __('umrah.previous_page') }}</a>
        @endif
        <span
            class="">{{ __('umrah.page_position', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</span>
        @if($paginator->hasMorePages())
            <a class="btn btn-outline-primary" rel="next" href="{{ $paginator->nextPageUrl() }}">{{ __('umrah.next_page') }}</a>
        @else
            <span class="btn btn-outline-secondary disabled" aria-disabled="true">{{ __('umrah.next_page') }}</span>
        @endif
    </nav>
@endif