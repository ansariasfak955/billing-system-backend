@component('mail::message')
Dear {{ ucfirst($user['name']) }},

Thank you for registering with us.

Login with the credentials given below:-<br><br>
Email : {{ $user['email']}}<br>
Password : {{ $password }}<br><br>

@endcomponent