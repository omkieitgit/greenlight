<!DOCTYPE html>
<html>
<head>
    <title>Lender It</title>
</head>
<body>
<h1>Hello Team,</h1>
<p>
    We receive following request from user and following information are below,
</p>

<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:'Trebuchet MS', Verdana, Arial;">

    <tr>
        <td style="color:#333">
            <h2>Our client has expressed an interest in getting a funding quote.</h2>
            <h2>Here is the record link and client of Greenlight Property Finder. Please contact them as soon as possible to service their funding needs on this property.</h2>
        </td>
    </tr>
    <tr>
        <td style="color:#333">
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: {{ @$info['address_url'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Property Address: <a target="_blank" style="text-decoration: none;" href="{{ @$info['map_type'] }}">{{ @$info['address'] }}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: {{ @$info['first_name'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Email: {{ @$info['email'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Contact Number: {{ @$info['contact_number'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Type of Lender: {{ @$info['what_type_lender'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Note: {{ @$info['notes'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Current credit score?: {{ @$info['credit_score'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How many fix n flip transactions have you completed over the past 2 years?: {{ @$info['hown_many_fix_n_flip'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How many rental properties do you currently own? How many have you purchased over the past 2 years?: {{ @$info['retal_currently_own'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">What location(s) are you acquiring properties to either fix n flip or buy/hold?: {{ @$info['location_acquiring'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How much cash on hand do you have in the bank?: {{ @$info['cash_in_hand'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Value of retirement accounts including 401K, IRA, Stcoks, Mutual Funds, Money Market, Home Equity Lines, Bonds, Cash from Insurance policy, money markets, etcs. Please be specific as to the value of each investment type.: {{ @$info['gal_401k'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Any bankruptcies, short sales or foreclosure? If so, how long ago?: {{ @$info['bankruptcies'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How many properties do you have under rehab currently? Purchase price? Rehab costs? "As is" value? After repair value?: {{ @$info['rehab_currently'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Real estate goals over the next 12 monts?: {{ @$info['gal_real_estate_12'] }}</span></h2>
        </td>
    </tr>
</table>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>