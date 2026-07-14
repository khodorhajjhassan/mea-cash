@component('mail::message')
# {{ $mailLocale === 'ar' ? 'رمز إعادة تعيين كلمة المرور' : 'Password Reset Code' }}

{{ $mailLocale === 'ar'
    ? 'استخدم الرمز التالي لإعادة تعيين كلمة مرور حسابك في MeaCash:'
    : 'Use the following code to reset your MeaCash account password:' }}

@component('mail::panel')
## {{ $code }}
@endcomponent

{{ $mailLocale === 'ar'
    ? 'تنتهي صلاحية هذا الرمز خلال 15 دقيقة.'
    : 'This code expires in 15 minutes.' }}

{{ $mailLocale === 'ar'
    ? 'إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة بأمان.'
    : 'If you did not request a password reset, you can safely ignore this email.' }}

{{ $mailLocale === 'ar' ? 'شكراً لك،' : 'Thanks,' }}<br>
MeaCash
@endcomponent
