<?php
/**
 * Created By Rativardhan Singh Sengar  2/12/19 8:37 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/12/19 8:36 PM
 */

namespace App\Services;


use App\Models\WholesaleBuyerNModel;
use Log;

class WholesaleBuyerNService
{
    private $findOneById;
    private $findAllByHouseId;
    private $userService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService) {
        Log::info("WholesaleBuyerNService: __construct called");
        $this->userService = $userService;
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("WholesaleBuyerNService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  WholesaleBuyerNModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("WholesaleBuyerNService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  WholesaleBuyerNModel::where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $updateInfo
     * @return mixed
     */
    public function update($id, $updateInfo){
        Log::info("WholesaleBuyerNService: update called");

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateInfo);

    }

    /**
     * @param $info
     * @return mixed
     */
    public function create($info){
        Log::info("WholesaleBuyerNService: create called");

        $info['added_by'] = $this->userService->user_id();
        return WholesaleBuyerNModel::create($info);
    }

    function isSTHBProperty($house_id,$user_id) {
        Log::info("WholesaleBuyerNService: isSthbProperty called");

        if (empty($house_id) || empty($user_id))
            return false;

        $is_exists = WholesaleBuyerNModel::where(
            [
                'house_id' => $house_id,
                'user_id'  => $user_id,
            ])->exists();

        if( $is_exists)
        {
            return true;
        }

        return false;

    }

}
