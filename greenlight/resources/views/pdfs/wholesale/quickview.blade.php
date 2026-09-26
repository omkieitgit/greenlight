<div style="text-align: center;font-size: 17px;" class="field w100 left font_class  ">
    <strong>
        {{$address}} <br/> {{$city}} {{$state}} {{$zip}}
        </strong>
</div>
<div class="clearfix"></div>
<div style="height:742px;  width:100%;display: block; ">
    <div style="position: absolute;">
        <div >
            <div style="width: 50%; float: left; " >

                <div  class="w100  font_class " >
                    <fieldset>
                        <legend>Picture</legend>
                        <div class="w100" style="display: block;">
                            <img src="{{$img_src}}" title="Image" width="320px" height="248px" style="border:solid #220000; padding:2px;" />
                        </div>

                        <div style="padding-top: 10px; padding-bottom: 10px;">
                            &nbsp;<a  href="{{$address_url}}/pictures" class=" but-gal right" target="_blank">View More Pictures</a>
                        </div>
                        <div class="clearfix"></div>
                    </fieldset>
                </div>
                <div  class="w100   font_class ">
                    <fieldset>
                        <legend>Specific Facts</legend>
                        <label class="bold" >Supply & Demand Ratio</label> <span>&nbsp;</span> {{$gsd}}<br>
                        <label class="bold" >Average Average Days On Market</label> <span>&nbsp;</span> {{$est_days_on_market}}<br>
                        <label class="bold" >School Districts</label> <span>&nbsp;</span> {{$school}}<br>
                        <label class="bold" > Rents Zestimate</label> <span>&nbsp;</span> {{$rents_zestimate}}<br>
                        <div class="clearfix"></div>
                    </fieldset>
                </div>
            </div>
            <div style="width: 50%; float: right; ">
                <div   class="w100  font_class">
                    <fieldset>
                        <legend>Home Buyers Information</legend>
                        <div class="" style="display: block;">
                            <table width="100%">
                                <tr>
                                    <td width="50%"><label class="tdlabel">CMA/ARV</label> </td>
                                    <td width="50%">&nbsp;&nbsp;{{$cma_arv}}</td>
                                </tr>
                                <tr>
                                    <td><label class="tdlabel">Wholesale Cost</label></td>
                                    <td>&nbsp;&nbsp;{{$whole_sale_cost}}</td>
                                </tr>
                                <tr>
                                    <td>   <label class="tdlabel">Estimated Construction/Renovation Costs</label></td>
                                    <td>&nbsp;&nbsp;{{$est_house_repairs}}</td>
                                </tr>

                                <tr>
                                    <td>  <label class="tdlabel">Zestimate</label> </td>
                                    <td>&nbsp;&nbsp;{{$zestimates}}</td>
                                </tr>
                                <tr>
                                    <td>  <label class="tdlabel">County Value</label> </td>
                                    <td>&nbsp;&nbsp;{{$county_value}}</td>
                                </tr>

                                <tr>
                                    <td> <label class="tdlabel">Zillow Link</label> </td>
                                    <td>&nbsp;&nbsp;
                                        <a style="width:50px;display: inline-block;" class="" href="{{$zillow_url}}" target="_blank">Link</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label class="tdlabel">Realtor Link</label> </td>
                                    <td> &nbsp;&nbsp; <a style="width:50px;display: inline-block;" class="" href="{{$realtor_url}}" target="_blank">Link</a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style="padding-top: 10px; padding-bottom: 10px;">
                            <a href="{{$address_url}}/homebuyers" class=" but-dollars right" target="_blank">Click here to view more details</a>
                        </div>
                        <br><br><br>
                    </fieldset>
                </div>

                <div class="w100  font_class">
                    <fieldset>
                        <legend>Property Information</legend>
                        <label>Total Living Square Feet</label> <span>&nbsp;</span> {{$total_living_sqft}}<br>
                        <label>Total Square feet</label> <span>&nbsp;</span> {{$total_sqft}}<br>
                        <label class="bold" >Beds</label> <span>&nbsp;</span>{{$bed}}<br>
                        <label class="bold" >Baths</label> <span>&nbsp;</span> {{$bath}}<br>
                        <label>Year Built</label> <span>&nbsp;</span>{{$year_built}}<br>
                        <label>Lot Size</label> <span>&nbsp;</span> {{$lot_acreage_sf}}<br>
                        <label>Main Floor</label> <span>&nbsp;</span> {{$third_floor_area}}<br>
                        <label>Upper floor</label> <span>&nbsp;</span>  {{$second_floor_area}}<br>
                        <label>Basement Area</label> <span>&nbsp;</span>  {{$basement_area}}<br>

                        <label>Notes of Condition</label> <span>&nbsp;</span>
                        {{$property_description}}
                        <br>

                        <div style="padding-top: 10px; padding-bottom: 10px;">
                            <a  href="{{$address_url}}/propertyinformation" class=" but-home right" target="_blank">Click here to view more details</a>
                        </div>
                        <br><br><br>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>


