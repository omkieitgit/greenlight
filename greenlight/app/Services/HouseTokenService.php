<?php
/**
 * Created By Rativardhan Singh Sengar  3/26/19 1:08 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/26/19 12:38 AM
 */

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\HouseTokenModel;
use App\Models\User;
use Log;
use Illuminate\Support\Str;
class HouseTokenService
{
    private $findOneById;
    private $userService;

    /**
     * HouseTokenService constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        Log::info("HouseTokenService: __construct called");
        $this->userService = $userService;
    }


    /**
     * Find property record
     * @param $house_id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("HouseTokenService: findOneById called");
        if ($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById = HouseTokenModel::find($house_id);
        return $this->findOneById;
    }

    public function firstOrCreate($propertyData)
    {
        Log::info("HouseTokenService: firstOrCreate called");
        return HouseTokenModel::firstOrCreate($propertyData);
    }

    public function firstOrNew($propertyData)
    {
        Log::info("HouseTokenService: firstOrNew called");
        return HouseTokenModel::firstOrNew($propertyData);
    }

    public function create($propertyData)
    {
        Log::info("HouseTokenService: create called");
        return HouseTokenModel::create($propertyData);
    }

    public function update($id, $updateData)
    {
        Log::info("HouseTokenService: update called");

        unset($updateData['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateData);

    }

    public function token($house_id)
    {
        $info  = $this->findOneById($house_id);
        $token = '';

        if (empty($info))
        {
            $insert_info             = [];
            $insert_info['house_id'] = $house_id;
            $insert_info['token']    = $token = Str::random(16);;
            $this->create($insert_info);
        } else if (empty($token))
        {
            $token = $info->token;
        }

        return $token;
    }

    public function isTokenExists($token)
    {
        if(empty($token))
            return false;

        $info = HouseTokenModel::where('token',$token)->get()->first();

        return $info;
    }

    public function address_url($house_id, $property_info)
    {
        $token = $this->token($house_id);
        $link                = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);
        return $link;
    }

    public function address_url_anchor($house_id, $property_info)
    {
        $token = $this->token($house_id);
        $link                = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);
        return CommonHelper::address_url_anchor($link);
    }

    public function detail_url($house_id, $property_info)
    {
        //$token = $this->token($house_id);
        $link  = env("APP_FRONTEND") . 'home/showdetail/' . $house_id . '/' . CommonHelper::url_slug($property_info);
        return $link;
    }


}
