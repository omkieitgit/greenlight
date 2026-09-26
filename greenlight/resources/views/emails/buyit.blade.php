<!DOCTYPE html>
<html>
<head>
    <title>Buy It</title>
</head>
<body>
<!-- <h1>Hello,</h1>
<p>
    We receive following request from
    @if (@isset($user_type))
    {{$user_type}}
    @else
        user
    @endif
     and following information are below,
</p> -->


<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:'Trebuchet MS', Verdana, Arial;">
    <!-- Content -->
    <tr>
        <td style="color:#555">
            
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">{!! $info['pos_message'] !!}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">{{ $info['top_message'] }}</span></h2>
        </td>
    </tr>
    <tr>
        <td style="color:#333">
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: <a target="_blank" href="{{$info['address_url']}}">{{ $info['address_url'] }}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: {{ $info['first_name'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; ">
                <span style="font-size: 14px;">Did you drive the property or are you willing to take the risk of buying blind?: {{ $info['did_you_buy'] }}</span></h2>
            <!-- <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Did you get as many pictures as possible of the home?: {{ $info['did_you_picture'] }}</span></h2> -->
            <!-- <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Please submit the amount that you feel is needed for repairs.: {{ $info['please_submit'] }}</span></h2> -->
            <!-- <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Repair Cost: {{ $info['buyit_repair_cost'] }} </span></h2> -->
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Please insert your estimation of the ARV value?: {{ $info['buyit_estimation_arv_value'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">What is the highest amount you are willing to offer or bid to get this property?: {{ $info['highest_offer_bid'] }}</span></h2>
            <!-- <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Do you have your money or financing in place?: {{ $info['do_money_finance'] }}</span></h2> -->
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Notes: {{ $info['notes'] }}</span></h2>
        </td>
    </tr>
    <!-- #Content -->
</table>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>
