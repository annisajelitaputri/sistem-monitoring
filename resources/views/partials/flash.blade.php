@if (session('success'))
    <div class="mg-alert mg-alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="mg-alert mg-alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
