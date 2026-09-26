<table cellpadding="2" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:12px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td width="32%">County: {{$county}}</td>
        <td width="32%">Opening Bid: {{$opening_bid}}</td>
        <td  width="32%">winning bid: {{$winning_bid_amount}}</td>
    </tr>
    <tr>
        <td>Sale Date: {{$sale_date}}</td>
        <td>Sale Type: {{$sale_type}}</td>
        <td>Property Type:{{$property_type}}</td>
    </tr>
    <tr>
        <td>First Lien Amount: {{$first_lien_amount}}</td>
        <td>Loan Estimated Balance First Lien: {{$first_loan_estimated_balance}}</td>
        <td>Second Lien Amount: {{$second_lien_amount}}</td>
    </tr>
    <tr>
        <td>Loan Estimated Balance Second Lien: {{$second_loan_estimated_balance}}</td>
        <td>Third Lien Amount: {{$third_lien_amount}}</td>
        <td>Loan Estimated Balance Third Lien: {{$third_loan_estimated_balance}}</td>
    </tr>

    <tr>
        <td>HOA Lien: {{$hoa_name}}</td>
        <td>CMA/ARV: {{$cma_arv}}</td>
        <td>Owner Name: {{$owner_full_name}}</td>
    </tr>
    <tr>
        <td>winning bidder: {{$winning_bidder}}</td>
        <td>HOA Redemption Expiration Date: {{$hoa_redemption_expires}}</td>
        <td>Trustee: {{$trustee}}</td>
    </tr>
    <tr>
        <td>Address: {{$winning_bidder}}</td>
        <td>Tax Redemption Expiration Date: {{$tax_redemption_expires}}</td>
        <td>Trustee: {{$trustee}}</td>
    </tr>
    <tr>
        
        <td>Total Estimated Debt w/ Late Payments & Attorney Fees $: {{$total_est_debt}}</td>
        <td>Total Estimated Late Payments and Fees %: {{$total_est_late_fee}}</td>
    </tr>
    <tr>
        <td>Legal Description: {{$legal_description}}</td>
    </tr>
    <tr>
        <td colspan="3">Record URL: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address_url}}</a></td>
    </tr>
</table>
<hr/>
