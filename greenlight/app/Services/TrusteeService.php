<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:49 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 9:34 PM
 */

namespace App\Services;
use App\Models\TrusteeModel;
use Log;

class TrusteeService
{
    private $findOneById;
    private $findAllById;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("TrusteeService: __construct called");
    }

    public function findAllInformation($house_id, $is_cache = false)
    {
        Log::info("TrusteeService: findOneById called");

        if($is_cache == true)
        {
            return $this->findAllById;
        }

        $this->findAllById =  TrusteeModel::with(
            [
            ]
        )->where('house_id',$house_id)->first();
        return $this->findAllById;
    }


    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("TrusteeService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  TrusteeModel::find($house_id);
        return $this->findOneById;
    }


    public function updateOrCreate($info){
        Log::info("TrusteeService: updateOrCreate called");

        return TrusteeModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
