<?php
/**
 * Created By Rativardhan Singh Sengar  10/31/18 8:11 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/30/18 11:54 PM
 */

namespace App\Services;
use App\Models\MapVideoModel;
use Log;

class MapVideoService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("MapVideoService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("MapVideoService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  MapVideoModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function update($id, $info){

        Log::info("MapVideoService: update called");

        # we don't want to update houseID through input
        unset($info['house_id']);
        return CmaArvModel::updateOrCreate(["house_id"=>$id],$info);
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function create($info){
        Log::info("CmaArvService: createCmaArvommendation called");

        return CmaArvModel::create($info);
    }

    public function updateOrCreate( $info){
        Log::info("MapVideoService: updateOrCreate called");

        # we don't want to update houseID through input
        # House id security handle in controller

        return MapVideoModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
