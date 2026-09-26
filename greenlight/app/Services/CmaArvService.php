<?php
/**
 * Created By Mranalinee Chouhan  11/28/18
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 11/28/18
 */

namespace App\Services;
use App\Models\CmaArvModel;
use App\Models\SubToPropertyModel;
use App\Models\DocumentSubtoModel;
use App\Models\PropertyModel;

use Log;

class CmaArvService
{
    private $findAllByHouseId;
    private $userService;
    /**
     * Create a new controller instance.findAllByHouseId
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserService $userService) {
        Log::info("CmaArvService: __construct called");
        $this->userService = $userService;
    }


    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */

    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("CmaArvService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  CmaArvModel::where('house_id',$house_id)
                                              ->with(['user'=> function($query) {
                                                  $query->select(['id','first_name','last_name','username']);
                                              },
                                              "compSection"])
                                              ->get();
        return $this->findAllByHouseId;
    }


    public function getCmaArvByWhere($where = array())
    {
        if(empty($where))
        {
            return false;
        }
        return CmaArvModel::where($where)->first();
    }

    public function create($info){
        Log::info("CmaArvService: create called");
        return CmaArvModel::create($info);
    }


    public function updateReference($info, $updateData){
        Log::info("CmaArvService: update called");
        return $info->update($updateData);
    }

    function getCmaArvblockedUser(){
        return CmaArvModel::select(["cma_arv_recommendations.id","users.id as user_id","users.status"])
        ->leftJoin('users','users.id','=','cma_arv_recommendations.user_id')
        ->where('users.status','blocked')
        ->whereNotNull("cma_arv_recommendations.user_id")
        ->get();
    }

    function updateUserAndDate($id){
        return CmaArvModel::where('id',$id)->update(['user_id'=>NULL,'date'=>NULL]);
    }

    function createUpdateSubTo($house_id,$info){
        return SubToPropertyModel::updateOrCreate(["house_id" => $house_id], $info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function subtoDocucment($info)
    {
        Log::info("DocumentService: subtoDocucment called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentSubtoModel::create($info);
    }

    function getSubToPropertyInfo($house_id,$state){
        return SubToPropertyModel::
                select(['subto_property.*','home_information.state'])
               ->rightJoin('home_information','home_information.house_id','=','subto_property.house_id')
            //    ->with(['subto_document'=>function($query) use($state){
            //         $query->where('state',$state)->where('is_deleted',0);
            //    }])
               ->where('home_information.house_id',$house_id)->first();
    }

    function getSubToPropertyDocument($house_id,$state){
        return DocumentSubtoModel::where('state',$state)->where('is_deleted',0)->where('house_id',$house_id)->get();
    }
}
