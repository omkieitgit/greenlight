<?php
/**
 * Created By Rativardhan Singh Sengar  3/6/19 8:12 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/6/19 7:56 PM
 */

namespace App\Services;
use App\Models\HomeBuyersAlias2DarrenModel;


use Log;

class HomeBuyersAlias2DarrenService
{

    private $findOneById;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("'HomeBuyersAlias2DarrenService: __construct called");
    }


    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("'HomeBuyersAlias2DarrenService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  HomeBuyersAlias2DarrenModel::find($house_id);
        return $this->findOneById;
    }

    /**
     * @param $info
     * @return mixed
     */
    public function updateOrCreate($info){
        Log::info("'HomeBuyersAlias2DarrenService: updateOrCreate called");

        return HomeBuyersAlias2DarrenModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }

}
