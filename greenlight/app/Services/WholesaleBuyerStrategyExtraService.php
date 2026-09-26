<?php
/**
 * Created By Rativardhan Singh Sengar  2/4/19 12:45 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/4/19 12:44 AM
 */

namespace App\Services;
use App\Models\LocalRealEstateModel;
use App\Models\WholesaleBuyerStrategyExtraModel;
use App\Models\WholesaleBuyerStrategyModel;
use Log;

class WholesaleBuyerStrategyExtraService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("WholesaleBuyerStrategyServiceExtra: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("WholesaleBuyerStrategyServiceExtra: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  WholesaleBuyerStrategyExtraModel::find($house_id);
        return $this->findOneById;
    }

    public function updateOrCreate($info){
        Log::info("WholesaleBuyerStrategyServiceExtra: updateOrCreate called");
        return WholesaleBuyerStrategyExtraModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
