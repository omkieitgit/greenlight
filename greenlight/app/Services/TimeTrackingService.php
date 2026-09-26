<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:41 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/31/18 1:18 AM
 */

namespace App\Services;
use App\Models\AcTimeTrackingModel;


use Log;
use DB;

class TimeTrackingService
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
        Log::info("McdService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("McdService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  AcTimeTrackingModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("McdService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  AcTimeTrackingModel::where('house_id',$house_id)->with('user')->get();
        // foreach($this->findAllByHouseId as $trackingTime){
        //     $trackingTime->start_time=date('H:i:s',strtotime($trackingTime->start_time));
        //     $trackingTime->end_time=date('H:i:s',strtotime($trackingTime->end_time));
        //     $trackingTime->total_time=date('H:i:s',strtotime($trackingTime->total_time));
        // }
        return $this->findAllByHouseId;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getTimeTrackingSummery($house_id, $is_cache = false)
    {

        $summery =  AcTimeTrackingModel::SELECT(['user_id',DB::raw('sum(total_time) as total_time')])
                    ->with('user')->where('house_id',$house_id)->groupBy('user_id')->get();
        return $summery;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreate($id, $info){
        Log::info("McdService: updateCreate called");
        $info= AcTimeTrackingModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        unset($info['house_id']);
        $info = $this->findDetailById($info->id, false);
        return $info;
    }

    function findDetailById($id){
        $info =  AcTimeTrackingModel::where('id',$id)->with('user')->first();
        return $info;
    }
}
