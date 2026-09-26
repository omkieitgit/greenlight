<?php
/**
 * Created By Rativardhan Singh Sengar  3/7/19 10:29 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/6/19 8:29 PM
 */

namespace App\Services;
use App\Models\PropertyAcquisitionBtoCFirstModel;


use Log;

class PropertyAcquisitionBtoCFirstService
{

    private $findOneById;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("'PropertyAcquisitionBtoCFirstService: __construct called");
    }


    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("'PropertyAcquisitionBtoCFirstService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  PropertyAcquisitionBtoCFirstModel::find($house_id);
        return $this->findOneById;
    }

    /**
     * @param $info
     * @return mixed
     */
    public function updateOrCreate($info){
        Log::info("'PropertyAcquisitionBtoCFirstService: updateOrCreate called");
        return PropertyAcquisitionBtoCFirstModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
