<!DOCTYPE html>
<html>
<head>
    <title>Email Links</title>
</head>
<body>
<h1>Hello,</h1>
<p>
    We receive following request from user and following information are below,
</p>
<p>
    {{ @$info['message'] }}
</p>
<p>
<div style="text-align:center; color:red; font-size:26px;margin-top: 25px;">Greenlight Property Finder Properties URL</div>
</p>

<table cellpadding="5" cellspacing="0" width="100%"
       style="padding:10px; color:#333; font-size:11px; font-family:'Trebuchet MS', Verdana, Arial;">
        {!! @$tr_html !!}
</table>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>