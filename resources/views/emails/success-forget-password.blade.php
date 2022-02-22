@component('mail::message')
<h2>Hi, {{$user['name']}}</h2>
    <p> Your password has been changed successfully!<br>
        If it wasn't you, contact our team.
    </p>
@endcomponent