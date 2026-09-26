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

class TopSearchController extends Controller {
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

        // ToDo: User role base code, home buyer can search only his invite properties

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
