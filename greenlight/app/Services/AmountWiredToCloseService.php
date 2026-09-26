<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 11:54 PM
 */

namespace App\Services;


use App\Models\AmountWiredToCloseModel;
use Log;

class AmountWiredToCloseService
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
        Log::info("AmountWiredToCloseService: __construct called");

        $this->userService = $userService;
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("AmountWiredToCloseService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  AmountWiredToCloseModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("AmountWiredToCloseService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  AmountWiredToCloseModel::where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $updateInfo
     * @return mixed
     */
    public function update($id, $updateInfo){
        Log::info("AmountWiredToCloseService: update called");

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findOneById($id, true);

        unset($updateInfo['added_by']);
        return $info->update($updateInfo);

    }

    /**
     * @param $info
     * @return mixed
     */
    public function create($info){
        Log::info("AmountWiredToCloseService: create called");
        $info['added_by'] = $this->userService->user_id();
        return AmountWiredToCloseModel::create($info);
    }
}
