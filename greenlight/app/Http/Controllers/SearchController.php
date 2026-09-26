<?php
/**
 * Created By Rativardhan Singh Sengar  3/11/19 10:47 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/7/19 10:47 PM
 */

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;
use App\Models\PropertyModel;
use App\Services\HouseBuyItService;
use App\Services\WholesaleBuyerStrategyServiceExtra;
use Illuminate\Support\Facades\DB;
use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Models\SaleDetailsModel;
use App\Services\UserService;

class SearchController extends Controller {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $houseBuyItService;
    private $userService;

    public function __construct(Request $request
    , HouseBuyItService $houseBuyItService
    ,UserService $userService
    ) {
        Log::info("SearchController: __construct called");
        $this->request = $request;
        $this->houseBuyItService = $houseBuyItService;
        $this->userService = $userService;
    }

    # WHERE CustomerName LIKE 'a%'	Finds any values that start with "a"
    private function startLikeWhere($query, $field, $value) {
        if (empty($value)) return $query;
        return $query->where($field, 'LIKE', "$value%");
    }

    private function likeWhere($query, $field, $value) {
        if (empty($value)) return $query;
        return $query->where($field, 'LIKE', "%$value%");
    }

    private function where($query, $field, $value, $condition = "=") {
        if (empty($value)) return $query;
        return $query->where($field, $condition, $value);
    }

