@if ($paginator->hasPages())
	<nav class="flex-space margin-bottom-1">
		@if ($paginator->onFirstPage())
			<a class="v-button display-hidden">Previous</a>
	    @else
			<a class="v-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous Page</a>
	    @endif

	    {{-- Next Page Link --}}
	    @if ($paginator->hasMorePages())
	        <a class="v-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Next Page</a>
	    @else
	        <a class="v-button display-hidden">Next Page</a>
	    @endif
    </nav>
@endif
