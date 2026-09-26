<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:41 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/31/18 1:18 AM
 */

namespace App\Services;
use App\Models\OwnerModel;
use App\Models\OwnerSocailMediaModel;

use Log;

class OwnerService
{
    private $findOneById;
    private $findAllByHouseId;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("OwnerService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("OwnerService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  OwnerModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("OwnerService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  OwnerModel::with(["social_media_info"])->where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateOwnerInfo($id, $updateInfo){
        Log::info("OwnerService: updateOwnerInfo called");

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findOneById($id, false);
        return $info->update($updateInfo);
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createOwnerInfo($info){
        Log::info("OwnerService: createOwnerInfo called");

        return OwnerModel::create($info);
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateOwnerSocialMediaInfo($id, $updateInfo){
        Log::info("OwnerService: updateOwnerSocailMediaInfo called");
        # we don't want to update houseID through input
        return OwnerSocailMediaModel::updateOrCreate(["id" => $id], $updateInfo);
    }

    public function findOwnerSocialMedia($house_id,$ownerId){
        return OwnerSocailMediaModel::where("house_id",$house_id)->where("owner_id",$ownerId)->get();
    }
}
