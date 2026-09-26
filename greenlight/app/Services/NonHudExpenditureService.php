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

use App\Models\NonHudExpendituresModel;





class NonHudExpenditureService
{
    private $findAllByHouseId;

    /**
     * NonHudExpenditureService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("NonHudExpenditureService: __construct called");
        $this->request = $request;

    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createNonHudExpenditures($info){
        Log::info("NonHudExpenditureService: createNonHudExpenditures called");

        return NonHudExpendituresModel::create($info);
    }


    /**
     * @param $id
     * @param $info
     * @return mixed
     */

    public function updateNonHudExpenditures($id, $updateData){
        Log::info("NonHudExpenditureService: updateNonHudExpenditures called");

        return NonHudExpendituresModel::updateOrCreate(["id"=>$id],$updateData);

    }

    /**
     * @param $house_id
     * @return info
     */

    public function getNonHudExpenditures($house_id, $is_cache = false){
        Log::info("NonHudExpenditureService: updateNonHudExpenditures called");
        if($is_cache == true)
        {
            return $this->updateNonHudExpenditures;
        }

        $this->updateNonHudExpenditures =  NonHudExpendituresModel::where('house_id',$house_id)->get();

        #$this->getClientDocument = ClientMasterClosingDocModel::where('house_id',$house_id)->get();
        return $this->updateNonHudExpenditures;

    }



}
