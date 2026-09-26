<?php
/**
 * Created By Rativardhan Singh Sengar  7/23/19 12:09 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/3/19 10:17 PM
 */

namespace App\Services;

use App\Models\MortgageLiensCheckbyModel;
use Log;

class MortgageLiensCheckByService {
    private $userService;


    public function __construct(UserService $userService) {
        Log::info("MortgageLiensCheckByService: __construct called");
        $this->userService = $userService;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($info, $is_cache = false)
    {
        Log::info("MortgageLiensCheckByService: findOneById called");
       
        $findOneById =  MortgageLiensCheckbyModel::where($info)->first();
        return $findOneById;
    }

    public function create($info) {
        Log::info("MortgageLiensCheckByService: create called");
        $checkBy=$this->findOneById($info);
        $mortgageCheck='';
        if(!$checkBy){
            $info['user_id'] = $this->userService->user_id();
            $mortgageCheck= MortgageLiensCheckbyModel::create($info);
        }
        return $mortgageCheck;
    }

}
