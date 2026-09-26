<div style="margin-top:20px;">
    <div style="" class="w100 left  font_class ">
        <fieldset>
            <legend>Picture</legend>
            <div style="padding-left:10px;">
                <img src="{{$img_src}}" title="Image" width="320px" height="248px"
                     style="border:solid #220000; padding:2px;"/>
            </div>
            <div class="clearfix"></div>
            &nbsp;<a href="{{$address_url}}/pictures" class=" but-gal right" target="_blank">View More
                Pictures</a>
        </fieldset>
    </div>
    <div class="clearfix"></div>
    <div style="" class="w100 left  font_class ">
        <fieldset class="fieldset_class">
            <legend>Acquisition</legend>
            <table width="100%">
                <tr>
                    <td width="30%"><label class="bold">Address</label>
                    </td>
                    <td width="20%">{{$address}}</td>
                    <td width="25%"><label class="bold">Owner Names</label></td>
                    <td width="25%">{{$full_name}}</td>
                </tr>
                <tr>
                    <td><label class="bold">Owner Contact Info</label></td>
                    <td>{{$owner_phone}}</td>
                    <td><label class="bold">Sq Feet</label></td>
                    <td>{{$total_sqft}}</td>
                </tr>
                <tr>
                    <td><label class="bold">Garage number</label></td>
                    <td>{{$garages}}</td>
                    <td><label class="bold">Lot size</label></td>
                    <td>{{$lot_acreage_sf}}</td>
                </tr>

                <tr>
                    <td><label class="bold">1st Lien Amount</label></td>
                    <td>{{$f_lien_amount}}</td>
                    <td><label class="bold">Date of 1st Lien</label></td>
                    <td>{{$f_date_recorded}}</td>
                </tr>

                <tr>
                    <td><label class="bold">2nd Lien Amount</label></td>
                    <td>{{$s_lien_amount}}</td>
                    <td><label class="bold">Date of 2nd Lien</label></td>
                    <td>{{$s_date_recorded}}</td>
                </tr>

                <tr>
                    <td><label class="bold">3rd Lien Amount</label></td>
                    <td>{{$t_lien_amount}}</td>
                    <td><label class="bold">Date of 3rd Lien</label></td>
                    <td>{{$t_date_recorded}}</td>
                </tr>
                @php
                    $total = 0;
                @endphp
                @for ($i = 0; $i < count($assessment); $i++)
                    @php
                        $locale = 'en_US';
                        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
                        $number_ordinal =  $nf->format($i+1);
                        $t1 =  \App\Helpers\CommonHelper::emptyMoneyDefault(@$assessment[$i]->property_taxes_owed);
                        $t2 =  \App\Helpers\CommonHelper::emptyDefault(@$assessment[$i]->property_taxes_owed_year);
                        $total += floatval(@$assessment[$i]->property_taxes_owed);
                    @endphp
                    <tr>
                        <td><label class="bold">Property Taxes Owed {{$number_ordinal}}</label></td>
                        <td>{{@$t1}}</td>
                        <td><label class="bold">Property Taxes Owed {{$number_ordinal}} Year</label></td>
                        <td>{{$t2}}</td>
                    </tr>
                @endfor
                @php
                    $total =  \App\Helpers\CommonHelper::emptyMoneyDefault($total);
                @endphp
                <tr>
                    <td><label class="bold">Total Property Taxes Owed</label></td>
                    <td> {{$total}}
                    </td>
                    <td></td>
                    <td>
                    </td>
                </tr>

                @for ($i = 0; $i < count($assessment); $i++)
                    @php
                        $locale = 'en_US';
                        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
                        $number_ordinal =  $nf->format($i+1);
                        $t1 =  \App\Helpers\CommonHelper::emptyMoneyDefault(@$assessment[$i]->taxes_assessed);
                        $t2 =  \App\Helpers\CommonHelper::emptyDefault(@$assessment[$i]->taxes_year);
                    @endphp
                    <tr>
                        <td><label class="bold">Property Taxes Assessed {{$number_ordinal}}</label></td>
                        <td>{{$t1}}</td>
                        <td><label class="bold">Property Taxes Assessed {{$number_ordinal}} Date</label></td>
                        <td>{{$t2}}</td>
                    </tr>
                @endfor
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <span>&nbsp;</span> <br>
            <div class="clearfix"></div>
        </fieldset>
    </div>


</div>