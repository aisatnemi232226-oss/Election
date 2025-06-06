@if ($paginator->hasPages())
    <div class="flex items-center justify-center px-6 -mt-4 bg-gray-50 space-x-8">
        <div>
            <p class="text-sm text-gray-600">
                Showing 
                <span class="font-medium" id="pagination-showing-start">{{ $paginator->firstItem() ?? 0 }}</span>
                to 
                <span class="font-medium" id="pagination-showing-end">{{ $paginator->lastItem() ?? 0 }}</span>
                of 
                <span class="font-medium" id="pagination-total">{{ $paginator->total() }}</span>
                results
            </p>
        </div>

        <div class="flex items-center space-x-1">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded-md cursor-not-allowed">
                    <span class="sr-only">Previous</span>
                    &lt;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100">
                    <span class="sr-only">Previous</span>
                    &lt;
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1 text-sm text-white bg-blue-600 border border-blue-600 rounded-md">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100">
                    <span class="sr-only">Next</span>
                    &gt;
                </a>
            @else
                <span class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded-md cursor-not-allowed">
                    <span class="sr-only">Next</span>
                    &gt;
                </span>
            @endif
        </div>
    </div>
@endif