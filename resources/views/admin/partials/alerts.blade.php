@if (session('success'))
    <div class="alert alert-success" data-auto-dismiss>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error" data-auto-dismiss>
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        <p class="font-semibold">Validation error. Please review your input.</p>
        <ul class="mt-2 list-disc pl-5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
