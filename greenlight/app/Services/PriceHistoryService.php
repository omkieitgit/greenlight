<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:41 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/31/18 1:18 AM
 */

namespace App\Services;
use App\Models\PriceHistoryModel;

use Log;

class PriceHistoryService
{
    private $findOneById;
    private $findAllByHouseId;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("PriceHistoryService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("PriceHistoryService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  PriceHistoryModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("PriceHistoryService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  PriceHistoryModel::where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updatePriceHistory($id, $updateInfo){
        Log::info("PriceHistoryService: updatePriceHistory called");

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateInfo);

    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createPriceHistory($info){
        Log::info("PriceHistoryService: createPriceHistory called");

        return PriceHistoryModel::create($info);
    }

    public function updateOrCreate($id, $info){
        Log::info("SchoolNeighborhoodService: updateOrCreate called");

        # we don't want to update houseID through input
        //unset($info['house_id']);
        //$info['house_id'] = $id;
        
        return PriceHistoryModel::updateOrCreate(["id"=>$id],$info);
    }
}
