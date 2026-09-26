<?php
/**
 * Created By Rativardhan Singh Sengar  12/21/18 1:14 AM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 12/21/18 1:13 AM
 */

namespace App\Services;
use App\Models\SaleDetailsDescriptionsModel;
use App\Models\SaleTrusteeNotesModel;

use Log;

class SaleDetailsDescriptionsService
{
    private $findOneById;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("SaleDetailsDescriptionsService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("SaleDetailsDescriptionsService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  SaleDetailsDescriptionsModel::find($id);
        return $this->findOneById;
    }

    public function updateOrCreate($id, $propertyData){
        Log::info("SaleDetailsDescriptionsService: updateOrCreate called");

        # we don't want to update houseID through input
        unset($propertyData['sale_id']);
        $propertyData['sale_id'] = $id;

        return SaleDetailsDescriptionsModel::updateOrCreate(["sale_id"=>$id],$propertyData);
    }

    public function updateOrCreateSaleNotes($id, $propertyData){
        $propertyData['sale_id']=$id;
        Log::info("SaleDetailsDescriptionsService: updateOrCreateSaleNotes called");

        if(!empty($propertyData['before_sale_trustee_notes'])){
            return SaleTrusteeNotesModel::updateOrCreate(
                ['sale_id'=>$id,
                'before_sale_trustee_notes'=>$propertyData['before_sale_trustee_notes']
                ],$propertyData);
        }
        if(!empty($propertyData['after_sale_trustee_notes'])){
            return SaleTrusteeNotesModel::updateOrCreate(
                ['sale_id'=>$id,
                'after_sale_trustee_notes'=>$propertyData['after_sale_trustee_notes']
            ],$propertyData);
        }
    }


}
