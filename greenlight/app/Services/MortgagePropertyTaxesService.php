<?php
/**
 * Created By Rativardhan Singh Sengar  1/21/19 11:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/18/19 3:23 AM
 */

namespace App\Services;

use App\Models\MortgageHoaModel;
use App\Models\MortgagePropertyTaxesModel;
use Log;

class MortgagePropertyTaxesService
{
    private $findOneById;
    private $findAllById;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct()
    {
        Log::info("MortgagePropertyTaxesService: __construct called");
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllInformation($house_id, $is_cache = false)
    {
        Log::info("MortgagePropertyTaxesService: findAll called");
        if ($is_cache == true)
        {
            return $this->findAllById;
        }

        $this->findAllById = MortgagePropertyTaxesModel
            ::with(
                [
                    'mortgage_property_taxes_owed',
                    'mortgage_property_taxes_document',
                    'mortgage_property_taxes_document.user'

                ]
            )->where('house_id', $house_id)->get();

        return $this->findAllById;
    }

    /**
     * Find property record
     * @param $house_id
     * @return mixed
     */
    public function findOneById($house_id, $is_cache = false)
    {
        Log::info("MortgagePropertyTaxesService: findOneById called");
        if ($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById = MortgagePropertyTaxesModel::find($house_id);
        return $this->findOneById;
    }

    public function updateOrCreate($house_id, $info)
    {
        Log::info("MortgagePropertyTaxesService: updateOrCreate called");

        # we don't want to update houseID through input
        unset($info['house_id']);
        $info['house_id'] = $house_id;
        return MortgagePropertyTaxesModel::updateOrCreate(["house_id" => $house_id], $info);
    }

}
