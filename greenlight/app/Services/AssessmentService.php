<?php
/**
 * Created By Rativardhan Singh Sengar  10/30/18 11:53 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/29/18 9:32 PM
 */

namespace App\Services;
use App\Models\AssessmentModel;

use Log;

class AssessmentService
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
        Log::info("AssessmentService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("AssessmentService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  AssessmentModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("AssessmentService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  AssessmentModel::where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateAssessment($id, $updateInfo){
        Log::info("AssessmentService: updateOrCreate called");

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
    public function createAssessment($info){
        Log::info("AssessmentService: createAssessment called");
        return AssessmentModel::create($info);
    }

    public function update($info){
        Log::info("AssessmentService: update called");
        return AssessmentModel::where('id', $info['id'])->where('house_id', $info['house_id'])
            ->update($info);
    }

}
