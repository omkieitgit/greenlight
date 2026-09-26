<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
<h1>Hello {{ $user->first_name.' '.$user->last_name }},</h1>
<p>
    Please click on the link below, within 24 hours, to reset your password.
</p>
<p>
    <a href='{{ $reset_link}}'>{{$reset_link}}</a>
</p>
<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME']}}
    {{ $footer['TEAM_DOMAIN']}}</p>
</body>
</html>