<?php
/**
 * Created By Rativardhan Singh Sengar  2/4/19 12:43 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/2/18 1:45 AM
 */

namespace App\Services;
use App\Models\LocalRealEstateModel;
use App\Models\WholesaleBuyerStrategyModel;
use Log;

class WholesaleBuyerStrategyService
{
    private $findOneById;
    private $findAllById;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("WholesaleBuyerStrategyService: __construct called");
    }

    public function findAllInformation($house_id, $is_cache = false)
    {
        Log::info("WholesaleBuyerStrategyService: findOneById called");

        if($is_cache == true)
        {
            return $this->findAllById;
        }

        $this->findAllById =  WholesaleBuyerStrategyModel::with(
            [
//                'extra',
//                'emails_am',
//                'emails_company_team_member',
//                'emails_funder_lender',
//                'emails_time_left_notice',
//                'sthb',
//               // 'sthb_total',
            ]
        )->where('house_id',$house_id)->first();
        return $this->findAllById;
    }


    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("WholesaleBuyerStrategyService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  WholesaleBuyerStrategyModel::find($house_id);
        return $this->findOneById;
    }


    public function updateOrCreate($info){
        Log::info("WholesaleBuyerStrategyService: updateOrCreate called");

        return WholesaleBuyerStrategyModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
