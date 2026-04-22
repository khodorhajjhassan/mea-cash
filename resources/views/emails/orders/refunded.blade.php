<x-mail::message>
@if($mailLocale === 'ar')
# تم استرداد الطلب

مرحباً {{ $order->user->name }}،

تم استرداد طلبك **#{{ $order->order_number }}**.

تمت إعادة مبلغ **${{ number_format($order->total_price, 2) }}** إلى رصيد محفظتك.

@if($order->refund_notes)
**سبب الاسترداد:**
{{ $order->refund_notes }}
@endif

يمكنك مراجعة رصيدك وسجل العمليات من لوحة حسابك.

شكراً،<br>
{{ config('app.name') }}
@else
# Order Refunded

Hello {{ $order->user->name }},

Your order **#{{ $order->order_number }}** has been refunded.

The amount of **${{ number_format($order->total_price, 2) }}** has been credited back to your wallet balance.

@if($order->refund_notes)
**Reason for Refund:**
{{ $order->refund_notes }}
@endif

You can view your updated balance and transaction history in your dashboard.

Thanks,<br>
{{ config('app.name') }}
@endif
</x-mail::message>
