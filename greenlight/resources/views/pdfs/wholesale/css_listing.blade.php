<style>

     .td_listing{
        font-size: 12px;
        FONT-FAMILY: ABCDEF-LeelawadeeUI1;
        color: rgb(0,0,0);
     
    }
    .td_listing label {
        padding-right: 3px;
    }
    
    .buttonQV:hover {
        background-color: #ddd;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#fafafa', EndColorStr='#dddddd');
    }

    .buttonQV {
        display: inline-block;
        white-space: nowrap;
        background-color: #ccc;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#eeeeee', EndColorStr='#cccccc');
        border: 1px solid #777;
        padding: 0 1.5em;
        margin: 0.5em;
        font: bold 1em/2em Arial, Helvetica;
        text-decoration: none;
        color: #333;
        text-shadow: 0 1px 0 rgba(255,255,255,.8);
        -moz-border-radius: .2em;
        -webkit-border-radius: .2em;
        border-radius: .2em;
        -moz-box-shadow: 0 0 1px 1px rgba(255,255,255,.8) inset, 0 1px 0 rgba(0,0,0,.3);
        -webkit-box-shadow: 0 0 1px 1px rgba(255,255,255,.8) inset, 0 1px 0 rgba(0,0,0,.3);
        box-shadow: 0 0 1px 1px rgba(255,255,255,.8) inset, 0 1px 0 rgba(0,0,0,.3);
    }
    .but-dollars {
        {{--background-image: url("<?php echo base_url(); ?>assets/images/dollars.png");--}}
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px 2px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
        background-size: 25px;
    }
    .but-gal {
        {{--background-image: url("<?php echo base_url(); ?>assets/images/pictures.png");--}}
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px -1px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
    }
    .but-home {
        {{--background-image: url("<?php echo base_url(); ?>assets/images/gohome.png");--}}
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px -1px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
    }
    .but-foreclosure {
        {{--background-image: url("<?php echo base_url(); ?>assets/images/foreclosure.gif");--}}
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px -1px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
        background-size: 32px;
    }
    .clearfix {
        zoom: 1;
    }
    .clearfix:after {
        clear: both;
    }
    .clearfix:before, .clearfix:after {
        content: '\0020';
        display: block;
        overflow: hidden;
        visibility: hidden;
        width: 0;
        height: 10px;
    }

    .tdlabel {
        display: inline-block;
        width: 100%;
        font-weight: 600;
        color: #666;
        padding: 2px;
        text-align: right;
    }

    .right {
        float: right;
    }
    .left {
        float: left;
    }
    .field {
        display: block;
        margin: 4px 0px;
    }
    .font_class {
        color: #333333;
        background: #fff;
        font: 11px Helvetica, Arial, FreeSans, sans-serif;
        font-family: "Trebuchet MS", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    .w50 {
        width:100%;


    }
    .break_after{
        page-break-after: always;}
    .break_before{
        page-break-before: always;}



    #header,
    #footer {
        position: fixed;
        left: 0;
        right: 0;
        color: #aaa;
        font-size: 0.9em;
    }

    #header {
        top: 0;
        border-bottom: 0.1pt solid #aaa;
    }

    #footer {
        bottom: 10px;
        border-top: 0.1pt solid #aaa;
    }

    #header table,
    #footer table {
        width: 100%;
        border-collapse: collapse;
        border: none;
    }

    #header td,
    #footer td {
        padding: 0;
        width: 50%;
    }

    .page-number {
        text-align: right !important;
        right: 0px;
        position: absolute;
    }

    .page-number:before {
        content: "Page " counter(page);
    }
    .page-footer {
        text-align: center;
    }
   
</style>

<div id="footer">
    <div class="page-footer" >
        2484 Walnut St. #256 Cary, NC 27518
        <span class="page-number" ></span>
    </div> 
    <!--     -->
</div>
