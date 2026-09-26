<!DOCTYPE html>
<html>
<head>
    <title>Deposit</title>
</head>
<body>
<h1>Hello Team,</h1>
<p>
    We receive following request from user and following information are below,
</p>

<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td style="color:#333">
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: {{ @$info['first_name'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Email: {{ @$info['email'] }} </span></h2>

            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Number Home: {{ @$info['number_home'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Number Home if Crawl Space Picture : {{ @$info['number_of_home'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Total Cost: {{ @$info['total_cost'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Notes: {{ @$info['buyer_notes'] }} </span></h2>
        </td>
    </tr>
</table>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>