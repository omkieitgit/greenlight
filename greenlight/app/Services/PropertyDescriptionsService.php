<?php
/**
 * Created By Rativardhan Singh Sengar  10/29/18 9:00 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/29/18 8:55 PM
 */

namespace App\Services;
use App\Models\PropertyDescriptionsModel;
use Log;

class PropertyDescriptionsService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("PropertyDescriptionsService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("PropertyDescriptionsService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  PropertyDescriptionsModel::find($id);
        return $this->findOneById;
    }

    public function updateOrCreate($id, $propertyData){
        Log::info("PropertyDescriptionsService: updateOrCreate called");

        # we don't want to update houseID through input
        unset($propertyData['house_id']);
        $propertyData['house_id'] = $id;
        return PropertyDescriptionsModel::updateOrCreate(["house_id"=>$id],$propertyData);
    }

}
