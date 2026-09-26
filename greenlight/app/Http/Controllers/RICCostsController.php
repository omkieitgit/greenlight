<?php
/**
 * Created By Rativardhan Singh Sengar  3/7/19 10:30 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/7/19 10:28 PM
 */

namespace App\Http\Controllers;

use App\Http\Validations\PropertyAcquisitionBtoCValidations;
use App\Http\Validations\RCICostsValidations;
use App\Models\CarryCostsModel;
use App\Models\DocumentRenovationCosts;
use App\Models\IncidentalCostsModel;
use App\Services\DocumentService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

use App\Services\ClientService;
use App\Services\PayoutService;

class RICCostsController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $documentService;
    private $clientService;
    private $payoutService;


    public function __construct(Request $request
    , DocumentService $documentService
    , ClientService $clientService
    , PayoutService $payoutService
    )
    {
        Log::info("RICCostsController: __construct called");
        $this->request                              = $request;
        $this->documentService                      = $documentService;
        $this->clientService                        = $clientService;
        $this->payoutService                        = $payoutService;
    }

    public function index($house_id)
    {
        Log::info("RICCostsController: index called");
        $info = [];
        $info['incidental_costs'] = $this->clientService->getRenovationDetail($house_id,'incidental');
        $info['carry_costs'] = $this->clientService->getRenovationDetail($house_id,'carry');
        $info['renovation_costs'] = $this->clientService->getRenovationDetail($house_id,'renovation');
        $info['category_by_costs'] = $this->clientService->getRenovationByCat($house_id);
        $info['atual_burn_rate'] = $this->clientService->getActualBurnRateByHouseId($house_id);
        $info['burn_rate_detail'] = $this->clientService->getBurnRateDetailByHouseId($house_id);
        $info['payout'] = $this->payoutService->getPayoutInfo($house_id);

        // DocumentRenovationCosts::where('house_id', $house_id)->with(['user'])->get();
        // IncidentalCostsModel::where('house_id', $house_id)->get();
        // CarryCostsModel::where('house_id', $house_id)->get();

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    //updateIncidental

    public function updateOrCreateIncidental()
    {

        Log::info("RICCostsController: updateOrCreateIncidental called");

        ## check input validation
        Log::info("RICCostsController: update validation check");
        $rules = RCICostsValidations::updateCreateIncidental();

        $all             = $this->request->all();
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $info = IncidentalCostsModel::updateOrCreate(['id'=>@$all['id']],$all);

        return response()->json(['message' => __("messages.record_saved"), 'data'=>$info], 200);

    }


    public function deleteIncidental($id)
    {

        Log::info("RICCostsController: delete called");

        try
        {
            $info = IncidentalCostsModel::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                    , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function updateOrCreateCarry()
    {

        Log::info("RICCostsController: updateOrCreateCarry called");

        ## check input validation
        Log::info("RICCostsController: update validation check");
        $rules = RCICostsValidations::updateCreateCarry();

        $all             = $this->request->all();
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $info = CarryCostsModel::updateOrCreate(['id'=>@$all['id']],$all);

        return response()->json(['message' => __("messages.record_saved"), 'data'=>$info], 200);

    }

    public function deleteCarry($id)
    {

        Log::info("RICCostsController: delete called");

        try
        {
            $info = CarryCostsModel::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                    , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function updateOrCreateRenovation()
    {

        Log::info("RICCostsController: updateOrCreateRenovation called");

        $all             = $this->request->all();
        try {
            ## check input validation
            Log::info("updateOrCreateRenovation: update validation check");
            $rules = RCICostsValidations::renovationDocumentValidation();
            $validator = Validator::make($all, $rules);

            // We are not using this because , we want to return all error into one array box
            if ($validator->fails()) {
                Log::info("updateOrCreateRenovation: validation failed");
                $message = CommonHelper::customValidatorMessageArray($validator);
                return response()->json(['message' => $message], 400);
            }
            else
            {
                Log::info("updateOrCreateRenovation: validation pass");
            }

            $doc_info = $this->documentService->documentUploadBase64($all,'document_renovation_costs');
            if ($doc_info != false) {
                $all['store_name'] = $doc_info['store_name'];
                $all['org_name'] =  $doc_info['org_name'];
                $all['added_by'] = $doc_info['added_by'];
                if(empty($all['id']))
                $all['created_at'] = date('Y-m-d H:i:s');

                $all['updated_at'] = date('Y-m-d H:i:s');
                $info = DocumentRenovationCosts::updateOrCreate(['id'=>@$all['id']],$all);
                $info2 = DocumentRenovationCosts::where('id',$info->id)->with(['user'])->get();
                return response()->json(['message' => __("messages.document_upload"),
                    'data'     => $info2
                ], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function deleteRenovation($id)
    {

        Log::info("deleteRenovation: delete called");

        try
        {
            $info = DocumentRenovationCosts::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                    , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


}
