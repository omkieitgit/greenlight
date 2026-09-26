<?php
/**
 * Created By Rativardhan Singh Sengar  3/19/19 11:26 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/2/18 1:45 AM
 */

namespace App\Services;
use App\Models\HouseLatestInfoModel;
use Log;

class HouseLatestInfoService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("HouseLatestInfoService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("HouseLatestInfoService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  HouseLatestInfoModel::find($house_id);
        return $this->findOneById;
    }


    public function updateOrCreate($info){
        Log::info("HouseLatestInfoService: updateOrCreate called");

        return HouseLatestInfoModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
