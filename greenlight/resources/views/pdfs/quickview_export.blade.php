<style>
    body
    {
        /*font-family: dejavusans;*/
        /*font-size: 11px;*/
        /*color: #333333;*/
        background: #fff;
        font: 11px Helvetica, Arial, FreeSans, sans-serif;
        font-family: "Trebuchet MS", "Helvetica Neue", Helvetica, Arial, sans-serif;
        /*margin-right: -15px;*/
        /*margin-left: -15px;*/
    }

    p
    {
        margin-bottom: 0px;
        margin-top: 0px;
    }

    .clearfix
    {
        zoom: 1;
        clear: both;
    }

    .clearfix:after
    {
        clear: both;
    }

    .clearfix:before, .clearfix:after
    {
        content: '\0020';
        display: block;
        overflow: hidden;
        visibility: hidden;
        width: 0;
        height: 10px;
    }

    .row
    {
        display: block;
        /*-ms-flex-wrap: wrap;*/
        flex-wrap: wrap;

        width: 100%;
        clear: both;
        /*border: 1px solid red;*/
        page-break-inside: avoid;
    }

    .col-md-2
    {
        /*-ms-flex: 0 0 16.666667%;*/
        flex: 0 0 16.666667%;
        width: 16.666667%;
        float: right;
    }

    .col-md-2:first-child
    {
        /*-ms-flex: 0 0 16.666667%;*/
        flex: 0 0 16.666667%;
        width: 16.666667%;
        float: left;
    }

    .col-md-3
    {
        position: relative;
        /*-ms-flex: 0 0 25%;*/
        flex: 0 0 25%;
        width: 25%;
        float: right;
    }

    .col-md-12
    {

        /*-ms-flex: 0 0 100%;*/
        flex: 0 0 100%;
        width: 100%;
        /*float: left;*/

        /*border: 1px solid green;*/
    }

    .break_after
    {
        page-break-after: always;
    }

    .break_before
    {
        page-break-before: always;
    }

    #header,
    #footer
    {
        position: fixed;
        left: 0;
        right: 0;
        color: #aaa;
        font-size: 0.9em;
    }

    #header
    {
        top: 0;
        border-bottom: 0.1pt solid #aaa;
    }

    #footer
    {
        bottom: 10px;
        border-top: 0.1pt solid #aaa;
    }

    #header table,
    #footer table
    {
        width: 100%;
        border-collapse: collapse;
        border: none;
    }

    #header td,
    #footer td
    {
        padding: 0;
        width: 50%;
    }

    .page-number
    {
        text-align: right !important;
        right: 0px;
        position: absolute;
    }

    .page-number:before
    {
        content: "Page " counter(page);
    }

    .page-footer
    {
        text-align: center;
    }


</style>
<div class="row clearfix">
    <div class="col-md-12">
        <div class="col-md-2">
            <img src="{{@$img_src}}" width="100px" height="100px"/>
        </div>


        <div class="col-md-3">
             <p><span>First Lien Amount:</span>{{$first_lien_amount}}</p>
             <p><span>First Lien Date:</span> {{$first_date_recorded}}</p>
             <p><span>Second Lien Amount:</span>{{$second_lien_amount}}</p>
             <p><span>Second Lien Date:</span> {{$second_date_recorded}}</p>
             <p><span>Third Lien Amount:</span>{{$third_lien_amount}}</p>
             <p><span>Third Lien Date:</span> {{$third_date_recorded}}</p>
             <p><span>HOA Lien Amount:</span>{{$hoa_lien_amount}}</p>
             <p><span>HOA Date:</span> {{$hoa_date_recorded}}</p>
             <p><span>Other Lien Amount:</span>{{$other_lien_amount}}</p>
             <p><span>Other Lien Date:</span> {{$other_date_recorded}}</p>
        </div>
        <div class="col-md-3">
            <p><span>Sale Time:</span> {{@$sale_time}} </p>
            <p><span>Sale Date:</span> {{@$sale_date}} </p>
            <p><span>CASE #:</span>  {{$case_number}}</p>
            <p><span>Opening Bid:</span>  {{$opening_bid}}</p>
            <p><span>Sale Type:</span>  {{$sale_type}}</p>
            <p><span>Sale Status:</span>  {{$sale_status}}</p>
            <p><span>Owner Name:</span> {{$owner_full_name}}</p>
            <p><span>Owner Phone:</span> {{$owner_phone}}</p>
            <p><span>Buy It Request::</span> {{$buy_it_request}}</p>
        </div>
        <div class="col-md-3">
            <p><span>Address:</span> {{@$address}} </p>
            <p><span>County:</span> {{@$county}} </p>
            <p><span>City:</span> {{@$city}} </p>
            <p><span>State:</span> {{@$state}} </p>
            <p><span>Zip:</span> {{@$zip}} </p>
            <p><span>CMA/ARV: {{$cma_arv}}</p>
            <p><span>County Value: {{$county_value}}</p>
        </div>

    </div>

    <div class="col-md-12 clearfix">
        <p>&nbsp;</p>
    </div>
</div>
<hr/>
