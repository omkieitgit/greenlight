<?php
/**
 * Created By Rativardhan Singh Sengar  6/2/19 12:28 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/1/19 7:17 PM
 */

namespace App\Services;

use App\Models\HousePaymentLogModel;
use Log;

class HousePaymentLogService {
    private $findOneById;
    private $userService;

    /**
     * PropertyQueueListService constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService) {
        Log::info("HousePaymentLogService: __construct called");
        $this->userService = $userService;
    }


    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id) {
        Log::info("HousePaymentLogService: findOneById called");
        $this->findOneById = HousePaymentLogModel::find($id);
        return $this->findOneById;
    }


    public function getByPaymentType($house_id, $payment_type) {

        if(empty($house_id) || empty($payment_type))
            return false;

        Log::info("HousePaymentLogService: findOneById called");
        return HousePaymentLogModel::where(['house_id'     => $house_id,
                                                          'payment_type' => $payment_type,])->count();

    }


    public function create($info) {
        Log::info("WholesaleNotesService: create called");
        $info['user_id'] = $this->userService->user_id();
        return HousePaymentLogModel::create($info);
    }

}
