{{--@extends('info.is_agree_content')--}}
<style>.pdf_div {
        /*padding: 26px 44px 8px 40px;*/
        min-height: 475px;
        margin-top: -3px;
    }
    .embed_pdf {
        border: none;
        width: 100%;
        /*margin-left: 20px;*/
        height: 1064px;
        overflow: auto;
        margin-bottom: -32px;
        margin-top: 16px;
    }
</style>
<div class="clearfix">
    <div class="_view1">
        <div class="left w100">
            <form name="agree" id="agree" enctype="multipart/form-data" method="post">
                <p>The Terms and Conditions have been updated. Please read through the terms and conditions before proceeding to the site. Please check the box "I agree".</p>
                <div class="pdf_div" style="display:block; min-height:1002px;">
                    @include('info.is_agree_content')
                </div>
            </form>
        </div>
    </div>
</div>

