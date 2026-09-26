<?php
/**
 * Created By Mranalinee Chouhan 
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Services;

use App\Models\W9Model;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;



class W9Service
{
    private $findAllByHouseId;

    /**
     * W9Service constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("W9Service: __construct called");
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createW9Info($info){
        Log::info("W9Service: W9 called");

        return W9Model::create($info);
    }


     /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */

    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("W9Service: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  W9Model::where('house_id',$house_id)->first();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateW9($user_id, $info){

        Log::info("CmaArvService: updateCmaArvommendation called");

        # we don't want to update houseID through input
        return W9Model::updateOrCreate(["user_id"=>$user_id],$info);
    }

    public function findAllByUserId($user_id, $is_cache = false)
    {
        Log::info("W9Service: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  W9Model::where('user_id',$user_id)->first();
        return $this->findAllByHouseId;
    }

  

}

