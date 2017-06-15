@if ($paginator->hasPages())
	<nav class="pagination">
	    <ul class="pagination-list">
	        {{-- Previous Page Link --}}
	        @if ($paginator->onFirstPage())
	            <a class="pagination-previous" title="This is the first page" disabled="">Previous</a>
	        @else
	            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link">&laquo;</a></li>
	        @endif

	        {{-- Pagination Elements --}}
	        @foreach ($elements as $element)
	            {{-- "Three Dots" Separator --}}
	            @if (is_string($element))
	            	<span class="pagination-ellipsis">…</span>
	            @endif

	            {{-- Array Of Links --}}
	            @if (is_array($element))
	                @foreach ($element as $page => $url)
	                    @if ($page == $paginator->currentPage())
	                        <li><a class="pagination-link is-current"><span>{{ $page }}</span></a></li>
	                    @else
	                        <li><a href="{{ $url }}" class="pagination-link">{{ $page }}</a></li>
	                    @endif
	                @endforeach
	            @endif
	        @endforeach

	        {{-- Next Page Link --}}
	        @if ($paginator->hasMorePages())
	            <a class="pagination-previous" rel="next" href="{{ $paginator->nextPageUrl() }}" >Next</a>
	        @else
	            <a class="pagination-previous" title="This is the first page" disabled="">Next</a>
	        @endif
	    </ul>
	</nav>
@endif
