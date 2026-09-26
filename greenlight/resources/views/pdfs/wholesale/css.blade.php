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
    .but-dollars {
        background-image: url("{{$dollars_pic}}");
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px 2px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
        background-size: 25px;
    }
    .but-gal {
        background-image: url("{{$pictures_pic}}");
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px -1px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
    }
    .but-home {
        background-image: url("{{$gohome_pic}}");
        background-repeat: no-repeat;
        /* background-attachment: fixed; */
        background-position: 5px -1px;
        height: 30px;
        line-height: 30px;
        padding-left: 40px;
    }
    .but-foreclosure {
        background-image: url("{{$foreclosure_pic}}");
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

    fieldset {
        border: 1px solid #CCCCCC;
        border-radius: 10px 10px 10px 10px;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.15) inset, 0 1px 0 rgba(255, 255, 255, 0.15);
        margin-bottom: 16px;
        padding: 20px;
    }
    fieldset label {
        display: inline-block;
        width: 45%;
        font-weight: 600;
        color: #666;
        padding: 2px;
        text-align: right;
    }
    
     .fieldset_class label {
        display: inline-block;
        width: 98%;
        font-weight: 600;
        color: #666;
        padding: 2px;
        text-align: left;
    }
    .tdlabel {
        /*display: inline-block;*/
        width: 100%;
        font-weight: 600;
        color: #666;
        padding: 2px;
        text-align: right;
    }
    fieldset legend {
        font-size: 18px;
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
    .w100 {
        width:100%;
    }
    .break_after{
        page-break-after: always;}
    .break_before{
        page-break-before: always;}

    #footer {
        position: fixed;
        left: 0;
        right: 0;
        color: #aaa;
        font-size: 0.9em;
    }
    #footer {
        bottom: 10px;
        border-top: 0.1pt solid #aaa;
    }
    #footer table {
        width: 100%;
        border-collapse: collapse;
        border: none;
    }
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
