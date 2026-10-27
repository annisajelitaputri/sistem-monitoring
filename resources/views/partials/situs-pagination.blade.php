@if ($paginator->hasPages())
    <nav class="st-pagination" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="st-pg st-pg-off">Sebelumnya</span>
        @else
            <a class="st-pg st-pg-nav" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="st-pg st-pg-off">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="st-pg st-pg-on">{{ $page }}</span>
                    @else
                        <a class="st-pg" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="st-pg st-pg-nav" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
        @else
            <span class="st-pg st-pg-off">Berikutnya</span>
        @endif
    </nav>
@endif
