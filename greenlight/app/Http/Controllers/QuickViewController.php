<?php
/**
 * Created By Rativardhan Singh Sengar  5/18/19 2:13 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/18/19 2:13 PM
 */

namespace App\Http\Controllers;
use App\Helpers\CommonHelper;
use App\Exports\CommonExport;

use App\Services\HouseBuyItService;
use App\Services\HouseTokenService;

use App\Models\PropertyModel;
use App\Services\PropertyService;
use App\Services\UserFavoritesService;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\DB;
class QuickViewController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $houseTokenService;
    private $propertyService;
    private $houseBuyItService;
    private $userFavoritesService;


    public function __construct(Request $request
        , HouseTokenService $houseTokenService
        , PropertyService $propertyService
        , HouseBuyItService $houseBuyItService
        , UserFavoritesService $userFavoritesService

    )
    {
        Log::info("AuthController: __construct called");
        $this->request = $request;
        $this->houseTokenService = $houseTokenService;
        $this->propertyService = $propertyService;
        $this->houseBuyItService = $houseBuyItService;
        $this->userFavoritesService = $userFavoritesService;

    }

    public function public($token,$address) {
        
        ## check token is valid or not
        $info = $this->houseTokenService->isTokenExists($token);
       
        if (empty($info))
        {
            return back()->withErrors([ 'error' => __('messages.not_valid_token')]);

            //return response()->json(['status' => 'failed','message' => __('messages.not_valid_token')], 200);
        }
        if(!empty(\Cookie::get('token'))){
            return redirect('setup/home/quick-view/'.$token.'/'.$address);
        }
        $house_id = $info->house_id;

        $info = PropertyModel::

        select(
            'house_id','zpid','address','city','county','state','zip','total_living_sqft'
            , 'year_built','bed','bath','lot_acreage_sf','county_value',
            'property_type','specific_property_type'
        )->
        with(
            [
                "last_sale_details"=> function($query) {
                    $query->select(['house_id','sale_id','sale_date','case_number','opening_bid'
                                    ,'sale_time']);
                },
                "mortgage_liens"=> function($query) {
                    $query->select(['house_id','loan_type']);
                },
                "local_real_estate_details"=> function($query) {
                    $query->select(['house_id','zillow_url','zestimate']);
                },
                "property_descriptions",
                "last_sale_details"=> function($query) {
                    $query->select(['house_id','sale_id','sale_date','case_number','opening_bid'
                                    ,'sale_time'])->whereNotNull('sale_date')->orderBy('sale_id','desc');
                },
                "front_picture"=> function($query) {
                    $query->select(['house_id','org_name','store_name']);
                },

                "last_cma_arv_recommendations"=> function($query) {
                    $query->select(['house_id','recommended_cma_arv','rents_zestimate','rental_rate']);
                },

                //                "cma_arv_recommendations"=> function($query) {
                //                    $query->select(['house_id','recommended_cma_arv','rents_zestimate']);
                //                },

                "property_acquisition_a_to_b_first"=> function($query) {
                    $query->select(['house_id','contract_purchase_price_est']);
                },
                "property_acquisition_a_to_b_second"=> function($query) {
                    $query->select(['house_id','house_construction_est','total_cost_to_buy_a_to_b_est']);
                },
                "wholesale_buyer_strategy"=> function($query) {
                    $query->select(['house_id','est_prp_rate_of_return','est_ann_return_aft_fnl_close','est_payout_split']);
                },
                "geo"
            ]
        )
                             ->where('home_information.house_id',$house_id)->first();
        $notAvail="Not available";
        return view('consumer/setup/quickview',['result'=>$info,'notAvail'=>$notAvail]);
        //return response()->json(['status' => 'success','data' => $info, 'message' => ""], 200);
    }

    public function private($token_or_house_id) {

        ## check token is valid or not
        $isValidInfo = $this->houseTokenService->isTokenExists($token_or_house_id);

        if (empty($isValidInfo))
        {
            // check if valid house_id or not
            $isValidInfo = $this->propertyService->findOneById($token_or_house_id);
        }

        if (empty($isValidInfo))
        {
            return response()->json(['status' => 'failed','message' => __('error_messages.house_id_exists')], 200);
        }

        $house_id = $isValidInfo->house_id;

        $info = PropertyModel::
        select(
            'house_id','zpid','address','city','county','state','zip','total_living_sqft'
            , 'year_built','bed','bath','lot_acreage_sf','county_value',
            'property_type','specific_property_type','parcel_id1'
        )->
        with(
            [
                "last_sale_details"=> function($query) {
                    $query->select(['house_id','sale_id','sale_date','case_number','opening_bid',
                                    'sale_type','sale_status','sale_time']);
                },
//                "mortgage_liens"=> function($query) {
//                    $query->select("*");
//                },
//
                "first_liens",
                "second_liens",
                "third_liens",
                "hoa_liens",
                "tax_liens",
                "other_liens",

                "local_real_estate_details"=> function($query) {
                    $query->select(['house_id','zillow_url','zestimate']);
                },

                "last_sale_details"=> function($query) {
                    $query->select(['house_id','sale_id','sale_date','case_number','opening_bid',
                                    'sale_type','sale_status','sale_time','sale_place','priceint',
                                    'trustee'])->whereNotNull('sale_date')->orderBy('sale_id','desc');
                },
                "front_picture"=> function($query) {
                    $query->select(['house_id','org_name','store_name']);
                },

                "last_cma_arv_recommendations"=> function($query) {
                    $query->select(['house_id','recommended_cma_arv','rents_zestimate']);
                },
                "cma_arv_recommendations"=> function($query) {
                    $query->select(['house_id',"specific_demand","general_demand","days_on_market","wholetail_value","recommended_cma_arv","user_id",
                                    "phase_renovation","rents_zestimate","date","info_added_by","comp_url_1","comp_url_2","comp_url_3","comp_url_4",
                                  ]);
                    $query->addSelect(DB::raw("CONCAT(price_sqft_sale_comps_from,' To ',price_sqft_sale_comps_to) price_sqft_sale_comps"));
                    $query->addSelect(DB::raw("CONCAT(price_sqft_sold_comps_from,' To ',price_sqft_sold_comps_to) price_sqft_sold_comps"));
                    $query->with(['user'=> function($query) {
                        $query->select(['id','first_name','last_name','username']);
                    }]);
                },

                "property_acquisition_a_to_b_first"=> function($query) {
                    $query->select(['house_id','contract_purchase_price_est']);
                },
                "property_acquisition_a_to_b_second"=> function($query) {
                    $query->select(['house_id','house_construction_est','total_cost_to_buy_a_to_b_est']);
                },
                "wholesale_buyer_strategy"=> function($query) {
                    $query->select(['house_id','est_prp_rate_of_return','est_ann_return_aft_fnl_close','est_payout_split']);
                },
                "property_descriptions",

                "owner_info" =>   function ($query) {
                    $query->select(['house_id','full_name','full_address','phone']);
                },
                "borrower_info"=> function($query) {
                    $query->select(['house_id','full_name']);
                },
                "geo",
            ]
        )
        ->where('home_information.house_id',$house_id)->first();

        $head = $this->houseBuyItService->getDownBuyitUser($house_id,1 );

        $top_message = [];
        if(!empty($head))
        {
            foreach ($head as $userPosition)
            {
                $top_user = CommonHelper::nameFormat($userPosition);

                if ($userPosition->position < 10)
                    $u_position = ucwords($this->houseBuyItService->getBuyitPositionLetterConvert($userPosition->position));
                else
                    $u_position = $userPosition->position . 'th';

                $top_message[] = $top_user . ' is in ' . $u_position . ' Position as a Buyer';
            }
        }

        $head = $this->houseBuyItService->getDownBuyitUser($house_id,1,'lender');
        if(!empty($head))
        {
            foreach ($head as $userPosition)
            {
                $top_user = CommonHelper::nameFormat($userPosition);

                if ($userPosition->position < 10)
                    $u_position = ucwords($this->houseBuyItService->getBuyitPositionLetterConvert($userPosition->position));
                else
                    $u_position = $userPosition->position . 'th';

                $top_message[] = $top_user . ' is in Lender Position.';
            }
        }

        $head = $this->houseBuyItService->getDownBuyitUser($house_id,1,'subto');
        if(!empty($head))
        {
            foreach ($head as $userPosition)
            {
                $top_user = CommonHelper::nameFormat($userPosition);

                if ($userPosition->position < 10)
                    $u_position = ucwords($this->houseBuyItService->getBuyitPositionLetterConvert($userPosition->position));
                else
                    $u_position = $userPosition->position . 'th';

                $top_message[] = $top_user . ' is in ' . $u_position . '  Position as a Subto';
            }
        }
        $lenderRequest= $this->houseBuyItService->getContactReqestPositions($house_id,1,'lender');
        $lender_request=[];
        if(!empty($lenderRequest))
        {
            foreach ($lenderRequest as $userPosition)
            {
                $top_user = CommonHelper::nameFormat($userPosition);

                if ($userPosition->position < 10)
                    $u_position = ucwords($this->houseBuyItService->getBuyitPositionLetterConvert($userPosition->position));
                else
                    $u_position = $userPosition->position . 'th';

                $lender_request[] = $top_user . ' is in ' . $u_position . '  Position as a Lender';
            }
        }

        $subToRequest= $this->houseBuyItService->getContactReqestPositions($house_id,1,'subto');
        $subto_request=[];
        if(!empty($subToRequest))
        {
            foreach ($subToRequest as $userPosition)
            {
                $top_user = CommonHelper::nameFormat($userPosition);

                if ($userPosition->position < 10)
                    $u_position = ucwords($this->houseBuyItService->getBuyitPositionLetterConvert($userPosition->position));
                else
                    $u_position = $userPosition->position . 'th';

                $subto_request[] = $top_user . ' is in ' . $u_position . '  Position as a contact owner';
            }
        }

        $info['lender_request'] =$lender_request;
        $info['subto_request'] =$subto_request;
        $info['buy_it_request'] = $top_message;
        $info['isFavourite'] = $this->userFavoritesService->isFavourite($house_id);
        return response()->json(['status' => 'success','data' => $info, 'message' => ""], 200);
    }



}
