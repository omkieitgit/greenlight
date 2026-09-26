<?php
/**
 * Created By Mranalinee Chouhan  18/0419 7:01 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/14/19 4:05 PM
 */

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;

use App\Services\ClientRenovationBudgetService;
use App\Http\Validations\ClientValidations;
use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Services\McdService;

class ClientRenovationBudgetController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    
    private $wholesaleBuyerNService;

    private $mcdService;


    public function __construct(Request $request
        , ClientRenovationBudgetService $clientRenovationBudgetService,
        McdService $mcdService

        
    )
    {
        Log::info("ClientRenovationBudgetController: __construct called");
        $this->request                                  = $request;
        $this->clientRenovationBudgetService            = $clientRenovationBudgetService;
        $this->mcdService                               = $mcdService;  

    }


    public function index($house_id)
    {
        Log::info("ClientRenovationBudgetController: index called");
        $client_renovation_budget = $this->clientRenovationBudgetService->getclientRenovationBudget($house_id);
        if(empty($client_renovation_budget)){
            $client_renovation_budget=[];
        }
        $lenderAmount=$this->mcdService->getTotalLenderAmount($house_id);
        if($lenderAmount){
            $client_renovation_budget['lender_fund_renovation']=$lenderAmount->total_funder_amount;
        }
        //$data                                        = (array)json_decode($client_renovation_budget);

        if (empty($client_renovation_budget))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $client_renovation_budget,'message' =>'success'], 200);
    }

    public function createClientRenovationBudget()
    {
        Log::info("ClientRenovationBudgetController: createClientRenovationBudget called");

        try
        {
            
            ## check input validation
            Log::info("ClientRenovationBudgetController: update validation check");
            $rules = ClientValidations::clientRenovationBudgetValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();


            $renovation_budget_info = $this->clientRenovationBudgetService->createClientRenovationBudget($this->request->all());

            if ($renovation_budget_info != false)
            {
                return response()->json(['message' => __("messages.record_saved")
                                         , 'data'   => ['house_id' => $renovation_budget_info['house_id'], 'id' => $renovation_budget_info->id]], 200);
                
            }
            else
            {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


  
}

