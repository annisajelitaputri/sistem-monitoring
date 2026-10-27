@if ($paginator->hasPages())
    <nav class="mg-pager" role="navigation">
        @if ($paginator->onFirstPage())
            <span class="mg-pbtn is-disabled">Sebelumnya</span>
        @else
            <a class="mg-pbtn" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="mg-pnum is-disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <a class="mg-pnum {{ $page == $paginator->currentPage() ? 'is-active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="mg-pbtn" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
        @else
            <span class="mg-pbtn is-disabled">Berikutnya</span>
        @endif
    </nav>
@endif
