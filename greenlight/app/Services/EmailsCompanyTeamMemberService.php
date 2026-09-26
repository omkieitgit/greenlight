<?php
/**
 * Created By Rativardhan Singh Sengar  2/12/19 8:33 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/12/19 8:32 PM
 */

namespace App\Services;


use App\Models\EmailsCompanyTeamMemberModel;
use Log;

class EmailsCompanyTeamMemberService
{
    private $findOneById;
    private $findAllByHouseId;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Log::info("EmailsCompanyTeamMemberService: __construct called");
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("EmailsCompanyTeamMemberService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  EmailsCompanyTeamMemberModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("EmailsCompanyTeamMemberService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  EmailsCompanyTeamMemberModel::where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function update($id, $updateInfo){
        Log::info("EmailsCompanyTeamMemberService: update called");

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
        Log::info("EmailsCompanyTeamMemberService: create called");

        return EmailsCompanyTeamMemberModel::create($info);
    }
}
