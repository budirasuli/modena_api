@component('mail::message')

Thank you for registering!

@component('mail::button', ['url' => $verifyUrl])
Verify Email
@endcomponent


Regards,<br>
{{ config('app.name') }}

@endcomponent