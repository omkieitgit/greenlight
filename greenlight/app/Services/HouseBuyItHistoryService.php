<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:25 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/25/19 11:25 PM
 */

namespace App\Services;
use App\Models\HouseBuyItHistoryModel;
use Log;

class HouseBuyItHistoryService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("HouseBuyItHistoryService: __construct called");
    }


    /**
     * Find property record
     * @param $house_byit_id
     * @return mixed
     */
    public function findOneById($house_byit_id, $is_cache = false)
    {
        Log::info("HouseBuyItHistoryService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  HouseBuyItHistoryModel::find($house_byit_id);
        return $this->findOneById;
    }

    public function create($propertyData){
        Log::info("HouseBuyItHistoryService: create called");
        return HouseBuyItHistoryModel::create($propertyData);
    }

    public function update($id, $updateData){
        Log::info("HouseBuyItHistoryService: update called");

        unset($updateData['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateData);

    }

}
