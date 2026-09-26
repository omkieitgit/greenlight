<table cellpadding="2" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:12px; font-family:'Trebuchet MS', Verdana, Arial;">
    <tr>
        <td width="32%">Address: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address}}</a></td>
        <td width="32%">County: {{$county}}</td>
        <td width="32%">Sale Date: {{$sale_date}}</td>
    </tr>
    <tr>
        <td>Case Number: {{$case_number}}</td>
        <td colspan="2">Record URL: <a href="{{$address_url}}" class=" but-dollars right" target="_blank">{{$address_url}}</a></td>
    </tr>

        @if(count($bidderInfo)>=1)
            @php
                $i = 1
            @endphp
            @foreach ($bidderInfo as $bidder)
                <tr>
                        <td>Bid {{$i}}: {{ $bidder->name_upset_bidder }}</td>
                        <td>Bid {{$i}} Amount: {{ $bidder->amount_of_bid }}</td>
                        <td>LDUB {{$i}}: {{ $bidder->last_date_to_upset_bid }}</td>
                </tr>
                @php
                ++$i
            @endphp
            @endforeach
        @endif
</table>
<hr/>
