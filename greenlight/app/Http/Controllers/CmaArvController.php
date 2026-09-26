<?php

namespace App\Http\Controllers;

use App\Http\Validations\CmaArvValidations;
use App\Models\AverageDaysMarketModel;
use App\Models\SectionHistoryModel;
use App\Models\User;
use App\Services\CmaArvService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use App\Services\PropertyService;
use Illuminate\Http\Request;

use App\Http\Validations\PropertyValidations;
use App\Models\CmaArvSqftCompModel;
use App\Services\DocumentService;
use App\Models\DocumentSubtoModel;
use App\Models\CompSectionHistoryModel;

class CmaArvController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyService;
    private $cmaArvService;
    private $userService;
    private $documentService;

    /**
     * CmaArvController constructor.
     * @param Request $request
     * @param PropertyService $propertyService
     * @param CmaArvService $cmaArvService
     * @param UserService $userService
     */
    public function __construct(Request $request
        , PropertyService $propertyService
        , CmaArvService $cmaArvService
        , UserService $userService
        , DocumentService $documentService

    )
    {
        Log::info("CmaArvController: __construct called");
        $this->request = $request;
        $this->propertyService = $propertyService;
        $this->cmaArvService = $cmaArvService;
        $this->userService = $userService;
        $this->documentService=$documentService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cmaArvIndex($house_id)
    {
        Log::info("CmaArvController: CmaArvIndex called");

        $info = $this->cmaArvService->findAllByHouseId($house_id);

        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        $headers = ['Content-Type' => 'application/json; charset=UTF-8'];
        return response()->json(['data' => $info], 200, $headers, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Not in use now , if modify
     * @return \Illuminate\Http\JsonResponse
     */
    private function cmaArvCreate()
    {
        Log::info("CmaArvController: cmaArvCreate called");

        ## check input validation
        Log::info("CmaArvController: update validation check");
        $rules = CmaArvValidations::CmaArvAddValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # first check record exists or not
        $info = $this->propertyService->findOneById($this->request->input('house_id'));

        if (empty($info)) {
            return response()->json(['message' => __("error_messages.house_id_exists")], 400);
        }

        # update information
        $info = $this->cmaArvService->create($this->request->all());

        return response()->json(['row' => ['house_id' => $info->house_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cmaArvUpdate($house_id)
    {
        Log::info("CmaArvController: cmaArvUpdate called");

        $info_added_by = $this->request->input('info_added_by');
        $checkAccess = $this->checkCMAARVAccess($info_added_by);
        if ($checkAccess !== true) {
            return response()->json($checkAccess, 200);
        }

        ## check input validation
        Log::info("CmaArvController: update validation check");
        $rules = CmaArvValidations::CmaArvUpdateValidation();

        $all = $this->request->all();
        $all['house_id'] = $house_id;
        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info = $this->cmaArvService->getCmaArvByWhere(["house_id" => $house_id, 'info_added_by' => @$all['info_added_by']]);

        if (empty($info)) {
            ## Test case
            # fresh, no date, no user_id ---- no entry
            # fresh, date, no user_id ------- entry
            # fresh, no date, user_id ------ entry
            # fresh, date, user_id ------ entry
            if ((!empty(@$all['user_id']) || !empty(@$all['date']))) {
                SectionHistoryModel::create([
                    "house_id" => $house_id,
                    "foreign_id" => $house_id,
                    'user_id' => $this->request->input('user_id'),
                    'date_by' => $this->request->input('date'),
                    'section_type' => $info_added_by,
                    'modify_by' => $this->userService->user_id()
                ]);

            }

            $this->cmaArvService->create($all);
        } else {

            if (!$this->userService->is_admin() &&  @$all['user_id'] != $this->userService->user_id()) {
                unset($all['user_id']);
                unset($all['date']);
            }

            # update, no date, no user_id ---- no entry
            # update, same date, no user_id ------- no entry
            # update, diff date, no user_id ------- entry
            # update, no date, same user_id ------- no entry
            # update, no date, diff user_id ------- entry
            # update, same date, same user_id ---- no entry
            # update, same date, diff user_id ----  entry
            # update, diff date, same user_id ----  entry
            # update, diff date, diff user_id ----  entry
            if ((!empty(@$all['user_id']) || !empty(@$all['date']))) {
                if (!empty(@$all['user_id']) && $info->user_id != @$all['user_id']) {
                    // make an entry
                    SectionHistoryModel::create([
                        "house_id" => $house_id,
                        "foreign_id" => $house_id,
                        'user_id' => $this->request->input('user_id'),
                        'date_by' => $this->request->input('date'),
                        'section_type' => $info_added_by,
                        'modify_by' => $this->userService->user_id()
                    ]);
                } else if (!empty(@$all['date']) && $info->date != @$all['date']) {
                    // make an entry
                    SectionHistoryModel::create([
                        "house_id" => $house_id,
                        "foreign_id" => $house_id,
                        'user_id' => $this->request->input('user_id'),
                        'date_by' => $this->request->input('date'),
                        'section_type' => $info_added_by,
                        'modify_by' => $this->userService->user_id()
                    ]);
                }
            }

            $this->cmaArvService->updateReference($info, $all);
        }

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    public function adomIndex($house_id){

        $info = AverageDaysMarketModel::where('house_id',$house_id)->get();
        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    public function adomUpdateOrCreate($house_id){

        $all = $this->request->all();
        $info_added_by = @$all['type'];//$this->request->input('type');

        $checkAccess = $this->checkCMAARVAccess($info_added_by);
        if ($checkAccess !== true) {
            return response()->json($checkAccess, 200);
        }

        #ToDo: add update or added by notes here. who added info
        $info = AverageDaysMarketModel::where(['house_id'=>$house_id, "type"=>$info_added_by]);

        if ($info->count() > 0) {
            $adom = $info->first();

            $update = [];
            $update['json'] = $all['json'];
            $adom->update($update);

        }
        else
        {
            $insert = [];
            $insert['type'] = $all['type'];
            $insert['house_id'] = $house_id;
            $insert['added_by'] = $this->userService->user_id();
            $insert['json'] = $all['json'];
            $adom = AverageDaysMarketModel::create($insert);

        }

        return response()->json(['data' => $adom,'message' => __("messages.record_saved")], 200);
    }


    private function checkCMAARVAccess($info_added_by)
    {
        ## check user current role type
        if($this->userService->is_admin() || $this->userService->is_web_team())
        {
            // return response()->json(['status' => 'failed', 'message' => __("messages.not_authorize_section")], 200);

        }
        else if( !(
            $this->userService->is_first_dtc() ||
            $this->userService->is_second_dca() ||
            $this->userService->is_chief_dca() ||
            $this->userService->is_buyer()

        ))
        {
            return ['status' => 'failed', 'message' => __("messages.not_authorize_section")];

        }

        else{
            if($this->userService->is_first_dtc() && $info_added_by != 'first_dtc')
            {
                // first_dtc
                return ['status' => 'failed', 'message' => __("error_messages.dtc_dca_chief_dca",['InfoAddedBy'=>
                        ucwords(
                            str_replace('_'," ",$info_added_by)
                        )]
                )];
            }

            if($this->userService->is_second_dca() && $info_added_by != 'second_dca')
            {
                // first_dtc
                return ['status' => 'failed', 'message' => __("error_messages.dtc_dca_chief_dca",['InfoAddedBy'=>ucwords(str_replace('_'," ",$info_added_by))])];
            }
            if($this->userService->is_chief_dca() && $info_added_by != 'third_dca' &&  $info_added_by != 'final_dca')
            {
                return ['status' => 'failed', 'message' => __("error_messages.dtc_dca_chief_dca" ,['InfoAddedBy'=>ucwords(str_replace('_'," ",$info_added_by))])];
            }

            if($this->userService->is_buyer() && $info_added_by != 'wholesale_buyer')
            {
                return ['status' => 'failed', 'message' => __("error_messages.dtc_dca_chief_dca", ['InfoAddedBy'=>ucwords(str_replace('_'," ",$info_added_by))])];
            }

        }

        return true;

    }


     

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cmaArvSingleUpdate($house_id)
    {
        Log::info("CmaArvController: cmaArvUpdate called");
       
        $info_added_by = $this->request->input('info_added_by');
        $checkAccess = $this->checkCMAARVAccess($info_added_by);
        if ($checkAccess !== true) {
            return response()->json($checkAccess, 200);
        }
        
        ## check input validation
        Log::info("CmaArvController: update validation check");
        $rules = CmaArvValidations::CmaArvAddSingleValidation();

        $all = $this->request->all();
        
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $saveData['info_added_by'] =$all['info_added_by'];
        $validator = Validator::make($saveData, $rules);
        $validator->validate();

        $info = $this->cmaArvService->getCmaArvByWhere(["house_id" => $house_id, 'info_added_by' => @$saveData['info_added_by']]);
        
        $saveHistory = ['ssd_sale_comps','gsd_sale_comps','ssd_sold_comps','gsd_sold_comps','rental_comps_map','comp_url_1','comp_url_2','comp_url_3','comp_url_4'];
        if (empty($info)) {
            ## Test case
            # fresh, no date, no user_id ---- no entry
            # fresh, date, no user_id ------- entry
            # fresh, no date, user_id ------ entry
            # fresh, date, user_id ------ entry
            if (!empty(@$saveData['user_id'])) {
                SectionHistoryModel::create([
                    "house_id" => $house_id,
                    "foreign_id" => $house_id,
                    'user_id' => $saveData['user_id'],
                    'date_by' =>date('Y-m-d'),
                    'section_type' => $info_added_by,
                    'modify_by' => $this->userService->user_id()
                ]);
            }

            if (!empty(@$saveData['date'])) {
                SectionHistoryModel::create([
                    "house_id" => $house_id,
                    "foreign_id" => $house_id,
                    'user_id' =>$this->userService->user_id(),
                    'date_by' =>date('Y-m-d'),
                    'section_type' => $info_added_by,
                    'modify_by' => $this->userService->user_id()
                ]);
            }

            

            $result = $this->cmaArvService->create($saveData);
            
            if (in_array($all['name'],$saveHistory)) {

                    $compressed = gzdeflate($all['value'],  9);
                    $compressed = base64_encode($compressed);
                    CompSectionHistoryModel::create([
                        'user_id' =>$this->userService->user_id(),
                        "house_id" => $house_id,
                        'section_type' => $info_added_by,
                        'section_name' => $all['name'],
                        'section_value'=>$compressed,
                        'date_by' => date('Y-m-d'),
                        "foreign_id" => $all['id']??$result->id]);
            }

        } else {

            // if (!$this->userService->is_admin() &&  @$saveData['user_id'] != $this->userService->user_id()) {
            //     unset($saveData['user_id']);
            //     unset($saveData['date']);
            // }

            # update, no date, no user_id ---- no entry
            # update, same date, no user_id ------- no entry
            # update, diff date, no user_id ------- entry
            # update, no date, same user_id ------- no entry
            # update, no date, diff user_id ------- entry
            # update, same date, same user_id ---- no entry
            # update, same date, diff user_id ----  entry
            # update, diff date, same user_id ----  entry
            # update, diff date, diff user_id ----  entry
            if ((!empty(@$saveData['user_id']) || !empty(@$saveData['date']))) {
                if (!empty(@$saveData['user_id']) && $info->user_id != @$saveData['user_id']) {
                    // make an entry
                    SectionHistoryModel::create([
                        "house_id" => $house_id,
                        "foreign_id" => $house_id,
                        'user_id' => $saveData['user_id'],
                        'date_by' => date('Y-m-d'),
                        'section_type' => $info_added_by,
                        'modify_by' => $this->userService->user_id()
                    ]);
                } else if (!empty(@$saveData['date']) && $info->date != @$saveData['date']) {
                    // make an entry
                    SectionHistoryModel::create([
                        "house_id" => $house_id,
                        "foreign_id" => $house_id,
                        'user_id' => $this->userService->user_id(),
                        'date_by' => $saveData['date'],
                        'section_type' => $info_added_by,
                        'modify_by' => $this->userService->user_id()
                    ]);
                }
            }

            if (in_array($all['name'],$saveHistory)) {
           
                $compressed = gzdeflate($all['value'],  9);
                $compressed = base64_encode($compressed);
                CompSectionHistoryModel::create([
                    'user_id' =>$this->userService->user_id(),
                    "house_id" => $house_id,
                    'section_type' => $info_added_by,
                    'section_name' => $all['name'],
                    'section_value'=>$compressed,
                    'date_by' => date('Y-m-d'),
                    "foreign_id" => $all['id']??$info->id]);
            }

            $this->cmaArvService->updateReference($info, $saveData);

        }

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    public function sqftIndex($house_id){

        $info = CmaArvSqftCompModel::where('house_id',$house_id)->get();
        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    public function sqftUpdateOrCreate($house_id){

        $all = $this->request->all();
        $info_added_by = @$all['cma_type'];//$this->request->input('type');

        $checkAccess = $this->checkCMAARVAccess($info_added_by);
        if ($checkAccess !== true) {
            return response()->json($checkAccess, 200);
        }

        #ToDo: add update or added by notes here. who added info
        $info = CmaArvSqftCompModel::where(['house_id'=>$house_id, "cma_type"=>$info_added_by]);

        if ($info->count() > 0) {
            $adom = $info->first();

            $update = [];
            $update['sqft_json_data'] = $all['json'];
            $adom->update($update);

        }
        else
        {
            $insert = [];
            $insert['cma_type'] = $all['cma_type'];
            $insert['house_id'] = $house_id;
            $insert['added_by'] = $this->userService->user_id();
            $insert['sqft_json_data'] = $all['json'];
            $adom = CmaArvSqftCompModel::create($insert);

        }

        return response()->json(['data' => $adom,'message' => __("messages.record_saved")], 200);
    }

    function updateSubTo($house_id){
        
        Log::info("CmaArvController: update called");
        if (empty($house_id))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }
        # update information
        $all = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $this->cmaArvService->createUpdateSubTo($house_id, $saveData);

        return response()->json(['row' => __("messages.record_saved")], 200);

    }

    function uploadSubToDocument(){
        #var_dump($this->request->all());die;
        Log::info("CmaArvController: uploadSubToDocument called");

        try {
            ## check input validation
            Log::info("uploadSubToDocument: update validation check");
            // $rules = MortgageOtherLiensPropertyTaxesValidations::liensDocumentValidation();
            // $validator = Validator::make($this->request->all(), $rules);
            // $validator->validate();
            $all = $this->request->all();
            $doc_info = $this->documentService->documentUpload('subto_document');
            $doc_info['subto_doc_type']=$all['documentType'];
            $doc_info['state']=$this->propertyService->findOneById($all['house_id'])->state;;
            if ($doc_info != false) {
                $doc = $this->cmaArvService->subtoDocucment($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'row'     => ['url' => $doc_info['document_url'],
                                                       'id'  => $doc->document_mortgage_id],$doc_info,$all], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    function updateSubToInfo($house_id){
        
        Log::info("CmaArvController: update called");
        if (empty($house_id))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }
        # update information
        $all = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData['strategy_option'] =!empty($all['strategy_option'])?implode('|||',$all['strategy_option']):'';
        $saveData['after_foreclosure'] =!empty($all['after_foreclosure'])?implode('|||',$all['after_foreclosure']):'';
        $saveData['checklist'] =!empty($all['checklist'])?implode('|||',$all['checklist']):'';
        $this->cmaArvService->createUpdateSubTo($house_id, $saveData);

        return response()->json(['message' => __("messages.record_saved")], 200);

    }
    

    function getSubToPropertyInfo($house_id){
        $state=$this->propertyService->findOneById($house_id)->state;;
        $result=$this->cmaArvService->getSubToPropertyInfo($house_id,$state);
        $subToDoc=[];        
        $docResult=$this->cmaArvService->getSubToPropertyDocument($house_id,$state);

        if(!empty($docResult)){
            foreach($docResult as $subToDocument){
                $subToDoc[$subToDocument->subto_doc_type][]=$subToDocument;
            }  
        }
        $result['subto_document']=$docResult;
        return response()->json(['message' => __("messages.record_saved"),"data"=>$result], 200);

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSubtoDocument($document_id) {
        Log::info("propertyDocumentUpload: removeSubtoDocument called");
        try {
            $info = DocumentSubtoModel::find($document_id);
            if (!empty($info)) {
                $info->update(['is_deleted'=>1]);
                return response()->json(['message' => __("messages.document_delete"),'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }
}
