<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:50 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/4/19 1:10 AM
 */

namespace App\Services;
use App\Models\TrusteeExtraModel;
use Log;

class TrusteeExtraService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("TrusteeExtraService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("TrusteeExtraService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  TrusteeExtraModel::find($house_id);
        return $this->findOneById;
    }

    public function updateOrCreate($info){
        Log::info("TrusteeExtraService: updateOrCreate called");
        return TrusteeExtraModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
