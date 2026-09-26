<?php
/**
 * Created By Rativardhan Singh Sengar  1/14/19 12:04 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/25/18 6:52 PM
 */

namespace App\Services;

use App\Models\MortgageOtherLiensPropertyTaxesModel;
use App\Models\SaleDetailsModel;
use Log;

class MortgageOtherLiensPropertyTaxesService
{
    private $findOneById;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct()
    {
        Log::info("MortgageOtherLiensPropertyTaxesService: __construct called");
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllInformation($house_id, $is_cache = false)
    {
        Log::info("MortgageOtherLiensPropertyTaxesService: findAll called");
        if ($is_cache == true)
        {
            return $this->findAllById;
        }

        $this->findAllById = MortgageOtherLiensPropertyTaxesModel
            ::with(
                [
                    //'mortgage_liens'
                    //, 'document_sale.user'
                    //, 'bidders'
                ]
            )->where('house_id', $house_id)->get();

        return $this->findAllById;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("MortgageOtherLiensPropertyTaxesService: findOneById called");
        if ($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById = MortgageOtherLiensPropertyTaxesModel::find($house_id);
        return $this->findOneById;
    }

    public function updateOrCreate($house_id, $info)
    {
        Log::info("LocalRealEstateService: updateOrCreate called");

        # we don't want to update houseID through input
        unset($info['house_id']);
        $info['house_id'] = $house_id;
        return MortgageOtherLiensPropertyTaxesModel::updateOrCreate(["house_id" => $house_id], $info);
    }

}
