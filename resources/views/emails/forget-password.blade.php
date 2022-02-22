@component('mail::message')
<h2>Forgot Password</h2>
You can reset password from bellow link:
<a href="{{env('WEBSITE_APP_URL')}}/reset-password/{{$token}}">Reset Password</a>
@endcomponent