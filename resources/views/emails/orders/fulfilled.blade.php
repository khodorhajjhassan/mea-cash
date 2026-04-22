<x-mail::message>
@php
    $orderUrl = $order->order_number
        ? route('store.orders.detail', ['locale' => $mailLocale, 'orderNumber' => $order->order_number])
        : route('store.orders', ['locale' => $mailLocale]);
@endphp

@if($mailLocale === 'ar')
# تم إكمال الطلب

مرحباً {{ $order->user->name }}،

تم إكمال طلبك **#{{ $order->order_number ?: $order->id }}** الخاص بـ **{{ $order->product->name_ar ?: $order->product->name_en }}**.

تفاصيل التسليم جاهزة الآن. يرجى فتح لوحة حسابك لعرض المفتاح أو معلومات التسليم بأمان.

<x-mail::button :url="$orderUrl">
عرض الطلب
</x-mail::button>

شكراً،<br>
{{ config('app.name') }}
@else
# Order Fulfilled!

Hello {{ $order->user->name }},

Great news! Your order **#{{ $order->order_number ?: $order->id }}** for **{{ $order->product->name_en }}** has been fulfilled.

Your delivery details are ready. Please open your dashboard to view the key or fulfillment information securely.

<x-mail::button :url="$orderUrl">
View Order in Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
@endif
</x-mail::message>
