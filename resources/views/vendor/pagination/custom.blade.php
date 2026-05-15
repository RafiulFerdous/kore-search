@if ($paginator->hasPages())
    <ul class="pagination-list">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="pagination-item disabled" aria-disabled="true">
                <span class="pagination-link">&laquo; Previous</span>
            </li>
        @else
            <li class="pagination-item">
                <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link pagination-dots">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="pagination-item active" aria-current="page">
                            <span class="pagination-link pagination-current">{{ $page }}</span>
                        </li>
                    @else
                        <li class="pagination-item">
                            <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="pagination-item">
                <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
            </li>
        @else
            <li class="pagination-item disabled" aria-disabled="true">
                <span class="pagination-link">Next &raquo;</span>
            </li>
        @endif
    </ul>
@endif
