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

class CompsController extends Controller {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $houseBuyItService;


    public function __construct(Request $request

    ) {
        Log::info("CompsController: __construct called");
        $this->request = $request;
    }

    # WHERE CustomerName LIKE 'a%'	Finds any values that start with "a"
    private function startLikeOrWhere($query, $field, $value) {
        if (empty($value)) return $query;
        return $query->OrWhere($field, 'LIKE', "$value%");
    }

    public function index() {

        Log::info("CompsController: index called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 20;
        if ($limit > 500) $limit = 500;

        $query            = $this->request->get('query')?$this->request->get('query'):'';

        if(strlen($query) < 1)
        {
            return response()->json([], 200);
        }

        $info = PropertyModel::select([
                                       "home_information.house_id"## Needed this field, Important this line
                                      ]);

        $info = $this->startLikeOrWhere($info, 'home_information.address', $query);
        $info = $this->startLikeOrWhere($info, 'city', $query);
        $info = $this->startLikeOrWhere($info, 'county', $query);
        //$info = $this->startLikeOrWhere($info, 'state', $query);
        //$info = $this->startLikeOrWhere($info, 'zip', $query);

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
            "home_information.house_id"## Needed this field, Important this line
        ]);

        //$info->orderBy('home_information.address',"ASC");
        $infos = $info->skip(intval(0))->take(intval($limit))->get();

        $infos = $infos->map(function ($info) {

            $temp = (Object) [];
            //echo $info->address;
             $temp->address = CommonHelper::addressFormat($info);
            $temp->house_id = $info->house_id;
            return $temp;
            //$info->address." "." ";
           });

        return response()->json($infos, 200);
    }

    public function compsDetails() {

        Log::info("CompsController: compDetail called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 20;
        if ($limit > 500) $limit = 500;

        $query= $this->request->get('query')?$this->request->get('query'):'';
        $house_id= $this->request->get('house_id')?$this->request->get('house_id'):'';

        if(strlen($query) < 1)
        {
            return response()->json([], 200);
        }

        $info = PropertyModel::select([
            "home_information.house_id"## Needed this field, Important this line
        ]);


        if($house_id){
            $info = $info->where('home_information.house_id', $house_id);
        }else{
          $info = $this->startLikeOrWhere($info, 'home_information.address', $query);
          $info = $this->startLikeOrWhere($info, 'city', $query);
          $info = $this->startLikeOrWhere($info, 'county', $query);
        }


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
            'home_information.total_living_sqft',
            'home_information.county_value',
            'property_type','specific_property_type',
            "home_information.house_id"## Needed this field, Important this line
        ]);

        $info->with([
                "last_sale_details" => function ($query) {
                    $query->select(['house_id', 'sale_id', 'trustee', 'sale_date', 'sale_time'
                        , 'case_number', 'priceint', 'opening_bid', 'sale_type', 'sale_status'
                        , 'nos_by','nos_date'
                    ]);
                },
                "local_real_estate_details"=> function ($query) {
                    $query->select(['house_id',
                        'house_id', 'zillow_url', 'zestimate'
                        , 'redfin_url', 'redfin_est'
                        , 'realtor_url', 'realtor_est'
                        , 'truila_url', 'truila_est'
                       // , 'har_url', 'har_est'
                       // , 'beenverified_url'
                    ]);
                },
                "front_picture"=> function ($query) {
                  $query->select(['house_id', 'org_name', 'store_name']);
                },

                "cma_arv_recommendations"=> function ($query) {
                  $query->select(['house_id','info_added_by', 'specific_demand', 'general_demand', 'days_on_market'
                                  ,'p1_value','p2_value','recommended_cma_arv','wholetail_value',
                                  'comp_url_1','comp_url_2','comp_url_3','comp_url_4'
                                 ]);
                },

                ]

        );

        //$info->orderBy('home_information.address',"ASC");
        $infos = $info->skip(intval(0))->take(intval($limit))->get();
        $result=$this->getCompUrlAddress($infos);
        //        $infos = $infos->map(function ($info) {
        //
        //            $temp = (Object) [];
        //            //echo $info->address;
        //            $temp->address = CommonHelper::addressFormat($info);
        //            $temp->house_id = $info->house_id;
        //            return $temp;
        //            //$info->address." "." ";
        //        });

        return response()->json($result, 200);
    }

    function getCompUrlAddress($infos){
      $infos->map(function ($info) {
          if($info->cma_arv_recommendations){
            foreach($info->cma_arv_recommendations as $cma_arv){
                //if($cma_arv->info_added_by=='first_dtc'){
                  if($cma_arv->comp_url_1){
                    $cma_arv->comp_url_1=$this->parseUrl($cma_arv->comp_url_1);
                  }
                  if($cma_arv->comp_url_2){
                    $cma_arv->comp_url_2=$this->parseUrl($cma_arv->comp_url_2);
                  }
                  if($cma_arv->comp_url_3){
                    $cma_arv->comp_url_3=$this->parseUrl($cma_arv->comp_url_3);
                  }
                  if($cma_arv->comp_url_4){
                    $cma_arv->comp_url_4=$this->parseUrl($cma_arv->comp_url_4);
                  }

              //  }
            }
          }
      });
      return $infos;
    }

    function parseUrl($compsUrl){
      $addressPosition=2;
      if (strpos($compsUrl, 'redfin') !== false) {
        $addressPosition=3;
      }elseif(strpos($compsUrl, 'trulia') !== false){
        $addressPosition=4;
      }
      $url=parse_url($compsUrl);
      //echo $compsUrl;
      //print_r(explode('/',$url['path']));die;
      return str_replace('-',' ',explode('/',$url['path'])[$addressPosition]);
    }

}
