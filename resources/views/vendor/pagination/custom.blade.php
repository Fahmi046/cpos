@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4">
        <ul class="inline-flex items-center -space-x-px">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-2 ml-0 leading-tight text-gray-400 bg-gray-200 rounded-l-lg cursor-not-allowed">
                        Previous
                    </span>
                </li>
            @else
                <li>
                    <button wire:click="gotoPage({{ $paginator->currentPage() - 1 }})"
                        class="px-3 py-2 ml-0 leading-tight text-white bg-indigo-600 rounded-l-lg hover:bg-indigo-700">
                        Previous
                    </button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="px-3 py-2 leading-tight text-gray-400 bg-gray-200">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span
                                    class="px-3 py-2 text-white bg-indigo-700 border border-indigo-600">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <button wire:click="gotoPage({{ $page }})"
                                    class="px-3 py-2 leading-tight text-indigo-600 bg-white border border-gray-300 hover:bg-indigo-100">
                                    {{ $page }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <button wire:click="gotoPage({{ $paginator->currentPage() + 1 }})"
                        class="px-3 py-2 leading-tight text-white bg-indigo-600 rounded-r-lg hover:bg-indigo-700">
                        Next
                    </button>
                </li>
            @else
                <li>
                    <span class="px-3 py-2 leading-tight text-gray-400 bg-gray-200 rounded-r-lg cursor-not-allowed">
                        Next
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
