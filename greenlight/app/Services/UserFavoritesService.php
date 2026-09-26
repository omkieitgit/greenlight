<?php
/**
 * Created By Rativardhan Singh Sengar  4/14/19 8:49 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/26/19 1:09 AM
 */

namespace App\Services;
use App\Models\UserFavoritesModel;
use Log;

class UserFavoritesService
{
    private $findOneById;
    private $userService;

    /**
     * UserFavoritesService constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService) {
        Log::info("UserFavoritesService: __construct called");
        $this->userService = $userService;

    }


    /**
     * Find property record
     * @param $house_byit_id
     * @return mixed
     */
    public function findOneById($id)
    {
        Log::info("UserFavoritesService: findOneById called");

        $this->findOneById =  UserFavoritesModel::find($id);
        return $this->findOneById;
    }
    public function isFavourite($house_id)
    {
        Log::info("UserFavoritesService: isFavourite called");

        return UserFavoritesModel::where([
            'user_id'=>$this->userService->user_id(),
            'house_id'=>$house_id
                                                        ])->count();

    }
    public function create($infoData){
        Log::info("UserFavoritesService: create called");

        $infoData['user_id'] = $this->userService->user_id();
        return UserFavoritesModel::create($infoData);
    }

    public function updateOrCreate($where , $propertyData){
        Log::info("UserFavoritesService: updateOrCreate called");

        if(empty($where))
            return false;

        $propertyData['user_id'] = $this->userService->user_id();
        return UserFavoritesModel::updateOrCreate($where, $propertyData);
    }

}
