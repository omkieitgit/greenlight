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
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: <a href="{{ url(@$info['address_url']) }}">{{ url(@$info['address_url']) }}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Property Address: <a target="_blank" style="text-decoration: none;" href="{{ @$info['map_type'] }}">{{ @$info['address'] }}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: {{ @$info['first_name'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Turn around time: {{ @$info['turn_around_time'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Rate of Return: {{ @$info['rate_of_return'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Renovation Risk: {{ @$info['renovation_risk'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Are you funding the deposit: {{ @$info['funding_deposit'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How much to bring the loan current: {{ @$info['loan_current'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How much is needed for Deposit: {{ @$info['needed_for_deposit'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How much is needed for renovation: {{ @$info['needed_for_renovation'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Miscellaneous Fees: {{ @$info['miscelainous_fees'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Is the Estimated Values Filled out accurately A to B?: {{ @$info['estimated_values_ab'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Is the Estimated Values Filled Out B to C?: {{ @$info['estimated_values_bc'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Do you have your Full Scope of Work?: {{ @$info['full_scope_work'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Do you have Full Material List?: {{ @$info['full_material_list'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Do you have your timeline: {{ @$info['your_timeline'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How many rental properties do you currently own? How many have you purchased over the past 2 years?: {{ @$info['purchased_over'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How many fix n flip transactions have you completed over the past 2 years?: {{ @$info['flip_transactions'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">How many properties do you have under rehab currently?: {{ @$info['rehab_currently'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">P1 Value: {{ @$info['p1_value'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">P1 ADOM: {{ @$info['p1_adom'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">P2 Value: {{ @$info['p2_value'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">P2 ADOM: {{ @$info['p2_adom'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">P3 Value: {{ @$info['p_value2'] }}</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">P3 ADOM: {{ @$info['p2_adom'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Wholetail Value: {{ @$info['wholetail_value'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Rental Rate: {{ @$info['rental_rate'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Loan Type: {{ @$info['loan_type'] }} </span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Note: {{ @$info['deposit_notes'] }} </span></h2>
        </td>
    </tr>
    <tr>
        <td>
            <a href="{{ @$info['fund_deal_url'] }}"><input type='button' name='fund_deal' value='Fund This Deal' /></a>

        </td>
    </tr>
</table>

<p>
    Thank you,<br/>
    {{ $footer['TEAM_NAME'] }}
    {{ $footer['TEAM_DOMAIN'] }}</p>
</body>
</html>