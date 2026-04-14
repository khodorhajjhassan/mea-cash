<x-mail::message>
# Order Fulfilled!

Hello {{ $order->user->name }},

Great news! Your order **#{{ $order->order_number ?: $order->id }}** for **{{ $order->product->name_en }}** has been fulfilled.

## Fulfillment Details
@php($fulfillment = $order->getFulfillmentDetails())
@php($data = $fulfillment['data'] ?? [])
@foreach($data as $label => $value)
**{{ ucfirst(str_replace('_', ' ', $label)) }}:** {{ $value }}
@endforeach

@if(!empty($fulfillment['admin_note']))
**Note from Admin:**
{{ $fulfillment['admin_note'] }}
@endif

<x-mail::button :url="config('app.url') . '/dashboard/orders/' . $order->id">
View Order in Dashboard
</x-mail::button>

If you have any questions, feel free to reply to this email or contact us on WhatsApp.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
