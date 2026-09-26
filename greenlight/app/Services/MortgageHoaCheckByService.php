<?php
/**
 * Created By Rativardhan Singh Sengar  7/23/19 12:57 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 7/23/19 12:23 AM
 */

namespace App\Services;

use App\Models\MortgageHoaCheckbyModel;
use Log;

class MortgageHoaCheckByService {
    private $userService;


    public function __construct(UserService $userService) {
        Log::info("MortgageHoaCheckByService: __construct called");
        $this->userService = $userService;
    }


    public function create($info) {
        Log::info("MortgageHoaCheckByService: create called");
        $info['user_id'] = $this->userService->user_id();

       return MortgageHoaCheckbyModel::create($info);

    }

}