    private function whereBetween($query, $field, $from, $to) {
        if (!empty($from)) $query->where($field, '>=', $from);

        if (!empty($to)) $query->where($field, '<=', $to);

        return $query;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        
        Log::info("SearchController: index called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if ($limit > 2000) $limit = 2000;
        $offset             = $this->request->get('offset') ? $this->request->get('offset') : 0;
        $address            = $this->request->get('address')?$this->request->get('address'):$this->request->get('property_address');
        $city               = $this->request->get('city');
        $county             = $this->request->get('county');
        $state              = $this->request->get('state');
        $zip                = $this->request->get('zip_code');
        $case_number        = $this->request->get('case_number');
        $trustee            = $this->request->get('trustee');
        $owner_full_name    = $this->request->get('owner_name');
        $borrower_full_name = $this->request->get('before_sale_spread');
        $entity_llc_name    = $this->request->get('entity_llc_name');
        $subdivision        = $this->request->get('sub_devision');
        $legal_description  = $this->request->get('legal_desc');
        $loan_type          = $this->request->get('loan_type');
        $sale_type          = $this->request->get('sale_type');
        $bidder_name        = $this->request->get('bidder_name');
        $potential_buy      = $this->request->get('potential_buy');
        $opening_bid        = $this->request->get('opening_bid');
        $low_first          = $this->request->get('low_first');
        $low_first_w_lien   = $this->request->get('low_first_w_lien');
        $no_cma_arv         = $this->request->get('no_cma_arv');
        $upset_bid          = $this->request->get('upset_bid');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');
        $year_built         = $this->request->get('year_built');
        $first_sort_by          = $this->request->get('first_sort_by');
        $order_by               = $this->request->get('order_by');
        $winning_bidder_name    = $this->request->get('winning_bidder_name');
        $redemption_date_from   = $this->request->get('redemption_date_from');
        $redemption_date_to     = $this->request->get('redemption_date_to');

        $from_lot_acr_sqft          = $this->request->get('from_lot_acr_sqft');
        $to_lot_acr_sqft            = $this->request->get('to_lot_acr_sqft');
        $from_living_sqft           = $this->request->get('from_living_sqft');
        $to_living_sqft             = $this->request->get('to_living_sqft');

        $second_sort_by             = $this->request->get('second_sort_by');
        $second_sort_order          = $this->request->get('second_sort_order');
        $property_type              = $this->request->get('property_type');
        $specific_property_location = $this->request->get('specific_property_location');
        $tax_sale                   = $this->request->get('tax_sale');
        $pick_from                  = $this->request->get('pick_from');
        $w_bids                     = $this->request->get('w_bids');
        $excess_surplus_funds       = $this->request->get('excess_surplus_funds');   
        $want_to_sale               = $this->request->get("want_to_sale");
        $order_by = strtolower($order_by);

        $info = PropertyModel::select([
                                       "home_information.house_id"## Needed this field, Important this line
                                      ]);

        if(!empty($address))
        {
            $find_short_address = config('constants.find_short_address');
            $replace_detail_address = \config('constants.replace_detail_address');
            $full_replace_count1 = 0;
            $full_phrase = str_ireplace($find_short_address, $replace_detail_address, $address, $full_replace_count1);
            $small_replace_count2 = 0;
            $small_phrase = str_ireplace($replace_detail_address, $find_short_address, $address, $small_replace_count2);

            $info->where(function ($query) use ($address, $full_phrase, $small_phrase,$full_replace_count1,$small_replace_count2) {
                
                $query->where('home_information.address', 'LIKE', "%$address%");
                if ($full_replace_count1 > 0) {
                    $query->orWhere('home_information.address', 'LIKE', $full_phrase);
                }
                if ($small_replace_count2 > 0) {
                    $query->orWhere('home_information.address', 'LIKE', $small_phrase);
                }
            });
        }

        $info = $this->likeWhere($info, 'home_information.city', $city);
        //$info = $this->likeWhere($info, 'home_information.county', $county);

           if(!empty($county)){
             $countyArray = explode(",", $county);
             $countyArray=array_map('trim', $countyArray);
             $countyArray = array_filter($countyArray);
             $info->whereIn('home_information.county', $countyArray);
           }


        $info = $this->where($info, 'state', $state);
        $info = $this->where($info, 'zip', $zip);
        $info = $this->where($info, 'subdivision', $subdivision);
        $info = $this->where($info, 'year_built', $year_built);
        $info = $this->where($info, 'property_type', $property_type);
        
        if(!empty($specific_property_location)){
            $info->whereIn('specific_property_location', explode(',',$specific_property_location));
        }
        if(!empty($from_lot_acr_sqft) && !empty($to_lot_acr_sqft)){
            $info = $this->whereBetween($info, 'lot_acreage_sf', $from_lot_acr_sqft, $to_lot_acr_sqft);
        }

        if(!empty($from_living_sqft) && !empty($to_living_sqft)){
            $info = $this->whereBetween($info, 'total_living_sqft', $from_living_sqft, $to_living_sqft);
        }
        if(!empty($tax_sale) && !empty($pick_from)){
            $this->where($info, 'tax_sale', $tax_sale);
            $this->where($info, 'pick_from', $pick_from);
        }
        if(!empty($excess_surplus_funds)){
            $this->where($info, 'excess_surplus_funds', $excess_surplus_funds);
        }
        if(!empty($want_to_sale)){
            $this->where($info, 'want_to_sale', $want_to_sale);
        }
        
        $isManyJoin = false;

        if($potential_buy == 1 || $low_first == 1 || $low_first_w_lien == 1 || $upset_bid == 1)
        {
            $info = $this->where($info, 'potential_buy', ($potential_buy ? 1 : 0));
            $info = $this->where($info, 'lf_dead_property', ($low_first ? 1 : 0));
            $info = $this->where($info, 'lf_wo_auction_outbid', ($low_first_w_lien ? 1 : 0));
            $info = $this->where($info, 'upst_auction_no_bid', ($upset_bid ? 1 : 0));
            $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
            $isManyJoin = true;
        }

        if(!empty($legal_description))
        {
            $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
            $info = $this->startLikeWhere($info, 'legal_description', $legal_description);
            $isManyJoin = true;
        }

        if (!empty($loan_type)) {
            $info->leftJoin('mortgage_liens', 'home_information.house_id', '=', 'mortgage_liens.house_id');
           // $info       = $this->likeWhere($info, 'mortgage_liens.trustee', $trustee);
            $info       = $this->likeWhere($info, 'mortgage_liens.loan_type', $loan_type);
            $isManyJoin = true;
        }

        


        if (!empty($owner_full_name)) {
            $info->leftJoin('owner_info', 'home_information.house_id', '=', 'owner_info.house_id');
            $info       = $this->likeWhere($info, 'owner_info.full_name', $owner_full_name);
            $isManyJoin = true;
        }
        if (!empty($borrower_full_name)) {
            $info->leftJoin('borrower_info', 'home_information.house_id', '=', 'borrower_info.house_id');
            $info       = $this->likeWhere($info, 'borrower_info.full_name', $borrower_full_name);
            $isManyJoin = true;
        }

        if (!empty($case_number) || !empty($opening_bid) || isset($trustee)) {
            
            $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
            $info = $this->likeWhere($info, 'sale_details.case_number', $case_number);
            $info = $this->where($info, 'sale_details.trustee', $trustee);
            if (!empty($opening_bid)) $info->where('sale_details.opening_bid', '>', 0);

            $isManyJoin = true;
        }

        if (isset($sale_type)) {
            $info->leftJoin(DB::raw("(SELECT house_id,MAX(sale_id) as sale_id FROM sale_details where sale_date is not null GROUP BY house_id) as last_sale_details"), 'home_information.house_id', '=', 'last_sale_details.house_id');
            $info->join('sale_details as saleDetailInfo', 'last_sale_details.sale_id', '=', 'saleDetailInfo.sale_id');
            $info = $this->where($info, 'saleDetailInfo.sale_type', $sale_type);
            $isManyJoin = true;
        }

        
        if ( !empty($sale_date_from) && !empty($sale_date_to) && !empty($w_bids)) {
            
            $saledateQuery=SaleDetailsModel::select(['home_information.house_id'])
            ->join('home_information','home_information.house_id','sale_details.house_id')
            ->join('sale_bidder','sale_bidder.sale_id','sale_details.sale_id')
            ->whereNull('sale_bidder.deleted_at')
            ->where('sale_bidder.name_upset_bidder',"!=","")
            ->whereNotNull('sale_bidder.name_upset_bidder')
            ->whereNotNull('sale_date')
            ->whereNull('home_information.deleted_at')
            ->groupBy('home_information.house_id')
            ->havingRaw("MAX(sale_date) >= '$sale_date_from'")
            ->havingRaw("MAX(sale_date) <= '$sale_date_to'");

            if(!empty($state)){
                $saledateQuery->where('state', $state);
            }
            if(!empty($county)){
                $saledateQuery->whereIn('county', $countyArray);
            }
            $saleResult=$saledateQuery->distinct()->get();
            $house_ids=[];
            foreach($saleResult as $data){
                $house_ids[]=$data->house_id;
            }
            $info->whereIn('home_information.house_id',$house_ids);
            $isManyJoin = true;

        }
        else{

            if ( !empty($sale_date_from) || !empty($sale_date_to)) {
                
                $saledateQuery=SaleDetailsModel::select(['home_information.house_id'])
                ->join('home_information','home_information.house_id','sale_details.house_id')
                ->whereNotNull('sale_date')->whereNull('home_information.deleted_at')
                ->groupBy('home_information.house_id')
                ->havingRaw("MAX(sale_date) >= '$sale_date_from'")
                ->havingRaw("MAX(sale_date) <= '$sale_date_to'");

                if(!empty($state)){
                    $saledateQuery->where('state', $state);
                }
                if(!empty($county)){
                    $saledateQuery->whereIn('county', $countyArray);
                }
                $saleResult=$saledateQuery->distinct()->get();
                $house_ids=[];
                foreach($saleResult as $data){
                    $house_ids[]=$data->house_id;
                }
                $info->whereIn('home_information.house_id',$house_ids);
                $isManyJoin = true;

                // $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
                // $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
                // $isManyJoin = true;
            }
    
            if (!empty($w_bids)) {
                $info->leftJoin( DB::raw("(SELECT house_id,sale_id,deleted_at,name_upset_bidder from sale_bidder group by house_id) as saleBidder"),'home_information.house_id', '=', 'saleBidder.house_id' );
                $info->whereNull('saleBidder.deleted_at');
                $info->where('saleBidder.name_upset_bidder',"!=","");
                $info->whereNotNull('saleBidder.name_upset_bidder');
                $isManyJoin = true;
             }
    
        }
       

        if (!empty($bidder_name)  || !empty($entity_llc_name)) {
            $info->leftJoin('sale_bidder', 'home_information.house_id', '=', 'sale_bidder.house_id');
            $info = $this->likeWhere($info, 'sale_bidder.name_upset_bidder', $bidder_name);
            $info->whereNull('sale_bidder.deleted_at');
            $info->where('sale_bidder.name_upset_bidder',"!=","");
            $info->whereNotNull('sale_bidder.name_upset_bidder');
            // $info = $this->likeWhere($info, 'sale_bidder.name_upset_bidde', $winning_bidder_name);
            //$info = $this->likeWhere($info, 'sale_bidder.name_upset_bidder', $entity_llc_name);
            
            $isManyJoin = true;
        }
        if (!empty($winning_bidder_name)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_details.sale_id) as sale_id FROM sale_details where sale_date is not null group by house_id) as winningSaleBidder"), 'home_information.house_id', '=', 'winningSaleBidder.house_id');
            $info->leftJoin(DB::raw("(select sale_id,bidder_id,name_upset_bidder,deleted_at FROM sale_bidder WHERE name_upset_bidder!='' and name_upset_bidder is not null and  deleted_at is null 
            AND name_upset_bidder LIKE '%".$winning_bidder_name."%' ORDER BY bidder_id DESC) saleBidder"),'saleBidder.sale_id','=','winningSaleBidder.sale_id');
            $info->whereNull('saleBidder.deleted_at');
            $info->where('saleBidder.name_upset_bidder',"!=","");
            $info->whereNotNull('saleBidder.name_upset_bidder');
            $info = $this->likeWhere($info, 'saleBidder.name_upset_bidder', $winning_bidder_name);
            $isManyJoin = true;
        }


         


        if (!empty($no_cma_arv)) {
            $info->leftJoin('cma_arv_recommendations', 'home_information.house_id', '=', 'cma_arv_recommendations.house_id');
            //$info->where('cma_arv_recommendations.recommended_cma_arv', '=', 0);
            $info->havingRaw('MAX(cma_arv_recommendations.recommended_cma_arv) is null', []);
            $info->orHavingRaw('MAX(cma_arv_recommendations.recommended_cma_arv) = ?', [0]);
           // $info->havingRaw('MAX(cma_arv_recommendations.recommended_cma_arv)  >  ?) ', [null]);
           // $info->orHavingRaw('MAX(cma_arv_recommendations.recommended_cma_arv)  = ?', [0]);
            $isManyJoin = true;
        }

        if (!empty($redemption_date_from) || !empty($redemption_date_to) || !empty($entity_llc_name)) {
            $info->leftJoin('mortgage_hoa', 'home_information.house_id', '=', 'mortgage_hoa.house_id');
            $info->leftJoin('mortgage_tax', 'home_information.house_id', '=', 'mortgage_tax.house_id');
  
            if (!empty($redemption_date_from) || !empty($redemption_date_to)){
              $info->where(function ($q) use ($redemption_date_to,$redemption_date_from){
                  $q->where(function ($q) use ($redemption_date_to,$redemption_date_from){
                      $q->where('mortgage_hoa.redemption_expires', '<=', $redemption_date_to);
                      $q->where('mortgage_hoa.redemption_expires', '>=', $redemption_date_from);
                    });
      
                  $q->orWhere(function ($q) use ($redemption_date_to,$redemption_date_from){
                        $q->where('mortgage_tax.redemption_expires', '<=', $redemption_date_to);
                        $q->where('mortgage_tax.redemption_expires', '>=', $redemption_date_from);
                      });
                });
            }
            
            if(!empty($entity_llc_name)){
                $info->where(function ($q) use ($entity_llc_name){
                    $q->where('mortgage_hoa.winning_bidder', 'LIKE', "%$entity_llc_name%");
                    $q->orWhere('sale_bidder.name_upset_bidder', 'LIKE',"%$entity_llc_name%");
                    $q->orWhere('mortgage_tax.winning_bidder', 'LIKE', "%$entity_llc_name%");
                });
            }
  
        }


        if(!empty($first_sort_by) && array_key_exists($first_sort_by, config('property_information.first_sort_by')))
        {

            if(empty($order_by) || !array_key_exists($order_by, config('property_information.order_by')))
            {
                $order_by = 'asc';
            }

            if($first_sort_by == "address")
            {
                $info->where( 'home_information.address', "!=", null);
                $info->orderBy('home_information.address',$order_by);
            }
            if($first_sort_by == "city")
            {
                $info->where( 'home_information.city', "!=", null);
                $info->where( 'home_information.city', "!=", "");
                $info->orderBy('home_information.city',$order_by);
            }
            if($first_sort_by == "zip")
            {
                $info->where( 'home_information.zip', "!=", null);
                $info->where( 'home_information.zip', "!=", "");
                $info->orderBy('home_information.zip',$order_by);
            }

            if($first_sort_by == "county")
            {
                $info->where( 'home_information.county', "!=", null);
                $info->where( 'home_information.county', "!=", "");
                $info->orderBy('home_information.county',$order_by);
            }
            if($first_sort_by == "state")
            {
                $info->where( 'home_information.state', "!=", null);
                $info->where( 'home_information.state', "!=", "");
                $info->orderBy('home_information.state',$order_by);
            }
            if($first_sort_by == "year_built")
            {
                $info->where( 'home_information.year_built', "!=", null);
                $info->where( 'home_information.year_built', "!=", "");
                $info->orderBy('home_information.year_built',$order_by);
            }

            if($first_sort_by == "ppsf")
            {
                $info->where( 'home_information.cost_per_sqft', "!=", null);
                $info->where( 'home_information.cost_per_sqft', ">", 0);
                $info->orderBy('home_information.cost_per_sqft',$order_by);
            }


            if($first_sort_by == "sale_price_opening_bid")
            {
                if (!(!empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || !empty($opening_bid))) {
                    $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
                }

                $info->where( 'sale_details.opening_bid', "!=", null);
                $info->where( 'sale_details.opening_bid', ">", 0);
                //$info->where( 'home_information.opening_bid', "!=", "");
                //$info->orderBy('sale_details.opening_bid',$order_by);

                $info->orderBy(\DB::raw('max(sale_details.opening_bid)'),$order_by);
                $info->groupBy('home_information.house_id');

            }
            if($first_sort_by == "sale_date")
            {
                if (!(!empty($case_number) || !empty($opening_bid))) {
                    $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
                }

                $info->where( 'sale_details.sale_date', "!=", null);
                //$info->orderBy('max(sale_details.sale_date)',$order_by);
                $info->orderBy(\DB::raw('max(sale_details.sale_date)'),$order_by);
                $info->groupBy('home_information.house_id');
            }
            if($first_sort_by == "sp_number")
            {
                if (!(!empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || !empty($opening_bid))) {
                    $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
                }

                $info->where( 'sale_details.case_number', "!=", null);
                $info->where( 'sale_details.case_number', "!=", "");
                $info->orderBy('sale_details.case_number',$order_by);
            }


            if($first_sort_by == "owner_name")
            {
                if (empty($owner_full_name)) {
                    $info->leftJoin('owner_info', 'home_information.house_id', '=', 'owner_info.house_id');
                }
                $info->where( 'owner_info.full_name', "!=", null);
                $info->orderBy('owner_info.full_name',$order_by);
            }
        }

        if(!empty($second_sort_by) && array_key_exists($second_sort_by, config('property_information.first_sort_by')))
        {

            if(empty($second_sort_order) || !array_key_exists($second_sort_order, config('property_information.order_by')))
            {
                $second_sort_order = 'asc';
            }

            if($second_sort_by == "address")
            {
                $info->where( 'home_information.address', "!=", null);
                $info->orderBy('home_information.address',$second_sort_order);
            }
            if($second_sort_by == "city")
            {
                $info->where( 'home_information.city', "!=", null);
                $info->where( 'home_information.city', "!=", "");
                $info->orderBy('home_information.city',$second_sort_order);
            }
            if($second_sort_by == "zip")
            {
                $info->where( 'home_information.zip', "!=", null);
                $info->where( 'home_information.zip', "!=", "");
                $info->orderBy('home_information.zip',$second_sort_order);
            }

            if($second_sort_by == "county")
            {
                $info->where( 'home_information.county', "!=", null);
                $info->where( 'home_information.county', "!=", "");
                $info->orderBy('home_information.county',$second_sort_order);
            }
            if($second_sort_by == "state")
            {
                $info->where( 'home_information.state', "!=", null);
                $info->where( 'home_information.state', "!=", "");
                $info->orderBy('home_information.state',$second_sort_order);
            }
            if($second_sort_by == "year_built")
            {
                $info->where( 'home_information.year_built', "!=", null);
                $info->where( 'home_information.year_built', "!=", "");
                $info->orderBy('home_information.year_built',$second_sort_order);
            }

            if($second_sort_by == "ppsf")
            {
                $info->where( 'home_information.cost_per_sqft', "!=", null);
                $info->where( 'home_information.cost_per_sqft', ">", 0);
                $info->orderBy('home_information.cost_per_sqft',$second_sort_order);
            }


            if($second_sort_by == "sale_price_opening_bid")
            {
                if (!(!empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || !empty($opening_bid))) {
                    $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
                }

                $info->where( 'sale_details.opening_bid', "!=", null);
                $info->where( 'sale_details.opening_bid', ">", 0);
                //$info->where( 'home_information.opening_bid', "!=", "");
                //$info->orderBy('sale_details.opening_bid',$order_by);

                $info->orderBy(\DB::raw('max(sale_details.opening_bid)'),$second_sort_order);
                $info->groupBy('home_information.house_id');

            }
            if($second_sort_by == "sale_date")
            {
                if (!(!empty($sale_type) || !empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || !empty($opening_bid))) {
                   $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
                }

                $info->where( 'sale_details.sale_date', "!=", null);
                //$info->orderBy('max(sale_details.sale_date)',$order_by);
                $info->orderBy(\DB::raw('max(sale_details.sale_date)'),$second_sort_order);
                $info->groupBy('home_information.house_id');
            }
            if($second_sort_by == "sp_number")
            {
                if (!(!empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || !empty($opening_bid))) {
                    $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
                }

                $info->where( 'sale_details.case_number', "!=", null);
                $info->where( 'sale_details.case_number', "!=", "");
                $info->orderBy('sale_details.case_number',$second_sort_order);
            }


            if($second_sort_by == "owner_name")
            {
                if (empty($owner_full_name)) {
                    $info->leftJoin('owner_info', 'home_information.house_id', '=', 'owner_info.house_id');
                }
                $info->where( 'owner_info.full_name', "!=", null);
                $info->orderBy('owner_info.full_name',$second_sort_order);
            }
        }


        if ($isManyJoin === false) {
            $total = $info->count();
        }
        else {

            $t = $info->groupBy('home_information.house_id');
            $total = DB::table(DB::raw("(" . $t->toSql() . ") as res"))
                       ->mergeBindings($t->getQuery())
                       ->count();

        }



        if(empty($legal_description))
        {
            $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        }

        $info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id');
        // $info->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id');
        $info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id');
        $info->select([
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip",
            "home_information.total_living_sqft",
            "home_information.year_built",
            "home_information.bed",
            "home_information.bath",
            'home_information.lot_acreage_sf',
            'home_information.parcel_id1',
            'home_information.parcel_id2',
            'home_information.subdivision',
            'home_information.total_living_sqft',
            'home_information.county_value',
           //  "property_descriptions.*",
           //  "local_real_estate.zestimate",
            "home_information.house_id"## Needed this field, Important this line
        ]);
        //$info->addSelect(["sale_bidder.sale_id","sale_bidder.bidder_id","sale_bidder.name_upset_bidder","sale_bidder.amount_of_bid","sale_bidder.last_date_to_upset_bid"]);
        $info->with([
            "geo",
            "property_descriptions",
            "local_real_estate_details",
            "user_drive_list"=> function ($query) {
                $query->select(['house_id', 'user_id'])->where('user_id',$this->userService->user_id());
            },
            "off_site"=> function ($query) {
                $query->select(['house_id', 'off_site', 'updated_at'
                ]);
            },
           

            "last_cma_arv_recommendations"       => function ($query) {
            $query->select(['house_id', 'recommended_cma_arv', 'general_demand', 'specific_demand','days_on_market']);
            },
            "cma_arv_recommendations"       => function ($query) {
                $query->select(['house_id', 'info_added_by', 'user_id', 'date']);
            },
            "cma_arv_recommendations.user"       => function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "first_liens"                         => function ($query) {
            $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                            ,'no_str_no_appt'
                           ]);
            },
            "second_liens"                                => function ($query) {
                $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                ,'no_str_no_appt'
                               ]);
            },
            "third_liens"                         => function ($query) {
                $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                ,'no_str_no_appt'
                               ]);
            },

            "hoa_liens"  => function ($query) {
                $query->select(['house_id', 'hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_foreclosing'
                                ,'no_str'
                               ]);
            },
            "last_borrower_info" =>   function ($query) {
                $query->select(['house_id', 'full_name']);
            }
            ,"last_owner_info" =>   function ($query) {
                $query->select(['house_id', 'full_name']);
            },

            // "wholesale_buyer_strategy"=> function($query) {
            //     $query->select(['house_id','est_close_date_a_to_b']);
            // },
            "front_picture"                      => function ($query) {
            $query->select(['house_id', 'org_name', 'store_name']);
        },
            "buy_it_1"=> function ($query) {
                $query->select(["house_buyit_id"
                                ,"house_id"
                                ,"user_id"
                                ,"position"
                                ,"email"
                                ,"first_name","last_name"]);
            },
            "buy_it_2"=> function ($query) {
                $query->select(["house_buyit_id"
                                ,"house_id"
                                ,"user_id"
                                ,"position"
                                ,"email"
                                ,"first_name","last_name"]);
            },
            'last_invite_date' =>function ($query) {
                $query->select(["invitations_info_id"
                                ,"house_id"
                                ,"created_at"]);
            },

        ]
        );

        if (!empty($bidder_name)) {

             $info->with([ 
            "bidder_sale_details" => function ($query)  use ($bidder_name){
                $query->select(['sale_details.house_id', 'sale_details.sale_id', 'trustee', 'sale_date', 'sale_time'
                            , 'case_number', 'priceint', 'opening_bid', 'sale_type', 'sale_status' , 'sale_details.nos_by','sale_details.nos_date'
                           ])->leftJoin('sale_bidder','sale_bidder.sale_id','=','sale_details.sale_id')->where("sale_bidder.name_upset_bidder",'LIKE', "%$bidder_name%");
            },
            "bidder_sale_details.nos" => function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            'bidder_sale_details.last_bidder'      => function ($query) {
            $query->select(['house_id', 'sale_id','bidder_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                            ,'name_upset_bidder', 'name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                            ,'im_by','im_date'
                            ]);
            },
            'bidder_sale_details.last_bidder.im'=> function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            'bidder_sale_details.sale_descriptions'=> function ($query) {
                $query->select(["sale_id","notice_of_foreclosure"]);
            },
            ]);
        }
        else{
            $info->with([ 
                "last_sale_details" => function ($query) {
                $query->select(['house_id', 'sale_id', 'trustee', 'sale_date', 'sale_time'
                                , 'case_number', 'priceint', 'opening_bid', 'sale_type', 'sale_status'
                                , 'nos_by','nos_date','auction_by','auction_date'
                               ]);
                },
                "last_sale_details.nos"=> function ($query) {
                    $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                },
                "last_sale_details.auction"=> function ($query) {
                    $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                },
                
                'last_sale_details.last_bidder' => function ($query) {
                $query->select(['house_id', 'sale_id','bidder_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                                ,'name_upset_bidder','name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                                ,'im_by','im_date'
                                ]);
                },
                'last_sale_details.last_bidder.im' => function ($query) {
                    $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                },
                'last_sale_details.sale_descriptions'=> function ($query) {
                    $query->select(["sale_id","notice_of_foreclosure"]);
                },
                ]);
        }

        if (!empty($w_bids)) {
            $info->with([ 
                'last_sale_details2.last_bidder'=> function ($query) {
                    $query->select(['house_id', 'sale_id','bidder_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                                    ,'name_upset_bidder', 'name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                                    ,'im_by','im_date'
                                    ]);
                    },
                "last_sale_details2.nos"=> function ($query) {
                    $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                },
                
                'last_sale_details2.last_bidder.im'=> function ($query) {
                    $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                },
                'last_sale_details2.sale_descriptions'=> function ($query) {
                    $query->select(["sale_id","notice_of_foreclosure"]);
                },
            ]);
        }

       

        $info = $info->skip(intval($offset))->take(intval($limit))->get();
        if (!empty($bidder_name)) {
            foreach($info as $property){
                unset($property->last_sale_details);
                $property->last_sale_details=$property->bidder_sale_details;
            }
        }  
        
        if (!empty($w_bids)) {
            foreach($info as $property){
                unset($property->last_sale_details);
                $property->last_sale_details=$property->last_sale_details2;
            }
        } 
        if (empty($info)) {
            return response()->json(['status' => 'success', 'total' => $total, 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success', 'total' => $total, 'data' => $info], 200);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function advance() {
        Log::info("SearchController: index called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if ($limit > 200) $limit = 200;
        $offset             = $this->request->get('offset') ? $this->request->get('offset') : 0;

        $as_owner_name       = $this->request->get('as_owner_name');
        $as_owner_email      = $this->request->get('as_owner_email');
        $as_owner_phone      = $this->request->get('as_owner_phone');
        $as_owner_address    = $this->request->get('as_owner_address');
        $as_borrower_name    = $this->request->get('as_borrower_name');
        $as_borrower_phone   = $this->request->get('as_borrower_phone');
        $as_borrower_address = $this->request->get('as_borrower_address');
        $as_borrower_email   = $this->request->get('as_borrower_email');
        $as_fb_username      = $this->request->get('as_fb_username');
        $as_parcel           = $this->request->get('as_parcel');
        $as_properties_owned = $this->request->get('as_properties_owned');
        $as_strategy         = $this->request->get('as_strategy');
        $as_mls              = $this->request->get('as_mls');
        $as_agent            = $this->request->get('as_agent');
        $address             = $this->request->get('address') ? $this->request->get('address') : $this->request->get('property_address');
        $city                = $this->request->get('city');
        $county              = $this->request->get('county');
        $state               = $this->request->get('state');
        $zip                 = $this->request->get('zip');
        $as_late             = $this->request->get('as_late');
        $as_auction_place    = $this->request->get('as_auction_place');
        $as_auction_spread   = $this->request->get('as_auction_spread');
        $as_square_feet      = $this->request->get('as_square_feet');
        $as_amenties_pool    = $this->request->get('as_amenties_pool');
        $as_amenties_spa     = $this->request->get('as_amenties_spa');
        $as_beds             = $this->request->get('as_beds');
        $as_garages          = $this->request->get('as_garages');

        $sale_type           = $this->request->get('sale_type');
        $sale_date_from      = $this->request->get('sale_date_from');
        $sale_date_to        = $this->request->get('sale_date_to');



        $info = PropertyModel::select([
                                          "home_information.house_id"## Needed this field, Important this line
                                      ])
        ;

        $info = $this->likeWhere($info, 'home_information.address', $address);
        $info = $this->startLikeWhere($info, 'city', $city);
        $info = $this->startLikeWhere($info, 'county', $county);
        $info = $this->where($info, 'state', $state);
        $info = $this->where($info, 'zip', $zip);
        $info = $this->where($info, 'parcel_id1', $as_parcel);
        $info = $this->where($info, 'lot_acreage_sf', $as_late, ">=");
        $info = $this->where($info, 'total_living_sqft', $as_square_feet, "<=");

        $info = $this->where($info, 'bed', $as_beds, "<=");
        $info = $this->where($info, 'garages', $as_garages, "=");

        if($as_amenties_pool == 1)
        {
            $info->where(function ($query)
            {
                $query->orWhere('owner_info.pool', '=', 1);
                $query->orWhere('owner_info.pool', '=', 3);
            });
        }

        if($as_amenties_spa == 1)
        {
            $info->where(function ($query)
            {
                $query->orWhere('owner_info.pool', '=', 2);
                $query->orWhere('owner_info.pool', '=', 3);
            });
        }

        $isManyJoin = false;

        if (
            !empty($as_owner_name)
            || !empty($as_owner_email)
            || !empty($as_owner_phone)
            || !empty($as_owner_address)

        ) {
            $info->leftJoin('owner_info', 'home_information.house_id', '=', 'owner_info.house_id');
            $info       = $this->startLikeWhere($info, 'owner_info.full_name', $as_owner_name);
            $info       = $this->startLikeWhere($info, 'owner_info.email', $as_owner_email);

            if(!empty($as_owner_phone))
            {
                $info->where(function ($query) use ($as_owner_phone)
                {
                    $query->orWhere('owner_info.phone', '=', $as_owner_phone);
                    $query->orWhere('owner_info.phone2', '=', $as_owner_phone);
                });
            }

            $info       = $this->startLikeWhere($info, 'owner_info.full_address', $as_owner_address);
            $isManyJoin = true;
        }

        if (
            !empty($as_borrower_name)
            || !empty($as_borrower_email)
            || !empty($as_borrower_phone)
            || !empty($as_borrower_address)

        ) {
            $info->leftJoin('borrower_info', 'home_information.house_id', '=', 'borrower_info.house_id');
            $info       = $this->startLikeWhere($info, 'borrower_info.full_name', $as_borrower_name);
            $info       = $this->startLikeWhere($info, 'borrower_info.email', $as_borrower_email);

            if(!empty($as_borrower_phone))
            {
                $info->where(function ($query) use ($as_borrower_phone)
                {
                    $query->orWhere('borrower_info.phone', '=', $as_borrower_phone);
                $query->orWhere('borrower_info.phone2', '=', $as_borrower_phone);
                });
            }

            $info       = $this->startLikeWhere($info, 'borrower_info.full_address', $as_borrower_address);
            $isManyJoin = true;
        }

        if (!empty($as_auction_place) || !empty($sale_date_from) || !empty($sale_date_to) || isset($sale_type)) {
            $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');

            $info = $this->whereBetween($info, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
            $info = $this->where($info, 'sale_details.sale_type', $sale_type);
            $info = $this->where($info, 'sale_details.sale_place', $as_auction_place);
            // if (!empty($opening_bid)) $info->where('sale_details.opening_bid', '>', 0);
            $isManyJoin = true;
        }


        if ($isManyJoin === false) {
            $total = $info->count();
        }
        else {
            // # ToDO convert this into above count info ..
           // $info->groupBy('home_information.house_id');
            // $total = $info->get()->count();

            $t = $info->groupBy('home_information.house_id');
            $total = DB::table(DB::raw("(" . $t->toSql() . ") as res"))
                       ->mergeBindings($t->getQuery())
                       ->count();

        }

        if(empty($legal_description))
        {
            $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        }

        $info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id');
        // $info->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id');
        $info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id');
        $info->select([
                          "home_information.address",
                          "home_information.city",
                          "home_information.county",
                          "home_information.state",
                          "home_information.zip",
                          "home_information.total_living_sqft",
                          "home_information.year_built",
                          "home_information.bed",
                          "home_information.bath",
                          'home_information.lot_acreage_sf',
                          'home_information.parcel_id1',
                          'home_information.parcel_id2',
                          'home_information.subdivision',
                          'home_information.total_living_sqft',
                          'home_information.county_value',
                          // "property_descriptions.*",
                          // "local_real_estate.zestimate",
                          "home_information.house_id"## Needed this field, Important this line
                      ]);

        $info->with([
                        "geo",
                        "property_descriptions",
                        "local_real_estate_details",
                        "off_site"=> function ($query) {
                            $query->select(['house_id', 'off_site', 'updated_at'
                            ]);
                        },
                        "last_sale_details" => function ($query) {
                            $query->select(['house_id', 'sale_id', 'trustee', 'sale_date', 'sale_time'
                                            , 'case_number', 'priceint', 'opening_bid', 'sale_type', 'sale_status'
                                            , 'nos_by','nos_date','auction_by','auction_date'
                                           ]);
                        },
                        "last_sale_details.nos"       => function ($query) {
                            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                        },

                        "last_sale_details.auction"=> function ($query) {
                            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                        },
                        "last_cma_arv_recommendations"       => function ($query) {
                            $query->select(['house_id', 'recommended_cma_arv', 'general_demand', 'specific_demand']);
                        },
                        "cma_arv_recommendations"       => function ($query) {
                            $query->select(['house_id', 'info_added_by', 'user_id', 'date']);
                        },
                        "cma_arv_recommendations.user"       => function ($query) {
                            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                        },
                        "first_liens"                         => function ($query) {
                            $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                            ,'no_str_no_appt'
                                           ]);
                        },
                        "second_liens"                                => function ($query) {
                            $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                            ,'no_str_no_appt'
                                           ]);
                        },
                        "third_liens"                         => function ($query) {
                            $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                            ,'no_str_no_appt'
                                           ]);
                        },

                        "hoa_liens"  => function ($query) {
                            $query->select(['house_id', 'hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_foreclosing'
                                            ,'no_str'
                                           ]);
                        },

                        'last_sale_details.last_bidder'      => function ($query) {
                            $query->select(['house_id', 'sale_id','bidder_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                                            , 'name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                                            ,'im_by','im_date'
                                           ]);
                        },
                        'last_sale_details.last_bidder.im'      => function ($query) {
                            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                        },
                        'last_sale_details.sale_descriptions'      => function ($query) {
                            $query->select(["sale_id","notice_of_foreclosure"]);
                        },
                        "last_borrower_info" =>   function ($query) {
                            $query->select(['house_id', 'full_name']);
                        }
                        ,"last_owner_info" =>   function ($query) {
                            $query->select(['house_id', 'full_name']);
                        },

                        "wholesale_buyer_strategy"=> function($query) {
                            $query->select(['house_id','est_close_date_a_to_b']);
                        },
                        "front_picture"                      => function ($query) {
                            $query->select(['house_id', 'org_name', 'store_name']);
                        },
                        "buy_it_1"=> function ($query) {
                            $query->select(["house_buyit_id"
                                            ,"house_id"
                                            ,"user_id"
                                            ,"position"
                                            ,"email"
                                            ,"email"
                                            ,"first_name","last_name"]);
                        },
                        "buy_it_2"=> function ($query) {
                            $query->select(["house_buyit_id"
                                            ,"house_id"
                                            ,"user_id"
                                            ,"position"
                                            ,"email"
                                            ,"email"
                                            ,"first_name","last_name"]);
                        },

                        ]
        );

        $info = $info->skip(intval($offset))->take(intval($limit))->get();
        
        if (empty($info)) {
            return response()->json(['status' => 'success', 'total' => $total, 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }
        
        return response()->json(['status' => 'success', 'total' => $total, 'data' => $info], 200);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexHouseId() {
        Log::info("SearchController: index called");
        $house_ids = $this->request->get('house_ids') ? $this->request->get('house_ids') : '';
        if (empty($house_ids)) {
            return response()->json(['status' => 'failed', 'data' => [], 'message' => __("error_messages.record_not_exists")], 400);
        }
        $house_ids = explode(",", $house_ids);
        $house_ids = array_filter($house_ids);

        $info = PropertyModel::select([
            "home_information.*"
           // , "local_real_estate.*"
            , "school_neighborhood.*"
            , "trustee.*"
            , "home_information.house_id"## Needed this field, Important this line
                                      ])
                             // ->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id')
                             ->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id')
                             ->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id')
                              // ->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id')
                             ->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        $info->whereIn('home_information.house_id', $house_ids);
        $info->with([
            "property_descriptions",
            "local_real_estate_details",
            'sale_details',
            'sale_details.bidders', 'cma_arv_recommendations', 'mortgage_liens', 'owner_info', 'borrower_info']);

        $info = $info->take(25)->get();

        if (empty($info)) {
            return response()->json(['status' => 'success', 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success', 'data' => $info], 200);
    }
}
