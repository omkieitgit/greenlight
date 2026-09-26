<?php
/**
 * Created By Mranalinee Chouhan 
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;


use App\Models\ClientRenovationBudgetModel;


class ClientRenovationBudgetService
{
    private $findAllByHouseId;

    /**
     * ClientRenovationBudgetService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("ClientRenovationBudgetService: __construct called");
        $this->request = $request;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createClientRenovationBudget($info){
        Log::info("ClientRenovationBudgetService: createClientRenovationBudget");

        return ClientRenovationBudgetModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }



    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getClientRenovationBudget($house_id, $is_cache = false)
    {
        Log::info("ClientRenovationBudgetService: getClientRenovationBudget called");
        if ($is_cache == true)
        {
            return $this->getClientRenovationBudget;
        }
        
        $this->getClientRenovationBudget = ClientRenovationBudgetModel::where('house_id',$house_id)->first();
        return $this->getClientRenovationBudget;
    }




}
