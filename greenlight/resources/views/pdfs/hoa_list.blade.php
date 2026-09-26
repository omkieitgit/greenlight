<table cellpadding="2" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:12px; font-family:'Trebuchet MS', Verdana, Arial;">
    
    <tr>
        <td width="32%">Buyer: {{$buy_it_request}}</td>
        <td width="32%">Trustee: {{$trustee}}</td>
        <td width="32%">County: {{$county}}</td>
    </tr>

    <tr>
        <td width="32%">Address: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address}}</a></td>
        <td>Year Built: {{$year_built}}</td>
        <td>Living Sqft: {{$total_living_sqft}}</td>
       
    </tr>

    <tr>
        <td>CMA/ARV: {{$cma_arv}}</td>
        <td>Total Mortgage Lien: {{$total_lien}}</td>
        <td>Total Estimated Equity: {{$total_equity}}</td>
    </tr>
    <tr>
        <td>HOA lien:{{$hoa_lien_amount}}</td>
        <td>DCV#:{{$DCV}}</td>
        <td>1st STR Y/N: {{$first_no_str}}</td>
    </tr>
    <tr>
        <td>Loan Estimated Balance First Lien: {{$first_loan_estimated_balance}}</td>
        <td>2nd STR Y/N: {{$second_no_str}}</td>
        <td>Loan Estimated Balance Second Lien: {{$second_loan_estimated_balance}}</td>
    </tr>
    <tr>
        <td>3rd STR Y/N: {{$third_no_str}}</td>
        <td>Loan Estimated Balance Third Lien: {{$third_loan_estimated_balance}}</td>
        <td>Total Estimated Balance: {{$total_loan_estimated_balance}}</td>
    </tr>
    <tr>
        <td>Max Bid: - </td>
        <td>90/180 Stategy: - </td>
        <td>Notes: - </td>
    </tr>
    <tr>
        <td colspan="3">Property URL: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address_url}}</a></td>
    </tr>
  	
</table>
<hr/>