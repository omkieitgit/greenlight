<?php
/**
 * Created By Rativardhan Singh Sengar  10/31/18 8:11 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/30/18 11:54 PM
 */

namespace App\Services;
use App\Models\WholesaleBuyerNTotalModel;
use Log;

class WholesaleBuyerNTotalService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("WholesaleBuyerNTotalService: __construct called");
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("WholesaleBuyerNTotalService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  WholesaleBuyerNTotalModel::find($id);
        return $this->findOneById;
    }

    public function updateOrCreate($id, $info){
        Log::info("WholesaleBuyerNTotalService: updateOrCreate called");

        # we don't want to update houseID through input
        unset($info['house_id']);
        $info['house_id'] = $id;
        return WholesaleBuyerNTotalModel::updateOrCreate(["house_id"=>$id],$info);
    }

}
