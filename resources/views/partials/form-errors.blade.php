@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $e)
            <div>{{ $e }}</div>
        @endforeach
    </div>
@endif
