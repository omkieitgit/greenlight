<?php
/**
 * Created By Rativardhan Singh Sengar  2/12/19 8:30 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/14/19 7:27 PM
 */

namespace App\Services;


use App\Models\EmailsAmModel;
use Log;

class EmailsAmService
{
    private $findOneById;
    private $findAllByHouseId;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("EmailsAmService: __construct called");
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("EmailsAmService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  EmailsAmModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("EmailsAmService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  EmailsAmModel::where('house_id',$house_id)->where('is_deleted','0')->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function update($id, $updateInfo){
        Log::info("EmailsAmService: update called");

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateInfo);

    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function create($info){
        Log::info("EmailsAmService: create called");

        return EmailsAmModel::create($info);
    }

    public function getBlockedAMUsers(){
        return EmailsAmModel::select(['emails_am_id','user_id','emails_am.email'])
        ->join('users', function($join)
        {
            $join->on('users.id', '=', 'emails_am.user_id');
            $join->orOn('users.email', '=', "emails_am.email");
        })
        ->where('users.status','blocked')
        ->where('emails_am.is_deleted','0')
        ->get();
    }

    public function updateAmEmailIsDeleted($id){
        return EmailsAmModel::where('emails_am_id',$id)->update(['is_deleted'=>1]);
    }
}
