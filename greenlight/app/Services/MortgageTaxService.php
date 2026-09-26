<?php
/**
 * Created By Rativardhan Singh Sengar  1/18/19 1:11 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/17/19 1:11 AM
 */

namespace App\Services;

use App\Models\MortgageTaxModel;
use Log;
use App\Models\CompSectionHistoryModel;

class MortgageTaxService
{
    private $findOneById;
    private $findAllById;
    private $mortgageTaxService;
    private $userService;

    public function __construct(UserService $userService )
    {
        Log::info("MortgageHoaService: __construct called");
        $this->userService = $userService;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllInformation($house_id, $is_cache = false)
    {
        Log::info("MortgageHoaService: findAll called");
        if ($is_cache == true)
        {
            return $this->findAllById;
        }

        $this->findAllById = MortgageTaxModel
            ::with(
                [
                    'mortgage_tax_document',
                    'mortgage_tax_document.user',
                     "compSection"
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
        Log::info("MortgageTaxService: findOneById called");

        $this->findOneById = MortgageTaxModel::find($house_id);
        return $this->findOneById;
    }

    public function updateOrCreate($house_id, $info)
    {
        Log::info("MortgageHoaService: updateOrCreate called");
        $findInfo = $this->findOneById($house_id);
          # we don't want to update houseID through input
        unset($info['house_id']);
        $info['house_id'] = $house_id;
        $this->storeMortgageCheck($house_id,$info);
        return MortgageTaxModel::updateOrCreate(["house_id" => $house_id], $info);
    }

    public function storeMortgageCheck($house_id,$info){
        $findInfo = $this->findOneById($house_id);
        if(empty($findInfo))
        {

            if(isset($info['dtc_first_check']) && $info['dtc_first_check'] == true){
                $info['check_type']=1;
                $info['section_name']="dtc_first_check";
                $this->storeMortgageCheckHistorySection($info);
            }

            if(isset($info['dca_second_check']) && $info['dca_second_check'] == true){
                $info['check_type']=2;
                $info['section_name']="dca_second_check";
                $this->storeMortgageCheckHistorySection($info);
            }

            if(isset($info['dca_final_check']) && $info['dca_final_check'] == true){
                $info['check_type']=3;
                $info['section_name']="dca_final_check";
                $this->storeMortgageCheckHistorySection($info);
            }
        }

        else if(isset($info['dtc_first_check']) && $info['dtc_first_check'] == true && $findInfo['dtc_first_check'] != $info['dtc_first_check'] )
        {
           
            $info['check_type']=1;
            $info['section_name']="dtc_first_check";
            $this->storeMortgageCheckHistorySection($info);
        }
        else if(isset($info['dca_second_check']) && $info['dca_second_check'] == true && $findInfo['dca_second_check'] != $info['dca_second_check'] )
        {
            $info['check_type']=2;
            $info['section_name']="dca_second_check";
            $this->storeMortgageCheckHistorySection($info);
        }
        else if(isset($info['dca_final_check']) && $info['dca_final_check'] == true && $findInfo['dca_final_check'] != $info['dca_final_check'] )
        {
           
            $info['check_type']=3;
            $info['section_name']="dca_final_check";
            $this->storeMortgageCheckHistorySection($info);
        }

        
    }

    public function storeMortgageCheckHistorySection($info) {

        CompSectionHistoryModel::create([
            'user_id' =>$this->userService->user_id(),
            "house_id" => $info['house_id'],
            'section_type' => "tax",
            'section_name' => $info['section_name'],
            'section_value'=>$info['check_type'],
            'date_by' => date('Y-m-d'),
            "foreign_id" => $info['house_id'],
        ]);

    }

}
