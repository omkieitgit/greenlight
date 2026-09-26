<?php
/**
 * Created By Rativardhan Singh Sengar  4/14/19 8:49 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/26/19 1:09 AM
 */

namespace App\Services;
use App\Models\InviteHistoryModel;
use Log;

class InviteHistoryService
{
    private $findOneById;
    private $userService;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserService $userService) {
        Log::info("InviteHistoryService: __construct called");
        $this->userService = $userService;

    }


    /**
     * Find property record
     * @param $house_byit_id
     * @return mixed
     */
    public function findOneById($InviteHistory_id, $is_cache = false)
    {
        Log::info("InviteHistoryService: findOneById called");

        $this->findOneById =  InviteHistoryModel::find($InviteHistory_id);
        return $this->findOneById;
    }

    public function create($propertyData){
        Log::info("InviteHistoryService: create called");

        //$propertyData['InviteHistorye_from'] = $this->userService->user_id();
        return InviteHistoryModel::create($propertyData);
    }

}
