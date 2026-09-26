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

use App\Models\PayoutModel;
use App\Models\PayoutDetailModel;
use App\Models\PayoutCategoryModel;
use App\Models\AdditionalFieldModel;
use App\Models\PayoutLenderModel;
use App\Models\ClientRenovationDetailModel;
use App\Models\HomebuyerRenoCategoryModel;

use DB;

class PayoutService {

    private $findAllByHouseId;

    /**
     * PayoutService constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        Log::info("PayoutService: __construct called");
        $this->request = $request;
    }

    /**
     * @param $info
     * @return mixed
     */
    public function createPayoutDetail($info) {
        Log::info("PayoutService: Client called");
        return PayoutDetailModel::create($info);
    }


    /**
     * @param $id
     * @param $info
     * @return mixed
     */

    public function updatePayoutDetail($updateData, $payoutId) {
        Log::info("ClientService: updateProperty called");
        $payoutdetail=PayoutDetailModel::updateOrCreate(["id" => $payoutId], $updateData);
        return PayoutDetailModel::with(['category'])->where('id',$payoutdetail->id)->first();
    }

    public function updateCreatePayoutDetail($data) {
        Log::info("ClientService: updateProperty called");
        return PayoutDetailModel::updateOrCreate(["category_id" => $data['category_id'],"house_id" => $data['house_id'],"payout_type" => $data['payout_type']], $data);
    }

    public function homebuyerRenoCatUpdateOrCreate($data){
        Log::info("ClientService: clientRenovationDetailUpdateOrCreate called");
        return HomebuyerRenoCategoryModel::updateOrCreate(["category_id"=>$data['category_id'],"house_id" => $data['house_id']],$data);
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findPayoutDetail($house_id, $is_cache = false) {
        Log::info("PayoutService: findPayout called");
        if ($is_cache == true) {
            return $this->findPayout;
        }
        $payout = PayoutDetailModel::where('house_id', $house_id)->get();
        
        $this->findPayout = $payout;
        return $this->findPayout;
    }


    function removePayoutDetail($id) {
        $payoutTitle = PayoutDetailModel::where('id', $id);
        if ($payoutTitle == true) {
            $payoutTitle->delete();
        }
    }


    function updateOrCreate($house_id,$updateData){
        return PayoutModel::updateOrCreate(["house_id" => $house_id], $updateData);
    }

    public function getPayout($house_id, $is_cache = false) {
        Log::info("PayoutService: findPayout called");
        if ($is_cache == true) {
            return $this->findAllByHouseId;
        }
        $payout = PayoutModel::with([
                    'payoutDetail',
                    'payoutDetail.category',
                    'additionField',
                    "total_air_bnb",
                    "distribution_llc"])
                  ->where('house_id', $house_id)->first();
        if(empty($payout)){
            $payout['payout_detail'] =  PayoutDetailModel::with(['category'])->where('house_id',$house_id)->get();
        }

        $this->findAllByHouseId = $payout;
        return $this->findAllByHouseId;
    }

    function createCategory($category){
       return PayoutCategoryModel::create($category); 
    }

    function updatePayoutCategory($id,$category){
        PayoutCategoryModel::where('id',$id)->update($category); 
        return PayoutCategoryModel::find($id); 
     }

    function getCategory(){
        return PayoutCategoryModel::get(); 
    }

    function createUpdateAdditionalField($id,$data){
        return AdditionalFieldModel::updateOrCreate(["id" => $id], $data);
    }

    function getPayoutInfo($house_id){
        return PayoutModel::select([DB::raw("DATE_FORMAT(close_date_a_to_b, '%m/%d/%Y') as close_date_a_to_b") ,DB::raw("DATE_FORMAT(close_date_b_to_c, '%m/%d/%Y') as close_date_b_to_c")])->where('house_id',$house_id)->first();
    }

    function createUpdatePayoutLender($id,$data){
        return PayoutLenderModel::updateOrCreate(["house_id" => $id], $data);
    }
}
