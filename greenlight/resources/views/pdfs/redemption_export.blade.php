<table cellpadding="2" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:12px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td width="32%">Sale Date: {{$sale_date}}</td>
        <td width="32%">HOA Redemption Expiration Date: {{$hoa_redemption_expires}}</td>
        <td width="32%">Opening Bid: {{$opening_bid}}</td>
    </tr>
    <tr>
        <td>HOA Winning Bid: {{$hoa_winning_bid}}</td>
        <td>Affidavit: {{$hoa_affidavit_date}}</td>
        <td>CMA/ARV:{{$cma_arv}}</td>
    </tr>
    <tr>
        <td>1st Lien $: {{ $first_lien }}</td>
        <td>1st Lien Date: {{ $first_lien_date }}</td>
        <td>2nd Lien $: {{ $second_lien }}</td>
    </tr>
    <tr>
        <td>2nd Lien Date: {{$second_lien_date}}</td>
        <td>3rd Lien $: {{$third_lien}}</td>
        <td>3rd Lien Date: {{$third_lien_date}}</td>
    </tr>
    <tr>
        <td>HOA Name: {{$hoa_name}}</td>
        <td>HOA Lien $: {{$hoa_lien}}</td>
        <td>HOA Lien Date: {{$hoa_lien_date}}</td>
    </tr>

    <tr>
        <td>Taxes Due: {{$taxes_due}}</td>
        <td>Owner Name: {{$owner_full_name}}</td>
        <td>Trustee: {{$trustee}}</td>
    </tr>
    <tr>
        <td>Address: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address}}</a></td>
        <td>County: {{$county}}</td>
    </tr>
    <tr>
        <td colspan="3">Legal Description: {{$legal_description}}</td>
    </tr>
    <tr>
        <td colspan="3">Record URL: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address_url}}</a></td>
    </tr>
</table>
<hr/>
