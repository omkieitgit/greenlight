<?php
/**
 * Created By Rativardhan Singh Sengar  1/22/19 12:01 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/21/19 11:56 PM
 */

namespace App\Services;

use App\Models\MortgagePropertyTaxesOwedModel;
use Log;

class MortgagePropertyTaxesOwedService
{
    private $findOneById;
    private $findAllByHouseId;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct()
    {
        Log::info("MortgagePropertyTaxesOwedService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($tax_id, $is_cache = false)
    {
        Log::info("MortgagePropertyTaxesOwedService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  MortgagePropertyTaxesOwedModel::find($tax_id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("MortgagePropertyTaxesOwedService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  MortgagePropertyTaxesOwedModel::where('house_id',$house_id)->get();
        return $this->findAllByHouseId;
    }


    /**
     * @param $id
     * @param $updateInfo
     * @return mixed
     */
    public function update($id, $updateInfo){
        Log::info("MortgagePropertyTaxesOwedService: update called");

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateInfo);

    }

    /**
     * @param $info
     * @return mixed
     */
    public function create($info){
        Log::info("MortgagePropertyTaxesOwedService: create called");
        return MortgagePropertyTaxesOwedModel::create($info);
    }

}
