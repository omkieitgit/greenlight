<?php
/**
 * Created By Mranalinee Chouhan  11/28/18
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 11/28/18
 */
namespace App\Services;
use App\Models\VehicleSaleInfoModel;
use App\Models\VehicleInfoModel;
use App\Models\VehicleCmaArvInfoModel;
use App\Models\VehicleNosInfoModel;
use App\Models\VehicleSaleRespondentInfoModel;

use Log;

class VehicleInputService
{
    private $findAllByHouseId;
   // private $userService;
    /**
     * Create a new controller instance.findAllByHouseId
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("CmaArvService: __construct called");
        //$this->userService = $userService;
    }


    function createUpdateVehicleSale($id,$info){
        return VehicleSaleInfoModel::updateOrCreate(["id" => $id], $info);
    }

    function getVehicleSaleInfo(){
        return VehicleSaleInfoModel::with(['respondent'])->get();
    }

    function getVehicleSaleInfoById($id){
        return VehicleSaleInfoModel::with(['respondent'])->where('id',$id)->first();
    }

    function getVehicleNosInfoById($id){
        return VehicleNosInfoModel::with(['nos'])->where('id',$id)->first();
    }

    function getVehicleInfo(){
        return VehicleInfoModel::get();
    }

    function getVehicleCmaArv(){
        return VehicleCmaArvInfoModel::get();
    }

    function getVehicleNos(){
        return VehicleNosInfoModel::with(['nos'])->get();
    }

    function updateCreateVehicleInput($id,$info){
        return VehicleInfoModel::updateOrCreate(["id" => $id], $info);
    }


    function updateCreateVehicleCmaArv($id,$info){
        return VehicleCmaArvInfoModel::updateOrCreate(["id" => $id], $info);
    }

    function updateCreateVehicleNosInfo($id,$info){
        return VehicleNosInfoModel::updateOrCreate(["id" => $id], $info);
    }

    function updateCreateVehicleSaleRespondentInfo($id,$info){
        return VehicleSaleRespondentInfoModel::updateOrCreate(["id" => $id], $info);
    }

    function getVehicleInfoDetail($vehicleId){
        return VehicleSaleInfoModel::with(['respondent','vehicle_info','vehicle_nos_info','vehicle_nos_info.nos','vehicle_cmaArv_info'])->where('id',$vehicleId)->first();
    }
}
