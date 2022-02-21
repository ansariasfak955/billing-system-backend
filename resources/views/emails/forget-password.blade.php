@component('mail::message')
<h2>Forgot Password</h2>
You can reset password from bellow link:
<a href="https:{{env('APP_URL')}}/reset-password/{{$token}}">Reset Password</a>
@endcomponent