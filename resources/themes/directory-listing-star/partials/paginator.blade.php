@if ($paginator->hasPages())
    <nav class="listar-pagination">
        <ul>
            @if (!$paginator->onFirstPage())
                <li class="listar-prevpage"><a href="{{ $paginator->previousPageUrl() }}"><i
                                class="fa fa-angle-left"></i></a></li>

            @endif
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li style="display: inline-block">{{ $element }}</li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="listar-active" style="display: inline-block"><a href="#">{{ $page }}</a></li>
                        @else
                            <li style="display: inline-block"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <li class="listar-nextpage"><a href="{{ $paginator->nextPageUrl() }}"><i
                                class="fa fa-angle-right"></i></a></li>
            @endif
        </ul>
    </nav>
@endif

