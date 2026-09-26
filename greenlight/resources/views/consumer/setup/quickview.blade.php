@include('consumer.includes.header')

<section class="ftco-section contact-section">
                <div id="listing" class="content white_space landing-padding">
                        <div class="container parent-loader">
                        <!-- Loader DIV -->
                        <div *ngIf="!dataLoaded"  class="child-loader"><span class="spinner-small "></span></div>
                        <!-- Loader DIV -->
                        <div class="row" *ngIf="dataLoaded">
                                <!-- begin col-6 -->
                                <div class="col-lg-12 m-t-10">
                                <div class="alert alert-warning fade show">
                                        <h4> You are not logged in! If you want to see the property details please Click <a href="{{ url('login') }}">Here</a>. </h4>
                                </div>
                                <!-- begin panel -->   
                                
                                <div class="form-group row m-t-10">
                                        <div class="col-md-6">
                                                <fieldset>
                                                        <legend><b>{{$result->address}}</b></legend>
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-4"><label class="col-form-label">Address</label></div>
                                                                <div class="col-md-8 ">{{ $result->address }} {{ $result->city }} {{ $result->state }} {{ $result->zip }}</div>
                                                        </div>
                                                
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-4"><label class="col-form-label">Sp Number</label></div>
                                                                <div class="col-md-8 ">{{ !empty($result->last_sale_details->case_number)?$result->last_sale_details->case_number:$notAvail }}</div>
                                                        </div>
                                        
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-4"><label class="col-form-label">Loan Type</label></div>
                                                                <div class="col-md-8 ">{{$result->loanType}}</div>
                                                        </div>
                                        
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-4"><label class="col-form-label">County</label></div>
                                                                <div class="col-md-8 ">{{ !empty($result->county)?$result->county:$notAvail }}</div>
                                                        </div>
                                                        
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-12"><label class="col-form-label"><b>Notes : </b>{{ !empty($result->property_descriptions->legal_description)?$result->property_descriptions->legal_description:$notAvail}}</label></div>      
                                                        </div> 
                                                </fieldset>

                                        </div>
                                </div>   


                                <div class="form-group row m-t-10">
                                        <div class="col-md-6">
                                                <fieldset>
                                                        <legend><b>Property Information</b></legend>
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-6"><label class="col-form-label">Total Living Square Feet</label></div>
                                                                <div class="col-md-6 ">{{ !empty($result->total_living_sqft)?$result->total_living_sqft:$notAvail}}</div>
                                                        </div>
                                                
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-6"><label class="col-form-label">Year Built</label></div>
                                                                <div class="col-md-6 ">{{ !empty($result->year_built)?$result->year_built:$notAvail}}</div>
                                                        </div>
                                                
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-6"><label class="col-form-label">Bed</label></div>
                                                                <div class="col-md-6 ">{{ !empty($result->bed)?$result->bed:$notAvail}}</div>
                                                        </div>
                                                
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-6"><label class="col-form-label">Bath</label></div>
                                                                <div class="col-md-6 ">{{ !empty($result->bath)?$result->bath:$notAvail}}</div>
                                                        </div>
                                                
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-6"><label class="col-form-label">Lot Size</label></div>
                                                                <div class="col-md-6 ">{{ !empty($result->lot_acreage_sf)?$result->lot_acreage_sf:$notAvail}}</div>
                                                        </div>
                                                
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-6"><label class="col-form-label">Zillow Link</label></div>
                                                                <div class="col-md-6 "><a href="{{$result->local_real_estate_details->zillow_url}}" target="_blank">Click Here</a></div>
                                                        </div>
                                                </fieldset>
                                        </div>
                                        <div class="col-md-6">
                                                <fieldset>
                                                        <legend><b>Picture</b></legend>
                                                        <div class="form-group row m-b-0">
                                                                <div class="col-md-12">
                                                                        <img src="{{$result->front_picture->url}}" style="width: 100%" />
                                                                </div>
                                                        </div>
                                                </fieldset>
                                        </div>
                                </div>    


                                <div class="form-group row m-t-10">
                                        <div class="col-md-6">
                                        <fieldset>
                                                <legend><b>Sale Information</b></legend>
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">CMA/ARV</label></div>
                                                <div class="col-md-6  ">{{ !empty($result->last_cma_arv_recommendations->recommended_cma_arv)?$result->last_cma_arv_recommendations->recommended_cma_arv:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">CMA/ARV Date</label></div>
                                                <div class="col-md-6 ">----</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">County Value</label></div>
                                                <div class="col-md-6 ">{{ !empty($result->county_value)?$result->county_value:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Estimated Consturction/Renovation Cost</label></div>
                                                <div class="col-md-6 " *ngIf="$result->property_acquisition_a_to_b_second">{{ !empty($result->property_acquisition_a_to_b_second->house_construction_est)?$result->property_acquisition_a_to_b_second->house_construction_est:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Vacant/ Rented/ Occupied</label></div>
                                                <div class="col-md-6 ">----</div>
                                                </div>
                                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Rents Zestimate</label></div>
                                                <div class="col-md-6 ">{{ !empty($result->last_cma_arv_recommendations->rents_zestimate)?$result->last_cma_arv_recommendations->rents_zestimate:$notAvail}}</div>
                                                </div>
                                
                                        </fieldset>
                                        </div>
                                        <div class="col-md-6">
                                        <fieldset>
                                                <legend><b>Home Buyer Information</b></legend>
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Estimated Project Rate of Return</label></div>
                                                <div class="col-md-6 ">{{ !empty($result->wholesale_buyer_strategy->est_prp_rate_of_return)?$result->wholesale_buyer_strategy->est_prp_rate_of_return:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Estimated Annualized Rate of Return</label></div>
                                                <div class="col-md-6 ">{{ !empty($result->wholesale_buyer_strategy->est_ann_return_aft_fnl_close)?$result->wholesale_buyer_strategy->est_ann_return_aft_fnl_close:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Estimated Profit</label></div>
                                                <div class="col-md-6 ">{{ !empty($result->wholesale_buyer_strategy->est_payout_split)}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Contract Purchase Price</label></div>
                                                <div class="col-md-6 " *ngIf="$result->property_acquisition_a_to_b_first">{{ !empty($result->property_acquisition_a_to_b_first->contract_purchase_price_est)?$result->property_acquisition_a_to_b_first->contract_purchase_price_est:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-6"><label class="col-form-label">Amount to Purchase (Total Cost to Buy A to B)</label></div>
                                                <div class="col-md-6 " *ngIf="$result->property_acquisition_a_to_b_second">{{ !empty($result->property_acquisition_a_to_b_second->total_cost_to_buy_a_to_b_est)?$result->property_acquisition_a_to_b_second->total_cost_to_buy_a_to_b_est:$notAvail}}</div>
                                                </div>
                                
                                                <div class="form-group row m-b-0">
                                                <div class="col-md-12"><label class="col-form-label"><b>Notes:</b>---- </label></div>                
                                                </div>
                                        </fieldset>
                                        </div>
                                </div>

                                </div>
                                <!-- end col-12 -->
                        </div>
                </div>
                </div>
</section>
@include('consumer.includes.footer')
<!-- row end -->
