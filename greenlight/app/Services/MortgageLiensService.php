<?php
/**
 * Created By Rativardhan Singh Sengar  1/14/19 7:26 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/12/18 1:47 AM
 */

namespace App\Services;

use App\Models\MortgageLiensModel;
use App\Models\CompSectionHistoryModel;
use Log;

class MortgageLiensService
{
    private $findOneById;
    private $mortgageLiensCheckByService;
    private $userService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MortgageLiensCheckByService $mortgageLiensCheckByService,UserService $userService) {
        Log::info("MortgageLiensService: __construct called");
        $this->mortgageLiensCheckByService = $mortgageLiensCheckByService;
        $this->userService = $userService;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findAllLiensByHouseId($house_id)
    {
        Log::info("MortgageLiensService: findAllLiensByHouseId called");

        return MortgageLiensModel::where(["house_id"=>$house_id])->with(
            [
                'mortgage_liens_document',
                'mortgage_liens_document.user',
                "check_by" => function ($query) {
                    $query->select(["id",
                                    "mortgage_id",
                                    "user_id",
                                    "house_id",
                                    "check_type",
                                    "created_at",
                                    "updated_at",
                                   ])->orderBy("id",'desc');
                },
                'check_by.user' => function ($query) {
                    $query->select(["users.id",
                                    "users.email",
                                    "users.first_name",
                                    "users.last_name"]);
                },
                "compSection"
            ]
        )->get();

    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("MortgageLiensService: findOneById called");
        if($is_cache == true && !empty($this->findOneById))
        {
            return $this->findOneById;
        }

        $this->findOneById =  MortgageLiensModel::find($id);
        return $this->findOneById;
    }


    function getRoleAccess($role=""){
        $userId =$this->userService->user_id();
        $itemCollection=$this->userService->userDetailsWithRoles($userId);
        $userRole  =false;
        foreach($itemCollection->user_roles as $roles){
            if($roles->role_key == $role){
                $userRole = true;
            }
        }
        return $userRole;
    }
    /**
     * @param $id
     * @param $updateInfo
     * @return mixed
     */
    public function update($id, $updateInfo){
        Log::info("MortgageLiensService: updateLiens called");

        $info = $this->findOneById($id, true);

        
        // if firstCheck
        if(isset($updateInfo['dtc_first_check']) && $updateInfo['dtc_first_check'] == true && $info['dtc_first_check'] != $updateInfo['dtc_first_check']){
            
            if($this->getRoleAccess('first_dtc')==false){
                return ['status' => 'failed', 'message' => __("messages.not_authorize_section")." DTC - Second Check"];
            }
            $insert_data = [
                "house_id" => $updateInfo['house_id'],
                "check_type" => "1",
                "mortgage_id" => $id,
            ];
            $this->mortgageLiensCheckByService->create($insert_data);
            $updateInfo['check_type']=1;
            $updateInfo['section_name']="dtc_first_check";
            $this->storeMortgageCheckHistorySection($updateInfo);
        }

        if(isset($updateInfo['dca_second_check']) && $updateInfo['dca_second_check'] == true && $info['dca_second_check'] != $updateInfo['dca_second_check']){
            
            if($this->getRoleAccess('second_dca')==false){
                return ['status' => 'failed', 'message' => __("messages.not_authorize_section")." DCA - Second Check"];
            } 

            $insert_data = [
                "house_id" => $updateInfo['house_id'],
                "check_type" => "2",
                "mortgage_id" => $id,
            ];
            $this->mortgageLiensCheckByService->create($insert_data);
            $updateInfo['check_type']=2;
            $updateInfo['section_name']="dca_second_check";
            $this->storeMortgageCheckHistorySection($updateInfo);
        }

        if(isset($updateInfo['dca_final_check']) && $updateInfo['dca_final_check'] == true && $info['dca_final_check'] != $updateInfo['dca_final_check']){
           
            if($this->getRoleAccess('third_dca')==false && $this->getRoleAccess('chief_dca')==false){
                return ['status' => 'failed', 'message' => __("messages.not_authorize_section")." DCA - Final Check"];
            }
            $insert_data = [
                "house_id" => $updateInfo['house_id'],
                "check_type" => "3",
                "mortgage_id" => $id,
            ];
            $this->mortgageLiensCheckByService->create($insert_data);
            $updateInfo['check_type']=3;
            $updateInfo['section_name']="dca_final_check";
            $this->storeMortgageCheckHistorySection($updateInfo);
        }


        # we don't want to update houseID through input
        unset($updateInfo['house_id']);
        unset($updateInfo['lien_type']);
        $temp =  $info->update($updateInfo);
        return $temp;

    }

    /**
     * @param $info
     * @return mixed
     */
    public function create($info){
        Log::info("MortgageLiensService: createLiens called");

        $temp =  MortgageLiensModel::create($info);
        
        // if firstCheck
        if(isset($info['dtc_first_check']) && $info['dtc_first_check'] == true){
            if($this->getRoleAccess('first_dtc')==false){
                return ['status' => 'failed', 'message' => __("messages.not_authorize_section")." DTC - Second Check"];
            }
            $insert_data = [
                "house_id" => $info['house_id'],
                "check_type" => "1",
                "mortgage_id" => $temp->mortgage_id,
            ];
            $this->mortgageLiensCheckByService->create($insert_data);
            $info['check_type']=1;
            $info['section_name']="dtc_first_check";
            $this->storeMortgageCheckHistorySection($info);
        }

        if(isset($info['dca_second_check']) && $info['dca_second_check'] == true){
            if($this->getRoleAccess('second_dca')==false){
                return ['status' => 'failed', 'message' => __("messages.not_authorize_section")." DCA - Second Check"];
            } 

            $insert_data = [
                "house_id" => $info['house_id'],
                "check_type" => "2",
                "mortgage_id" => $temp->mortgage_id,
            ];
            $this->mortgageLiensCheckByService->create($insert_data);
            $info['check_type']=2;
            $info['section_name']="dca_second_check";
            $this->storeMortgageCheckHistorySection($info);
        }

        if(isset($info['dca_final_check']) && $info['dca_final_check'] == true){

            if($this->getRoleAccess('third_dca')==false && $this->getRoleAccess('chief_dca')==false){
                return ['status' => 'failed', 'message' => __("messages.not_authorize_section")." DCA - Final Check"];
            }

            $insert_data = [
                "house_id" => $info['house_id'],
                "check_type" => "3",
                "mortgage_id" => $temp->mortgage_id,
            ];
            $this->mortgageLiensCheckByService->create($insert_data);
            $info['check_type']=3;
            $info['section_name']="dca_final_check";
            $this->storeMortgageCheckHistorySection($info);
        }

        return $temp;
    }

    public function storeMortgageCheckHistorySection($info){
        CompSectionHistoryModel::create([
            'user_id' =>$this->userService->user_id(),
            "house_id" => $info['house_id'],
            'section_type' => $info['lien_type'],
            'section_name' => $info['section_name'],
            'section_value'=>$info['check_type'],
            'date_by' => date('Y-m-d'),
            "foreign_id" => $info['mortgage_id'],
        ]);
    }


    public function findByLienTypeHouseId($info)
    {
        Log::info("MortgageLiensCheckByService: findOneById called");
       
        $findOneById =  MortgageLiensModel::where($info)->first();
        return $findOneById;
    }
}
