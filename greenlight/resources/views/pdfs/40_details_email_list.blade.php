<!-- Bootstrap CSS -->
{{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">--}}

<div class="row clearfix">
    <div class="col-md-12">
        <div class="col-md-2">
            <img src="{{@$img_src}}" width="100px" height="100px"/>
        </div>

        <div class="col-md-2">
            <p><span>NOS BY:</span> {{@$nos_by}} </p>
            <p><span>NOS DATE:</span> {{@$nos_date}}</p>
            <p><span>DTC By:</span> {{@$dtc_by}} </p>
            <p><span>DTC DATE:</span> {{@$dtc_date}}</p>
            <p><span>PROPERTY CLOSE A TO B:</span> {{@$property_close_a_to_b}}</p>
            <p><span>A TO B BONUS PAID DATE:</span></p>
        </div>

        <div class="col-md-3">
            <p><span>Sale Type:</span> {{@$sale_type}}</p>
            <p><span>First Lien Amount:</span> {{@$first_lien_amount}}</p>
            <p><span>First Lien Date:</span> {{@$first_date_recorded}}</p>
            <p><span>Foreclosing First Lien:</span> {{@$first_lien_foreclosing}}</p>
            <p><span>Min Amount To UPSET Bid:</span>{{@$min_amt_nxt_ub}}</p>
            <p><span>Last Date To UPSET Bid:</span> {{@$last_date_to_upset_bid}}</p>
            <p><span>Winning Bid:</span> {{@$winning_bid}}</p>
            <p><span>Winning Bidder:</span> {{@$wining_bidder}}</p>
        </div>
        <div class="col-md-2">
            <p><span>Trustee:</span> {{@$trustee}}</p>
            <p><span>Sale Date:</span> {{@$sale_date}}</p>
            <p><span>Sale Time:</span> {{@$sale_time}}</p>
            <p><span>CASE #:</span> {{@$case_number}}</p>
            <p><span>Precinct #:</span> {{@$priceint}}</p>
            <p><span>Opening Bid:</span> {{@$opening_bid}}</p>
            <p><span>County Value:</span>{{@$county_value}}</p>
            <p><span>CMA/ARV:</span> {{@$cma_arv}}</p>
            <p><span>Zestimate:</span> {{@$zestimates}}</p>
            <p><span>GSD:</span> {{@$gsd}} </p>
            <p><span>SSD:</span> {{@$ssd}}</p>
        </div>
        <div class="col-md-3">
            <p><span>Address:</span> {{@$address}}</p>
            <p><span>City:</span> {{@$city}}</p>
            <p><span>County:</span> {{@$county}}</p>
            <p><span>State:</span> {{@$state}}</p>
            <p><span>Zip:</span> {{@$zip}}</p>
            <p><span>Living SQFT:</span> {{@$total_living_sqft}}</p>
            <p><span>Bed:</span> {{@$bed}}</p>
            <p><span>Bath:</span> {{@$bath}}</p>
            <p><span>Lot Size:</span> {{@$lot_acreage_sf}}</p>
            <p><span>Parcel ID:</span> {{@$parcel_id1}}</p>
            <p><span>Subdivision:</span> {{@$subdivision}}</p>
        </div>



    </div>

    <div class="col-md-12 clearfix">
        <p><span>Notes of Condition:</span> {{@$property_description}} </p>
    </div>
</div>
<hr/>
