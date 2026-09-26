<table cellpadding="2" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:12px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td width="32%">Address: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address}}</a></td>
        <td width="32%">City: {{$city}}</td>
        <td width="32%">County: {{$county}}</td>
    </tr>
    <tr>
        <td>Bidder Name: {{$bidder_name}}</td>
        <td>Address of Upset Bidder:{{$bidder_address}}</td>
        <td>Amount of New Upset Bid: {{$amount_of_bid}}</td>
    </tr>

    <tr>
        <td>Bid Date: {{$bid_date}}</td>
        <td>Last Day for Next Upset Bid: {{$last_date_to_upset_bid}}</td>
    </tr>
    <tr>
        <td>Owner Name: {{$full_name}}</td>
        <td>Owner Address: {{$owner_full_address}}</td>
        <td>Owner Phone#:{{$owner_phone}}</td>
    </tr>

    <tr>
        <td colspan="3">Record URL: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address_url}}</a></td>
    </tr>
</table>
<hr/>
