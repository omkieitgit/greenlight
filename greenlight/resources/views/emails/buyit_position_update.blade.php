<!DOCTYPE html>
<html>
<head>
    <title>Buy IT</title>
</head>
<body>
<h1>Hello {{ $info['name'] }},</h1>
<p>
    We receive following request from user and following information are below,
</p>

<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:'Trebuchet MS', Verdana, Arial;">

    <tr>
        <td style="color:#333">
            <p><strong>Hello {name},</strong></p>

            <p>Congratulations your position has moved up on this property!</p>

            <p>Below is the property link</p>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: <a target="_blank" href="{{$info['address_url']}}">{{ $info['address_url'] }}</a></span></h2>
        </td>
    </tr>

</table>
<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>