<?php
/**
 * Created By Rativardhan Singh Sengar  6/2/19 12:46 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/2/19 12:39 AM
 */

namespace App\Services;

use App\Models\GeoModel;
use App\Models\HousePaymentLogModel;
use Log;

class GeoService {
    private $findOneById;

    /**
     * PropertyQueueListService constructor.
     */
    public function __construct(UserService $userService) {
        Log::info("GeoService: __construct called");
    }


    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id) {
        Log::info("GeoService: findOneById called");
        $this->findOneById = GeoModel::find($id);
        return $this->findOneById;
    }



    public function create($info) {
        Log::info("GeoService: create called");
        return GeoModel::create($info);
    }

    public function updateOrCreate($id, $info){
        Log::info("GeoService: updateOrCreate called");
        return GeoModel::updateOrCreate(["house_id"=>$id],$info);
    }
}
