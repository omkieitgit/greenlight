<?php
/**
 * Created By Rativardhan Singh  11/28/18
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 11/28/18
 */

namespace App\Services;
use App\Models\PropertyAcquisitionAtoBFirstModel;


use Log;

class PropertyAcquisitionAtoBFirstService
{

    private $findOneById;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("'PropertyAcquisitionAtoBFirstService: __construct called");
    }


    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("'PropertyAcquisitionAtoBFirstService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  PropertyAcquisitionAtoBFirstModel::find($house_id);
        return $this->findOneById;
    }

    /**
     * @param $info
     * @return mixed
     */
    public function updateOrCreate($info){
        Log::info("'PropertyAcquisitionAtoBFirstService: updateOrCreate called");
        return PropertyAcquisitionAtoBFirstModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
