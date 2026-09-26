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
<div id="footer">
    <div class="page-footer">
        2484 Walnut St. #256 Cary, NC 27518
        <span class="page-number"></span>
    </div>
</div>

<div style="text-align:center;">
    <p style="text-align:center"><img alt="logo-1024.png" src="{{$header_logo}}"
                                      style="height:103px; width:115px"/></p>
    <p><strong style="font-size: 25px; color:#980000;">Greenlight Property Finder</strong>
        <br/><strong><span>+ 1 702 327-4556</span></strong>
    </p>
</div>

<br/>
<div style="text-align:center; color:red; font-size:24px;">Greenlight Property Finder Properties Info</div>
<br/>

{{--<link href='https://fonts.googleapis.com/css?family=Spectral' rel='stylesheet'>--}}
{{--<style>--}}
    {{--body {--}}
        {{--font-family: 'Spectral';font-size: 22px;--}}
    {{--}--}}
{{--</style>--}}

{{--<div>--}}
    {{--<div style="float:left; width:10%">--}}
    {{--<p >--}}
        {{--<span>--}}
            {{--<img--}}
                     {{--src="{{$header_logo}}"--}}
                    {{--style="width: 109.50px; height: 108.59px;" >--}}
        {{--</span>--}}
    {{--</p>--}}
    {{--</div>--}}
    {{--<div style="float:left; width:90%;">--}}
        {{--<table border="0" style="width:100%;border-bottom:1px solid black;">--}}
            {{--<tr>--}}
                {{--<td colspan="2">--}}
                    {{--THE ESTATES LLC--}}
                {{--</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td colspan="2">--}}
                    {{--&nbsp;--}}
                {{--</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td colspan="">--}}
                    {{--2374 Walnut St #256, Cary, NC 27518--}}
                {{--</td>--}}
                {{--<td colspan="">--}}
                    {{--Phone: + 1 702 327-4556 or +1 919-623-3696--}}
                {{--</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td colspan="">--}}
                    {{--<a class="c15" href="#">www.estatestracking.com</a>--}}
                {{--</td>--}}
                {{--<td colspan="">--}}
                    {{--Email: <a href="mailto:craig@theestates.com">craig@theestates.com</a>--}}
                {{--</td>--}}
            {{--</tr>--}}
        {{--</table>--}}
    {{--</div>--}}
    {{--<p>&nbsp;</p>--}}
    {{--<p class="c6"><span class="c17">EstatesTracking Sales Info</span></p>--}}
{{--</div>--}}
