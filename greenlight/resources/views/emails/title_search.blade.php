<!DOCTYPE html>
<html>
<head>
    <title>Title Search Bid</title>
</head>
<body>
<h1>Hello Team,</h1>
<p>
    We receive following request from user and following information are below,
</p>

<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td style="color:#333">
            <h2>Our client has expressed an interest about title search.</h2>
            <h2>Here is the record link and client of Greenlight Property Finder. Please contact them as soon as possible to service about title search.</h2>
        </td>
    </tr>
    <tr>
        <td style="color:#333">
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: <a href="{{ url(@$info['address_url']) }}">{{ url(@$info['address_url']) }}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Property Address: <a target="_blank" style="text-decoration: none;" href="{{ @$info['map_type'] }}">{{ @$info['address'] }}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: {{ @$info['first_name'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Email: {{ @$info['email'] }}</span></h2>
        </td>
    </tr>
</table>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>