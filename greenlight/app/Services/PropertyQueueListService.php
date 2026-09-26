<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/14/19 7:28 PM
 */

namespace App\Services;

use App\Models\PropertyQueueListModel;
use Log;

class PropertyQueueListService
{
    private $findOneById;
    private $userService;

    /**
     * PropertyQueueListService constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        Log::info("PropertyQueueListService: __construct called");
        $this->userService = $userService;
    }


    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id)
    {
        Log::info("PropertyQueueListService: findOneById called");
        $this->findOneById = PropertyQueueListModel::find($id);
        return $this->findOneById;
    }

    /**
     * @return mixed
     */
    public function myList()
    {
        Log::info("PropertyQueueListService: myList called");
        return PropertyQueueListModel::where('user_id',  $this->userService->user_id())->get();
    }

    public function create($info)
    {
        Log::info("PropertyQueueListService: create called");

        $info['user_id'] = $this->userService->user_id();
        return PropertyQueueListModel::create($info);
    }

    public function update($list_id, $info)
    {
        Log::info("PropertyQueueListService: update called");

        return PropertyQueueListModel::where('list_id',$list_id)->update($info);
    }

    public function updateOrInsert($where, $updateData)
    {
        Log::info("PropertyQueueListService: updateOrInsert called");

        PropertyQueueListModel::updateOrInsert(
            $where,
            $updateData
        );

    }




}
