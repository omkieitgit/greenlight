<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
<h1>Hello {{ $user->first_name.' '.$user->last_name }},</h1>
<p>
    Your password has been changed successfully. If you didn't do this, please contact the system administrator. .
</p>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME']}}
    {{ $footer['TEAM_DOMAIN']}}</p>
</body>
</html>