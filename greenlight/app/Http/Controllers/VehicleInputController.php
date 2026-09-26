<?php

namespace App\Http\Controllers;

// use App\Http\Validations\CmaArvValidations;
// use App\Models\AverageDaysMarketModel;
// use App\Models\SectionHistoryModel;
// use App\Models\User;
// use App\Services\CmaArvService;
// use App\Services\UserService;
 use Validator;
 Use Log;
// use App\Helpers\CommonHelper;
 use App\Services\VehicleInputService;
 use Illuminate\Http\Request;
 use App\Services\DocumentService;
// use App\Http\Validations\PropertyValidations;
// use App\Models\CmaArvSqftCompModel;

// use App\Models\DocumentSubtoModel;

class VehicleInputController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $vehicleInputService;
    private $documentService;

    /**
     * VehicleInputController constructor.
     * @param Request $request
     * @param PropertyService $propertyService
     * @param CmaArvService $cmaArvService
     * @param UserService $userService
     */
    public function __construct(Request $request
        , VehicleInputService $vehicleInputService
        , DocumentService $documentService
    )
    {
        Log::info("CmaArvController: __construct called");
        $this->request = $request;
        $this->vehicleInputService = $vehicleInputService;
        $this->documentService=$documentService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info("CmaArvController: CmaArvIndex called");
        $info['vehicle_sale_info'] = $this->vehicleInputService->getVehicleSaleInfo();
        $info['vehicle_info'] = $this->vehicleInputService->getVehicleInfo();
        $info['vehicle_cma_arv_info'] = $this->vehicleInputService->getVehicleCmaArv();
        $info['vehicle_nos_info'] = $this->vehicleInputService->getVehicleNos();
        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        $headers = ['Content-Type' => 'application/json; charset=UTF-8'];
        return response()->json(['data' => $info], 200, $headers, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function getVehicleInfo($vehicleId){
        $info=$this->vehicleInputService->getVehicleInfoDetail($vehicleId);
        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        $headers = ['Content-Type' => 'application/json; charset=UTF-8'];
        return response()->json(['status'=>'success','data' => $info], 200, $headers, JSON_INVALID_UTF8_SUBSTITUTE);
 
    }
    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function VehicleInputSingleUpdate()
    {
        Log::info("VehicleInputController: VehicleInputSingleUpdate called");
       
        // $info_added_by = $this->request->input('info_added_by');
        // $checkAccess = $this->checkCMAARVAccess($info_added_by);
        // if ($checkAccess !== true) {
        //     return response()->json($checkAccess, 200);
        // }
        
        ## check input validation
        Log::info("CmaArvController: update validation check");
        //$rules = CmaArvValidations::CmaArvAddSingleValidation();

        $all = $this->request->all();
        
        //$saveData=[];
        //$saveData[$all['name']] = $all['value'];
        // $saveData['info_added_by'] =$all['info_added_by'];
        // $validator = Validator::make($saveData, $rules);
        // $validator->validate();
        $id=$all['id']??'';
        $info = $this->vehicleInputService->createUpdateVehicleSale($id,$all);
        
        if(!empty($all['respondent_info_data'])){
            foreach($all['respondent_info_data'] as $respondent){
                $respondentInfo['vehicle_sale_id']=$info->id;
                $respondentInfo['respondent']=$respondent['respondent'];
                $this->vehicleInputService->updateCreateVehicleSaleRespondentInfo($respondent['id'],$respondentInfo);
            }
        }

        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info->id], 200);
    }


    function createUpdateVehicleInfo(){
        Log::info("McdController: createUpdateVehicleInfo called");
        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();
        $all             = $this->request->all();
        $id              = $all['id']??'';
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        $vehicle_document = $this->documentService->documentUpload('vehicle_document','vehicle_document');
        
        if(!empty($vehicle_document) && $vehicle_document['store_name'] != $vehicle_document['vehicle_document'])
        {
            $all['store_name'] = $vehicle_document['store_name'];
            $all['org_name'] = $vehicle_document['org_name'];
        }
        $info=$this->vehicleInputService->updateCreateVehicleInput($id, $all);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
 
    }

    function createUpdateVehicleCmaArvInfo(){
        Log::info("McdController: createUpdateVehicleCmaArvInfo called");
        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();
        $all             = $this->request->all();
        $id              = $all['id']??'';
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        $info=$this->vehicleInputService->updateCreateVehicleCmaArv($id, $all);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
 
    }


    function createUpdatevehicleNosInfo(){
        Log::info("McdController: createUpdatevehicleNosInfo called");
        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();
        $all             = $this->request->all();
        $id              = $all['id']??'';
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        $vehicle_document = $this->documentService->documentUpload('vehicle_nos_document','vehicle_nos_document');
        if(!empty($vehicle_document) && $vehicle_document['store_name'] != $vehicle_document['vehicle_nos_document'])
        {
            $all['store_name'] = $vehicle_document['store_name'];
            $all['org_name'] = $vehicle_document['org_name'];
        }
        $info=$this->vehicleInputService->updateCreateVehicleNosInfo($id, $all);
        $result=$this->vehicleInputService->getVehicleNosInfoById($info->id);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$result], 200);
 
    }

}
