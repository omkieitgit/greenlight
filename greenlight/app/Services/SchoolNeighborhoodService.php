<?php
/**
 * Created By Rativardhan Singh Sengar  10/31/18 8:11 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/30/18 11:54 PM
 */

namespace App\Services;
use App\Models\SchoolNeighborhoodModel;
use Log;

class SchoolNeighborhoodService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("SchoolNeighborhoodService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("SchoolNeighborhoodService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  SchoolNeighborhoodModel::find($id);
        return $this->findOneById;
    }

    public function updateOrCreate($id, $info){
        Log::info("SchoolNeighborhoodService: updateOrCreate called");

        # we don't want to update houseID through input
        unset($info['house_id']);
        $info['house_id'] = $id;
        return SchoolNeighborhoodModel::updateOrCreate(["house_id"=>$id],$info);
    }

}
