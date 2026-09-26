<?php

namespace App\Http\Controllers;
use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Services\PayoutService;
use App\Http\Validations\ClientValidations;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Storage;
use App\Models\AdditionalFieldModel;


class PayoutController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;


    /**
     * @param PayoutService $PayoutService
     */

    public function __construct(Request $request
        
        , PayoutService $payoutService

        
    )
    {
        Log::info("PayoutController: __construct called");
        $this->request              = $request;
        $this->payoutService        = $payoutService;

          
    }



    public function index($house_id)
    {
        
        Log::info("PayoutController: indexAll called");
        $payout['payout'] = $this->payoutService->getPayout($house_id);
        if (empty($payout))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        $payout['category']=$this->payoutService->getCategory();

        return response()->json(['data' => $payout,'status'=>'success'], 200);
       

    }


    public function payoutCreate()
    {   
        Log::info("PayoutController: payoutCreate called");
        try
        {
            
            Log::info("payoutCreate: create validation check");
            $rules = ClientValidations::payoutValidation();

            $all=$this->request->all();
            $validator = Validator::make($all, $rules);
            $validator->validate();

            if($all['category_id']=='new'){
                $category=$this->payoutService->createCategory(['category_name'=>$all['category_name'],'category_type'=>$all['payout_type']]);
                $all['category_id']=$category['id'];
            }
            
            $payout_info = $this->payoutService->updatePayoutDetail($all, $this->request->id);

            return response()->json(['data' => $payout_info, 'message' => __("messages.record_saved")], 200);
            
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    function updatePayoutCategory(){
        Log::info("PayoutController: updatePayoutCategory called");
        try
        {
            
            Log::info("updatePayoutCategory: create validation check");
            $rules = ClientValidations::payoutCategoryValidation();
            $all=$this->request->all();
            $validator = Validator::make($all, $rules);
            $validator->validate();
            $category=$this->payoutService->updatePayoutCategory($all['id'],['category_name'=>$all['category_name']]);
            
            return response()->json(['data' => $category, 'message' => __("messages.record_saved")], 200);
            
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    public function getPayoutDetail($house_id)
    {
        Log::info("PayoutController: payOutGet called");
        $payout = $this->payoutService->findPayoutDetail($house_id);
        if (empty($payout))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $payout], 200);
    }


    public function removePayout($id){
        $this->payoutService->removePayoutDetail($id);
        return response()->json(['status'=>'success','message' => __("messages.record_delete") , 'data'=>[]],200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePayoutRecord()
    {
        Log::info("PropertyController: update called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $all['house_id'];
        $saveData[$all['name']] = $all['value'];
        $info=$this->payoutService->updateOrCreate($all['house_id'], $saveData);

        return response()->json(['row' => __("messages.record_saved"),'data'=>$info->id,'status'=>'success'], 200);
    }

    function getCategory(){
        
        $info=$this->payoutService->getCategory();
        return response()->json(['row' => __("messages.record_saved"),'data'=>$info,'status'=>'success'], 200);

    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUpdateAdditionalField($house_id)
    {
        Log::info("payoutController: createUpdateAdditionalField called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $saveData['lender_id'] = $all['lender_id'];
        $saveData['field_type'] = $all['field_type'];
        if(isset($all['field_name'])){
            $saveData['field_name'] = $all['field_name'];
        }
        $id=$all['id']??'';
        $info=$this->payoutService->createUpdateAdditionalField($id, $saveData);

        return response()->json(['row' => __("messages.record_saved"),'data'=>$info,'status'=>'success'], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUpdatePayoutLender($house_id)
    {
        Log::info("payoutController: createUpdatePayoutLender called");
        # update information
        $all = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $info=$this->payoutService->createUpdatePayoutLender($house_id, $saveData);
        return response()->json(['row' => __("messages.record_saved"),'data'=>$info,'status'=>'success'], 200);
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHomeBuyerRecord()
    {
        Log::info("PropertyController: update called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $all['house_id'];
        $saveData[$all['name']] = $all['value'];
        $saveData['category_id'] = $all['category_id'];
        $saveData['payout_type'] = $all['payout_type'];

        if($all['payout_type']=='nonhud'){
            $info = $this->payoutService->homebuyerRenoCatUpdateOrCreate($saveData);
        }else{
            $info = $this->payoutService->updateCreatePayoutDetail($saveData);
        }

        return response()->json(['row' => __("messages.record_saved"),'data'=>$info->id,'status'=>'success'], 200);
    }

     /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyAdditionalField($id)
    {
        Log::info("McdController: mcd called");
        try
        {
            $info = AdditionalFieldModel::find($id);
            if ($info != null)
            {
                $info->delete();
                return response()->json(['status'  => 'success','message' => __("messages.record_delete")], 200);
            }
            else
            {
                return response()->json(['status'  => 'failed','message' => __("error_messages.record_not_exists")], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status'  => 'failed','message' => __("error_messages.something_wrong")], 400);
        }
    }

}