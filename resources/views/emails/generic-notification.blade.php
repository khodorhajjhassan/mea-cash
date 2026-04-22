<x-mail::message>
# {{ $title }}

{{ $message }}

@if(!empty($details))
@foreach($details as $label => $value)
**{{ $label }}:** {{ $value }}

@endforeach
@endif

@if($actionUrl && $actionText)
<x-mail::button :url="$actionUrl">
{{ $actionText }}
</x-mail::button>
@endif

{{ $mailLocale === 'ar' ? 'شكراً،' : 'Thanks,' }}<br>
{{ config('app.name') }}
</x-mail::message>
