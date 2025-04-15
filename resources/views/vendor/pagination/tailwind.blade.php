@if (isset($verifiedVoters) && $verifiedVoters->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between sm:flex-1 sm:items-center sm:justify-between">
        <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ $verifiedVoters->firstItem() }}</span>
            to <span class="font-medium">{{ $verifiedVoters->lastItem() }}</span>
            of <span class="font-medium">{{ $verifiedVoters->total() }}</span> results
        </div>

        <div class="flex gap-1">
            {{-- Previous Button --}}
            @if ($verifiedVoters->onFirstPage())
                <span class="px-3 py-2 text-gray-400 bg-gray-200 rounded-l-md cursor-default">&laquo;</span>
            @else
                <a href="{{ $verifiedVoters->previousPageUrl() }}" class="page-link px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-100">&laquo;</a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($verifiedVoters->links()->elements as $element)
                @if (is_string($element))
                    <span class="px-4 py-2 text-gray-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $verifiedVoters->currentPage())
                            <span class="px-4 py-2 bg-blue-600 text-white font-medium border border-blue-600 rounded-md">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($verifiedVoters->hasMorePages())
                <a href="{{ $verifiedVoters->nextPageUrl() }}" class="page-link px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-100">&raquo;</a>
            @else
                <span class="px-3 py-2 text-gray-400 bg-gray-200 rounded-r-md cursor-default">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
