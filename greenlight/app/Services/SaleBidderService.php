<?php
/**
 * Created By Rativardhan Singh Sengar  12/25/18 6:46 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 12/25/18 6:46 PM
 */

namespace App\Services;
use App\Models\SaleBidderModel;
use Log;

class SaleBidderService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("SaleBidderService: __construct called");
    }


    public function findOneById($sale_id, $is_cache = false)
    {
        Log::info("SaleBidderService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  SaleBidderModel::find($sale_id);
        return $this->findOneById;
    }

    public function create($insertData){
        Log::info("SaleBidderService: create called");
        return SaleBidderModel::create($insertData);
    }

    public function update($bidder_id, $updateData){
        Log::info("SaleBidderService: update called");

        unset($updateData['sale_id']);
        unset($updateData['house_id']);
        unset($updateData['bidder_id']); # Laravel Don't update Primary key but still added this line

        $info = $this->findOneById($bidder_id, true);
        return $info->update($updateData);

    }

    
}
