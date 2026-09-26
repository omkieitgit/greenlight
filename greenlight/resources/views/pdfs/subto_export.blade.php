<table cellpadding="2" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:12px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td width="32%">County: {{$county}}</td>
        <td width="32%">Address: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address}}</a></td>
        <td width="32%">City: {{$city}}</td>
        
        
        
    </tr>
    <tr>
        <td>Zip:{{$zip}}</td>
        <td>Sale Date: {{$sale_date}}</td>
        <td>Sale Type:{{$sale_type}}</td>
        
       
    </tr>
    <tr>
        <td>CMA/ARV: {{$cma_arv}}</td>
        <td>1st Lien :{{$lien_amount}}</td>
        <td>STR Y/N:{{$no_str}}</td>
       
    </tr>
    <tr>
        <td>2nd Lien:{{$second_lien_amount}}</td>
        <td>STR Y/N:{{$second_no_str}}</td>
        <td>3RD lien:{{$third_lien_amount}}</td> 
        <!-- <td>3rd lien Date:{{$third_date_recorded}}</td> -->
       
    </tr>
    <tr>
        <td>STR Y/N:{{$third_no_str}}</td>
        <td>HOA Lien: {{$hoa_name}}</td>
        <td>Redemption Date:{{$redemption_date}}</td>
       
    </tr>
    <tr>
        <td>Tax Lien:{{$tax_name}}</td>
        <td>Date:{{$date_of_tax_lien}}</td>
        <td>Redemption Date:{{$redemption_date}}</td>
       
    </tr>
    <tr>
        <td>Notice of Mailing Date:{{$notice_email}}</td>
    </tr>
    <tr>
        <td colspan="3">Link to Greenlight Property Finder: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address_url}}</a></td>
    </tr>
</table>
<hr/>