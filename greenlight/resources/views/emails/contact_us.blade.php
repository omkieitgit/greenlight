<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
</head>
<body>
<h1>Hello Team,</h1>
<p>
    We receive following request from user and following information are below,
</p>
<p>First Name: {{ $info['your_name'] }}</p>
<p>Company Name: {{ $info['company'] }}</p>
<p>Phone: {{ $info['phone'] }}</p>
<p>Email: {{ $info['email'] }}</p>
<p>Date: {{ Carbon\Carbon::now()->toDayDateTimeString() }}</p>
<p>Message:</p>
<p>{{ $info['message'] }}</p>
<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>