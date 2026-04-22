<x-mail::message>
@if($mailLocale === 'ar')
# تأكيد البريد الإلكتروني

مرحباً {{ $user->name }}،

استخدم الرمز التالي لتأكيد حسابك في MeaCash:

<div style="font-size: 32px; font-weight: 800; letter-spacing: 8px; text-align: center; margin: 24px 0;">
{{ $code }}
</div>

ينتهي هذا الرمز خلال 15 دقيقة.

إذا لم تقم بإنشاء هذا الحساب، يمكنك تجاهل هذه الرسالة.

شكراً،<br>
{{ config('app.name') }}
@else
# Verify Your Email

Hello {{ $user->name }},

Use the code below to verify your MeaCash account:

<div style="font-size: 32px; font-weight: 800; letter-spacing: 8px; text-align: center; margin: 24px 0;">
{{ $code }}
</div>

This code expires in 15 minutes.

If you did not create this account, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endif
</x-mail::message>
