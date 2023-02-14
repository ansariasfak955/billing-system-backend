@component('mail::message')
<h2>Forgot Password</h2>
You can reset password from below link:
<a href="{{env('WEBSITE_APP_URL')}}/reset-password?token={{$token}}&company_name={{$company_name}}">Reset Password</a>
@endcomponent