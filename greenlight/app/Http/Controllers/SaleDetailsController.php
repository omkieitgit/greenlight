<?php

namespace App\Http\Controllers;


use App\Http\Validations\SaleDetailsValidations;
use App\Models\DocumentBidderModel;
use App\Models\DocumentSaleModel;
use App\Models\SaleBidderNotesModel;
use App\Models\SectionHistoryModel;
use App\Services\AssessmentService;
use App\Services\PropertyDescriptionsService;
use App\Services\SaleBidderService;
use App\Services\SaleDetailsDescriptionsService;
use App\Services\SaleDetailsService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use App\Http\Validations\PropertyValidations;
use App\Http\Validations\CommonValidations;
use App\Services\DocumentService;
use App\Models\SaleBidderModel;
use App\Services\AlarmMeService;


class SaleDetailsController extends Controller {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $validations;
    private $saleDetailsService;
    private $saleDetailsDescriptionsService;
    private $documentService;
    private $saleBidderService;
    private $userService;
    private $alarmMeService;
    

    /**
     * SaleDetailsController constructor.
     * @param Request $request
     * @param SaleDetailsService $saleDetailsService
     * @param SaleDetailsDescriptionsService $saleDetailsDescriptionsService
     * @param DocumentService $documentService
     * @param SaleBidderService $saleBidderService
     * @param UserService $userService
     */
    public function __construct(
        Request $request,
        SaleDetailsService $saleDetailsService,
        SaleDetailsDescriptionsService $saleDetailsDescriptionsService,
        DocumentService $documentService, SaleBidderService $saleBidderService,
        UserService $userService,
        AlarmMeService $alarmMeService
    ) {
        Log::info("SaleDetailsController: __construct called");
        $this->request                        = $request;
        $this->saleDetailsService             = $saleDetailsService;
        $this->saleDetailsDescriptionsService = $saleDetailsDescriptionsService;
        $this->documentService                = $documentService;
        $this->saleBidderService              = $saleBidderService;
        $this->userService                    = $userService;
        $this->alarmMeService                 = $alarmMeService;
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAll($house_id) {
        Log::info("SaleDetailsController: indexAll called");
        $info = [];

        $temp = $this->saleDetailsService->findSaleDetailsInformation($house_id);

        $temp->map(function ($order) {

            $order->bidders->map(function ($order1) {

                $order1->bidder_notes = @$order1->notes->notes;
                $order1->makeHidden(['notes']);

                return $order1;
            });
            return $order;
        });

        $info['sale'] = $temp;

        #$info['property_info']['document_property'] = $this->documentService->getPropertyDocument($house_id);

        if (empty($info['sale'])) {
            return response()->json(['data' => [],'status' => 'failed','message' => __("error_messages.record_not_exists")], 200);
        }
        return response()->json(['status' => 'success','data' => $info], 200);

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        Log::info("SaleDetailsController: create called");

        ## check input validation
        Log::info("SaleDetailsController: create validation check");
        $rules     = SaleDetailsValidations::saleDetailsCreateValidation();
        $all       = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $all['opening_bid'] = CommonHelper::dbNumberFormat($all['opening_bid']);
        $all['trustee_phone'] = CommonHelper::dbNumberFormat($all['trustee_phone']);
        $info = $this->saleDetailsService->create($all);
        if (!empty($info->sale_id)) {
            $this->saleDetailsDescriptionsService->updateOrCreate($info->sale_id, $all);
        }

        # update information
        if (!empty(@$all['nos_by']) || !empty(@$all['nos_date'])) {
            SectionHistoryModel::create(["house_id"     => $all['house_id'],
                                         "foreign_id"   => $info->sale_id,
                                         'user_id'      => $this->request->input('nos_by'),
                                         'date_by'      => $this->request->input('nos_date'),
                                         'section_type' => 'nos_by',
                                         'modify_by'    => $this->userService->user_id(),

                                        ]);
        }

        # update information
        if (!empty($all['im_by']) || !empty(@$all['im_date'])) {
            SectionHistoryModel::create(["house_id"     => $all['house_id'],
                                         "foreign_id"   => $info->sale_id,
                                         'user_id'      => $this->request->input('im_by'),
                                         'date_by'      => $this->request->input('im_date'),
                                         'section_type' => 'im_by',
                                         'modify_by'    => $this->userService->user_id(),
                                        ]);
        }

        if (!empty($all['trustee_caller']) || !empty(@$all['trustee_caller_date'])) {
            SectionHistoryModel::create(["house_id"     => $all['house_id'],
                                         "foreign_id"   => $info->sale_id,
                                         'user_id'      => $this->request->input('trustee_caller'),
                                         'date_by'      => $this->request->input('trustee_caller_date'),
                                         'section_type' => 'trustee_caller',
                                         'modify_by'    => $this->userService->user_id(),
                                        ]);
        }

        return response()->json(['data'    => ['sale_id' => $info->sale_id],
                                'status' => 'success',
                                 'message' => __("messages.record_saved")], 200);
    }


    /**
     * @param $sale_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($sale_id) {
        Log::info("SaleDetailsController: update called");

        # first check record exists or not
        $isExists = $this->saleDetailsService->findOneById($sale_id);

        if (empty($isExists)) {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        # if record exists update information
        ## check input validation
        Log::info("SaleDetailsController: update validation check");
        $rules = SaleDetailsValidations::saleDetailsUpdateValidation();
        $all = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # update information
        if(array_key_exists('nos_by',$all) && $isExists->nos_by != $all['nos_by']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $isExists->house_id,
                                            "foreign_id"   => $isExists->sale_id,
                                            'user_id'      => $this->request->input('nos_by'),
                                            'date_by'      => $this->request->input('nos_date'),
                                            'section_type' => 'nos_by',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }
        else if(array_key_exists('nos_date',$all) && $isExists->nos_date != $all['nos_date']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $isExists->house_id,
                                            "foreign_id"   => $isExists->sale_id,
                                            'user_id'      => $this->request->input('nos_by'),
                                            'date_by'      => $this->request->input('nos_date'),
                                            'section_type' => 'nos_by',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }
        # update information
        if(array_key_exists('im_by',$all) && $isExists->im_by != $all['im_by']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $isExists->house_id,
                                            "foreign_id"   => $isExists->sale_id,
                                            'user_id'      => $this->request->input('im_by'),
                                            'date_by'      => $this->request->input('im_date'),
                                            'section_type' => 'im_by',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }
        else if(array_key_exists('im_date',$all) && $isExists->im_date != $all['im_date']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $isExists->house_id,
                                            "foreign_id"   => $isExists->sale_id,
                                            'user_id'      => $this->request->input('im_by'),
                                            'date_by'      => $this->request->input('im_date'),
                                            'section_type' => 'im_by',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }

        # update information
        if(array_key_exists('trustee_caller',$all) && $isExists->trustee_caller != $all['trustee_caller']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $isExists->house_id,
                                            "foreign_id"   => $isExists->sale_id,
                                            'user_id'      => $this->request->input('trustee_caller'),
                                            'date_by'      => $this->request->input('trustee_caller_date'),
                                            'section_type' => 'trustee_caller',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }
        else if(array_key_exists('trustee_caller_date',$all) && $isExists->im_date != $all['trustee_caller_date']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $isExists->house_id,
                                            "foreign_id"   => $isExists->sale_id,
                                            'user_id'      => $this->request->input('trustee_caller'),
                                            'date_by'      => $this->request->input('trustee_caller_date'),
                                            'section_type' => 'trustee_caller',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }
        # update information
        $info = $this->saleDetailsService->update($sale_id, $all);
        $this->saleDetailsDescriptionsService->updateOrCreate($sale_id, $all);

        return response()->json(['message' => __("messages.record_saved"),'status' => 'success'], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function documentUpload() {
        #var_dump($this->request->all());die;
        Log::info("propertyDocumentUpload: propertyDocumentUpload called");

        try {
            ## check input validation
            Log::info("documentUpload: update validation check");
            $rules = SaleDetailsValidations::documentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_sale');
            if ($doc_info != false) {
                $doc = $this->documentService->sale($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'data'    => ['url' => $doc_info['document_url'],'id'  => $doc->id],
                                         'status' => 'success'
                                           ], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
        }

    }

    public function documentDelete($document_id) {
        Log::info("propertyDocumentUpload: documentDelete called");

        try {
            $info = DocumentSaleModel::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                        'status' => 'success',
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                        'status' => 'failed',
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBidder() {
        Log::info("SaleDetailsController: createBidder called");

        ## check input validation
        Log::info("SaleDetailsController: createBidder validation check");
        $rules     = SaleDetailsValidations::saleDetailsCreateBidderValidation();
        $all       = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        $sale_info = $this->saleDetailsService->findOneById($this->request->input('sale_id'))->toArray();
        $all['house_id'] = $sale_info['house_id'];

        # create information
        $info = $this->saleBidderService->create($this->request->all());

        SaleBidderNotesModel::updateOrCreate(
            ["bidder_id"=> $info->bidder_id],
            ['notes' => @$all['bidder_notes']]
        );

        # update information
        if (!empty($all['im_by']) || !empty(@$all['im_date'])) {
            SectionHistoryModel::create(["house_id"     => $all['house_id'],
                                         "foreign_id"   => $info->bidder_id,
                                         'user_id'      => $all['im_by'],
                                         'date_by'      => $this->request->input('im_date'),
                                         'section_type' => 'im_by',
                                         'modify_by'    => $this->userService->user_id(),

                                        ]);
        }

        return response()->json(['data'    => $info,
                                  'status' => 'success',
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBidder($bidder_id) {
        Log::info("SaleDetailsController: updateBidder called");

        # first check record exists or not
        $info = $this->saleBidderService->findOneById($bidder_id);

        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists"),'status' => 'failed'], 400);
        }

        ## check input validation
        Log::info("SaleDetailsController: updateBidder validation check");
        $rules = SaleDetailsValidations::saleDetailsUpdateBidderValidation();
        $all = $this->request->all();

        ## ToDo: ask vikas to fix this
        if(@$all['date_of_report'] == 'NaN-NaN-NaN')
        $all['date_of_report'] = NULL;
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # update information
        if(array_key_exists('im_by',$all) && $info->im_by != $all['im_by']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $info->house_id,
                                            "foreign_id"   => $info->sale_id,
                                            'user_id'      => $this->request->input('im_by'),
                                            'date_by'      => $this->request->input('im_date'),
                                            'section_type' => 'im_by',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }
        else if(array_key_exists('im_date',$all) && $info->im_date != $all['im_date']){
            // make an entry
            SectionHistoryModel::create([
                                            "house_id"     => $info->house_id,
                                            "foreign_id"   => $info->sale_id,
                                            'user_id'      => $this->request->input('im_by'),
                                            'date_by'      => $this->request->input('im_date'),
                                            'section_type' => 'im_by',
                                            'modify_by'    => $this->userService->user_id(),
                                        ]);
        }

        # create information
        $this->saleBidderService->update($bidder_id, $all);
        SaleBidderNotesModel::updateOrCreate(
            ["bidder_id"=> $bidder_id],
            ['notes' => @$all['bidder_notes']]
        );

        return response()->json(['data'    => $info,
                                  'status' => 'success',
                                 'message' => __("messages.record_saved")], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function documentUploadBidder() {

        Log::info("SaleDetailsController: documentUploadBidder called");

        try {
            ## check input validation
            Log::info("documentUploadBidder: update validation check");
            $rules = SaleDetailsValidations::documentBidderValidation();
            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_bidder');
            if ($doc_info != false) {
                $doc = $this->documentService->bidder($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'data'    => ['url' => $doc_info['document_url'],'id'  => $doc->id],
                                         'status' => 'success'
                                        ], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 200);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 200);
        }

    }

    public function documentDeleteBidder($document_id) {
        Log::info("SaleDetailsController: documentDeleteBidder called");

        try {
            $info = DocumentBidderModel::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'status' => 'success',
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'status' => 'failed',
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
        }
    }


        /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUpdateSingleSaleRecord($house_id) {
        
        Log::info("createUpdateSingleRecord: create called");

        ## check input validation
        //$rules     = SaleDetailsValidations::saleDetailsCreateValidation();
        
        $all       = $this->request->all();
        $saveItem[$all['name']]=$all['value'];
        $saveItem['house_id']=$house_id;
        # first check record exists or not
       
        //$validator = Validator::make($all, $rules);
        //$validator->validate();

        # create information
        //$saveItem['opening_bid'] = CommonHelper::dbNumberFormat(@$asaveItemll['opening_bid']);
        //$saveItem['trustee_phone'] = CommonHelper::dbNumberFormat(@$saveItem['trustee_phone']);
        if(!empty($all['id'])){
            $info = $this->saleDetailsService->findOneById($all['id']);
            if (empty($info)) {
                return response()->json(['message' => __("error_messages.record_not_exists")], 400);
            }
            $this->saleDetailsService->update($all['id'], $saveItem);
            $sale_id=$info->sale_id;
           
        }else{
            $info = $this->saleDetailsService->create($saveItem);
            $this->alarmMeService->sendEmailAlarmSettingUser($house_id);
            $sale_id=$info->sale_id;
            
        }

        $this->saleDetailsDescriptionsService->updateOrCreate($sale_id, $saveItem);
        
        

        $sectionArray=['nos_by','im_by','trustee_caller'];
        if(in_array($all['name'],$sectionArray)){
            $this->updateSectionHistory($all['name'],$info,$all['value']);
        }

        $sectionArray=['nos_date','im_date','trustee_caller_date'];
        if(in_array($all['name'],$sectionArray)){
            $this->updateDateSectionHistory($all['name'],$info,$all['value']);
        }

       
        return response()->json(['data'    => $sale_id,
                                'status' => 'success',
                                'message' => __("messages.record_saved")], 200);
    }


    

    public function updateSectionHistory($section,$saleInfo,$fieldName){

        SectionHistoryModel::create([
            "house_id"     => $saleInfo->house_id,
            "foreign_id"   => $saleInfo->sale_id,
            'user_id'      => $fieldName,
          //  'date_by'      => $this->request->input('nos_date'),
            'section_type' => $section,
            'modify_by'    => $this->userService->user_id(),
        ]);
    }

    public function updateDateSectionHistory($section,$saleInfo,$fieldName){

        SectionHistoryModel::create([
            "house_id"     => $saleInfo->house_id,
            "foreign_id"   => $saleInfo->sale_id,
            'date_by'      => $fieldName,
            'section_type' => $section,
            'modify_by'    => $this->userService->user_id(),
        ]);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUpdateBidderSingleRecord($house_id) {
        Log::info("SaleDetailsController: createUpdateBidderSingleRecord called");

        $all       = $this->request->all();
        $saveItem[$all['name']]=$all['value'];
        $saveItem['sale_id']=$all['sale_id'];
        $saveItem['house_id']=$house_id;
        

        if(!empty($all['id'])){
            # first check record exists or not
            $info = $this->saleBidderService->findOneById($all['id']);
            if (empty($info)) {
                return response()->json(['message' => __("error_messages.record_not_exists"),'status' => 'failed'], 400);
            }
            $this->saleBidderService->update($all['id'], $saveItem);
            $bidder_id=$info->bidder_id;

        }else{
            $info = $this->saleBidderService->create($saveItem);
            $bidder_id=$info->bidder_id;

        }

        if($all['name']=='bidder_notes'){
            SaleBidderNotesModel::updateOrCreate(
                ["bidder_id"=> $bidder_id],
                [$all['name'] => @$all['value']]
            );
        }
       

        $sectionArray=['im_by'];
        if(in_array($all['name'],$sectionArray)){
            $this->updateSectionHistory($all['name'],$info,$all['value']);
        }

        $sectionArray=['im_date'];
        if(in_array($all['name'],$sectionArray)){
            $this->updateDateSectionHistory($all['name'],$info,$all['value']);
        }
        
        return response()->json(['data'    => $bidder_id,
                                  'status' => 'success',
                                 'message' => __("messages.record_saved")], 200);
    }

    public function bidderSoftDeleted($bidder_id)
    {
        $all=$this->request->all();
        if(!empty($bidder_id)){
                $bidderInfo = SaleBidderModel::find($bidder_id);
                if (!is_null($bidderInfo)) {
                    SectionHistoryModel::create([
                        "house_id" => $bidderInfo->house_id,
                        "foreign_id" =>$bidder_id,
                        'user_id' => $this->userService->user_id(),
                        'date_by' => date('Y-m-d'),
                        'section_type' => 'bidder_deleted',
                        'modify_by' => $this->userService->user_id()
                    ]);
                    $bidderInfo->delete();
                    return response()->json(['status'  => 'success','message' => __("messages.record_delete") , 'data'=>[]],200);
                } else {
                    return response()->json(['status'  => 'failed','message' => __("error_messages.record_not_exists"), 'data' => []], 200);
                }
            
        }
       
        
    }
}
