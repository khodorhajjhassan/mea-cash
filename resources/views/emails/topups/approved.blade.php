<x-mail::message>
# Top-up Approved!

Hello {{ $topup->user->name }},

We are happy to inform you that your top-up request has been approved.

**Amount Credited:** ${{ number_format($amountCredited, 2) }}

@if($topup->admin_note)
**Admin Note:**
{{ $topup->admin_note }}
@endif

Your wallet has been updated. You can now use your balance to purchase products on MeaCash.

<x-mail::button :url="config('app.url') . '/dashboard/wallet'">
View My Wallet
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
