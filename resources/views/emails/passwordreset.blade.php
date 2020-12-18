@component('mail::message')
# Hello, {{ explode(" ", $name)[0] }}

You are receiving this email because we received a password reset request for your account.

@component('mail::button', ['url' => 'https://app.mylayr.com/reset-password/' . $token, 'color' => 'primary'])
Reset Password
@endcomponent

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required.

@component('mail::subcopy')
If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{ 'https://app.mylayr.com/reset-password/' . $token }}">{{ 'https://app.mylayr.com/reset-password/' . $token }}</a>
@endcomponent

@endcomponent