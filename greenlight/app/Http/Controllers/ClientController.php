<?php

namespace App\Http\Controllers;

use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Services\DocumentService;
use App\Services\PropertyService;
use App\Services\ClientService;
use App\Models\DocumentClientModel;
use App\Models\ClientMasterClosingDocModel;
use App\Models\ClientRenovationtModel;
use App\Http\Validations\ClientValidations;
use App\Helpers\CommonHelper;
use App\Services\WholesaleBuyerStrategyExtraService;
use App\Services\PropertyAcquisitionAtoBFirstService;
use App\Services\PropertyAcquisitionAtoBSecondService;
use App\Services\WholesaleBuyerStrategyServiceExtra;
use App\Services\WholesaleBuyerStrategyService;
use App\Services\McdService;

use App\Services\Form1099MiscService;
use Illuminate\Support\Facades\Storage;

use App\Models\CarryCostsModel;
use App\Models\DocumentRenovationCosts;
use App\Models\IncidentalCostsModel;
use App\Models\BurnRateModel;
use App\Models\ClientRenovationDetailModel;
use App\Models\MailingModel;
use App\Services\MailService;
use App\Services\UserService;
use App\Exports\CommonExport;
use Maatwebsite\Excel\Excel;

class ClientController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $documentService;
    private $propertyService;
    private $wholesaleBuyerStrategyExtraService;
    private $clientService;
    private $propertyAcquisitionAtoBFirstService;
    private $propertyAcquisitionAtoBSecondService;
    private $wholesaleBuyerStrategyService;

    private $form1099MiscService;
    private $mcdService;
    private $mailService;
    private $userService;

    private $excel;


    /**
     * @param DocumentService $documentService
     */

    public function __construct(Request $request

        , DocumentService $documentService
        , PropertyService $propertyService
        , ClientService $clientService
        , WholesaleBuyerStrategyExtraService $wholesaleBuyerStrategyExtraService
        , PropertyAcquisitionAtoBFirstService $propertyAcquisitionAtoBFirstService
        , PropertyAcquisitionAtoBSecondService $propertyAcquisitionAtoBSecondService
        , WholesaleBuyerStrategyService $wholesaleBuyerStrategyService
        , Form1099MiscService $form1099MiscService
        , McdService $mcdService
        , MailService $mailService       
        , UserService $userService
        , Excel $excel
    )
    {
        Log::info("ClientController: __construct called");
        $this->request                              = $request;
        $this->documentService                      = $documentService;
        $this->propertyService                      = $propertyService;
        $this->clientService                        = $clientService;
        $this->wholesaleBuyerStrategyExtraService   = $wholesaleBuyerStrategyExtraService;
        $this->propertyAcquisitionAtoBFirstService  = $propertyAcquisitionAtoBFirstService;
        $this->propertyAcquisitionAtoBSecondService = $propertyAcquisitionAtoBSecondService;
        $this->wholesaleBuyerStrategyService        = $wholesaleBuyerStrategyService;
        $this->form1099MiscService                  = $form1099MiscService;
        $this->mcdService                           = $mcdService;
        $this->mailService                          = $mailService;
        $this->userService                          = $userService;
        $this->excel                  = $excel;
    }

    public function index($house_id)
    {
        Log::info("ClientController: indexAll called");

        $infowbss                           = $this->wholesaleBuyerStrategyService->findAllInformation($house_id);
        $info['strategy']                   = $infowbss ? $infowbss->toArray() : [];
        $extra                              = $this->wholesaleBuyerStrategyExtraService->findOneById($house_id);
        $extra                              = $extra ? $extra->toArray() : [];
        $info['strategy']                   = array_merge($info['strategy'], $extra);

        $info_first                         = $this->propertyAcquisitionAtoBFirstService->findOneById($house_id);
        $info_second                        = $this->propertyAcquisitionAtoBSecondService->findOneById($house_id);
        $arr1                               = (array)json_decode($info_first);
        $arr2                               = (array)json_decode($info_second);
        $info['a_to_b']                     = array_merge($arr1, $arr2);

        $client_document                    = $this->clientService->getClientDocument($house_id);
        $info['client_document']            = (array)json_decode($client_document);

        $client_master_closing_doc          = $this->clientService->findClientMasterClosingDoc($house_id);

        $info['client_master_closing_doc']  = (array)json_decode($client_master_closing_doc);
        $info['client_master']              =  (array)json_decode($this->clientService->findClientMaster($house_id));
       // $info['sthb_total']                 =  (array)json_decode($$this->wholesaleBuyerNTotalService->findOneById($house_id));



        $info['form1099Misc']              =  (array)json_decode($this->form1099MiscService->findForm1099Misc($house_id));

        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    public function getClientMaster($house_id)
    {
        Log::info("ClientController: getClientMaster called");
        $info['client_master']=  (array)json_decode($this->clientService->findClientMaster($house_id));
        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }


    public function documentUpload()
    {
        #var_dump($this->request->all());die;
        Log::info("clientDocumentUpload: clientDocumentUpload called");

        try
        {

            ## check input validation
            Log::info("documentUpload: update validation check");
            $rules = ClientValidations::documentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('client_document');
            if ($doc_info != false)
            {
                $newCategory='';
                if(!is_numeric($doc_info['document_type'])){
                    $categoryName=$doc_info['document_type'];
                    $info=$this->clientService->saveUpdateRenovationCategory(['id'=>'','category_name'=>$categoryName]);
                    $doc_info['document_type']=$info->id;
                    $newCategory=['id'=>$info->id,'category_name'=>$categoryName];
                }

                $doc_info['house_id'] = $this->request->house_id;
                $doc = $this->documentService->client($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'data'   => ['url' => $doc_info['document_url'], 'id' => $doc->id],
                                         'category' => $newCategory
                                        ], 200);
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


    public function documentDelete($document_id)
    {
        Log::info("clientDocumentUpload: documentDelete called");

        try
        {
            $info = DocumentClientModel::find($document_id);
            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete")
                                         , 'data'=>[]],200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data'=>[]], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }


    public function masterClosingDoc()
    {
        #var_dump($this->request->all());die;
        Log::info("ClientController: ClientController called");

        try
        {

            ## check input validation
            Log::info("masterClosingDoc: update validation check");
            $rules = ClientValidations::masterClosingDocValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('client_master_closing_doc');
            if ($doc_info != false)
            {
                $doc_info['house_id'] = $this->request->house_id;
                $doc = $this->documentService->clientMasteeClosingDoc($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'data'   => ['url' => $doc_info['document_url'], 'id' => $doc->id]], 200);
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



    public function masterClosingDocDelete($document_id)
    {
        Log::info("clientDocumentUpload: masterClosingDocDelete called");

        try
        {
            $info = ClientMasterClosingDocModel::find($document_id);
            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                                         , 'data'=>[]],200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data'=>[]], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }


    public function clientMasterCreate()
    {
        #var_dump($this->request->all());die;
        Log::info("ClientController: clientMasterCreate called");

        try
        {

            ## check input validation
            Log::info("clientMasterCreate: update validation check");
            $rules = ClientValidations::clientMasterValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();


            # first check record exists or not
            $info = $this->propertyService->findOneById($this->request->input('house_id'));

            if (empty($info))
            {
                return response()->json(['message' => __("error_messages.house_id_exists")], 400);
            }

            # update information
            $all = $this->request->all();
            $all['created_at'] = time();
            $all['updated_at'] = time();
            $info = $this->clientService->createClientMasterInfo($all);

            return response()->json(['row' => ['house_id' => $info->house_id], 'message' => __("messages.record_saved")], 200);
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


    public function clientMasterUpdate($house_id)
    {
        #var_dump($this->request->all());die;
        Log::info("ClientController: clientMasterUpdate called");

        try
        {

            ## check input validation
            Log::info("clientMasterUpdate: update validation check");
            $rules = ClientValidations::clientMasterValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            # first check record exists or not
            $info = $this->propertyService->findOneById($house_id);

            if (empty($info))
            {
                return response()->json(['message' => __("error_messages.house_id_exists")], 400);
            }

            # update information
            $all = $this->request->all();
            $all['house_id'] = $house_id;
            $all['updated_at'] = time();
            $info = $this->clientService->updateClientMasterInfo($house_id, $all);

            return response()->json(['row' => ['house_id' => $info->house_id], 'message' => __("messages.record_saved")], 200);
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


    /** Client Renovation Section Start  */
    public function clientRenovation($house_id, $section_type,$orderBy)
    {

        Log::info("ClientController: clientRenovation called");

        $info['client_renovation'] = $this->clientService->findClientRenovation($house_id, $section_type,$orderBy);
        $info['lender_list'] = $this->mcdService->getMcdLenderlist($house_id);
        $info['lender_by_amount'] = $this->clientService->getLenderByAmount($house_id,$section_type);
        $info['recipients_info'] = $this->form1099MiscService->getRecipints();
        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

     /** Client Renovation Section Start  */
     public function clientRenovationLender($house_id, $section_type)
     {
 
         Log::info("ClientController: clientRenovationLender called");
         $info['lender_by_amount'] = $this->clientService->getLenderByAmount($house_id,$section_type);
         if (empty($info))
         {
             return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
         }
 
         return response()->json(['data' => $info], 200);
     }

    /** Client Renovation Section Start  */
    public function getClientNonHubExpenditures($house_id)
    {

        Log::info("ClientController: getClientNonHubExpenditures called");
        $info  = $this->clientService->findClientInvoiceExpenses($house_id);
        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }
        return response()->json(['data' => $info], 200);
    }


    public function clientRenovationCreate()
    {

        Log::info("clientController: clientRenovation called");

        try
        {
            ## check input validation
            Log::info("clientRenovation: update validation check");
            $all = $this->request->all();
            $all['amount']=floatval($all['amount']);
            $rules = ClientValidations::clientRenovationValidation();
            $validator = Validator::make($all, $rules);
            $validator->validate();

            #Information save
            $client_renovation = $all;
            $client_renovation['paid'] = floatval($client_renovation['paid']);
            if(empty($client_renovation['paid_date']) || $client_renovation['paid_date']=='null'){ 
                unset($client_renovation['paid_date']);
            }
            
            $renovationInfo = $this->clientService->clientRenovationCreate($client_renovation);
            $renovationDetailJson = json_decode($this->request->renovation_detail_data);

            $renovationDetailId=[];
            $subAmount=[];
            if(!empty($renovationDetailJson)){

                $renovationDetail=['house_id'=>$this->request->house_id,'invoice_id'=>$renovationInfo->id];

                foreach($renovationDetailJson as $renovation){
                    if(!empty($renovation->sub_category)){
                        #ToDo: check with non_hud_expenditures_sub_cat not belongs continue
                        $renovationDetail['sub_category']=$renovation->sub_category;
                        $renovationDetail['description']=$renovation->description;
                        $renovationDetail['amount']=$renovation->amount;
                        $renovationDetail['classification']=$renovation->classification;
                        $renovationDetail['id']= ''; //$renovation->id;
                        $insert = $this->clientService->clientRenovationDetailCreate($renovationDetail);
                        $this->store1099Form($renovation->msc_form_id??'',$insert->id,$renovation);
                    }
                }

            }
            $result=$this->clientService->findClientRenovationById($renovationInfo->id);
            $info['lender_by_amount']  = $this->clientService->getLenderByAmount($result->house_id,$result->section_type,$result->funder);
            $info['client_renovation'] = $result;
            return response()->json(['message' => __("messages.record_saved") , 'data' => $info], 200);

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function store1099Form($id,$renovationId,$renovation){
        if(!empty($renovation->recipients_id)){
            $dataArray=['house_id'=>$this->request->house_id,'recipients_id'=>$renovation->recipients_id,
                        'client_renovation_detail_id'=>$renovationId,'added_by'=>$this->request->auth->id,'form_year'=>date('Y')];
            $this->form1099MiscService->storeForm1099Misc($id,$dataArray);
        }
        
    }

    public function clientRenovationUpdate()
    {
        Log::info("clientController: clientRenovationUpdate called");
        try
        {
            $id = $this->request->input('id');
            $all = $this->request->all();
            $all['amount']=floatval($all['amount']);
            ## check input validation
            Log::info("clientRenovation: update validation check");
            $rules = ClientValidations::clientRenovationUpdateValidation();
            $validator = Validator::make($all, $rules);
            $validator->validate();

           // $renovationInfo = ClientRenovationtModel::find($id);
            #Information save
            $url = [];
            $client_renovation = $all;
            $client_renovation['paid'] = floatval($client_renovation['paid']);
            if(empty($client_renovation['paid_date']) || $client_renovation['paid_date']=='null'){ 
                unset($client_renovation['paid_date']);
            }
            $response = $this->clientService->clientRenovationUpdate($id, $client_renovation);

            $renovationDetailJson = json_decode($this->request->renovation_detail_data);
            $renovationDetailId=[];
            if(!empty($renovationDetailJson)){
                $renovationDetail=['house_id'=>$this->request->house_id,'invoice_id'=>$id];

                foreach($renovationDetailJson as $renovation){
                    if(!empty($renovation->sub_category)){
                        $renovationDetail['sub_category']=$renovation->sub_category;
                        $renovationDetail['description']=$renovation->description;
                        $renovationDetail['amount']=$renovation->amount;
                        $renovationDetail['id']= @$renovation->id;
                        $renovationDetail['classification']=$renovation->classification;
                        $insert = $this->clientService->clientRenovationDetailUpdateOrCreate($renovationDetail);
                        $this->store1099Form($renovation->msc_form_id??'',$insert->id,$renovation);
                    }
                }
            }
            $result=$this->clientService->findClientRenovationById($id);
            $info['lender_by_amount']  = $this->clientService->getLenderByAmount($result->house_id,$result->section_type,$result->funder);
            $info['client_renovation'] = $result;
            return response()->json(['message' => __("messages.record_modified") , 'data' => $info], 200);


        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    function createRicCost($renovationDetail){

        if($renovationDetail['classification']=='renovation'){
          $renovation=array();
          $renovation['sub_categories']=$renovationDetail['sub_category'];
          $renovation['description']=$renovationDetail['description'];
          $renovation['house_id']=$renovationDetail['house_id'];
          $renovation['amount']=$renovationDetail['amount'];
          $renovation['house_id']=$renovationDetail['house_id'];
          $renovation['added_by']     = $this->request->auth->id;
          $info = DocumentRenovationCosts::create($renovation);
        }else{
            $ricCost=array();
          $ricCost['categories']=$renovationDetail['sub_category'];
          $ricCost['title']=$renovationDetail['description'];
          $ricCost['house_id']=$renovationDetail['house_id'];
          $ricCost['cost']=$renovationDetail['amount'];
          $ricCost['monthly']=$renovationDetail['amount'];
          $ricCost['weekly']=round($renovationDetail['amount']/4,2);
          $ricCost['daily']=round($renovationDetail['amount']/30,2);
          if($renovationDetail['classification']=='carry'){
            $info = CarryCostsModel::updateOrCreate(['id'=>@$ricCost['id']],$ricCost);
          }else{
            $info = IncidentalCostsModel::updateOrCreate(['id'=>@$ricCost['id']],$ricCost);
          }
        }
    }

    public function clientRenovationDelete($id)
    {
        Log::info("clientDocumentUpload: clientRenovationDelete called");

        try
        {
            $info = ClientRenovationtModel::find($id);
            if ($info == true)
            {
                ClientRenovationDetailModel::where('invoice_id',$id)->delete();
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'status'=>'success', 'data'=>[]],200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data'=>[]], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

   
    /** Client Renovation Section Start  */



    public function clienDocumentGet($house_id)
    {
        Log::info("ClientController: clienDocumentGet called");

        $client_document                    = $this->clientService->getClientDocument($house_id);
        $info['client_document']            = (array)json_decode($client_document);

        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    public function clientMasterClosingdocGet($house_id)
    {
        Log::info("ClientController: clientMasterClosingdocGet called");

        $client_master_closing_doc          = $this->clientService->findClientMasterClosingDoc($house_id);
        $info['client_master_closing_doc']  = (array)json_decode($client_master_closing_doc);

        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }



    public function clientInformationCreate()
    {
        Log::info("ClientController: clientInformationCreate called");

        try
        {

            Log::info("ClientValidations: update validation check");
            $rules = ClientValidations::clientInformationValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();


            $info = $this->request->all();

            $info['house_id'] = $this->request->house_id;



            $clientInformation = $this->clientService->createClientInformation($info);

            if($this->request->member){
                if(count($info['member']) > 0){
                    foreach ($info['member'] as $key => $value)
                    {

                        $member = ['client_information_id' => $clientInformation['id'],
                                    'member' => $value['member_name'],
                                    'interest' => $value['interest'],
                                    'id'=>$value['id']];
                        $return_info = $this->clientService->createUpdateClientInfoMember($member);
                    }
                }
            }

            if($this->request->manager){
                if(count($info['manager']) > 0){

                    foreach ($info['manager'] as $key => $value)
                    {
                            $manager = [
                                        'client_information_id' => $clientInformation['id'],
                                        'manager' => $value['manager_name'],
                                        'email_id' =>$value['email_id'],
                                        'id'=>$value['id']];
                        $return_info = $this->clientService->createUpdateClientInfoManager($manager);
                    }
                }
            }

            return response()->json(['row' => ['house_id' => $clientInformation->house_id], 'message' => __("messages.record_saved")], 200);


        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


    public function clientInformationGet($house_id)
    {
        Log::info("ClientController: clientInformationGet called");

        $client_information=$this->clientService->findClinetInformation($house_id);
        if (empty($client_information))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $client_information], 200);
    }


    public function removeClientMember($id){
        $this->clientService->removeClientMember($id);
        return response()->json(['message' => __("messages.record_delete") , 'data'=>[]],200);
    }

    public function removeClientManager($id){
        $this->clientService->removeClientManager($id);
        return response()->json(['message' => __("messages.record_delete"), 'data'=>[]],200);
    }

    public function saveWholesaleBuyer(){

        Log::info("SaleDetailsController: create called");

        ## check input validation
        Log::info("SaleDetailsController: create validation check");
        $rules = [
            'house_id'  => 'required|numeric|exists:home_information,house_id',
            'user_id'   => 'nullable|numeric',
        ];
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # create information
        $info = $this->clientService->saveClient($this->request->all());

        return response()->json(['data' => [$info], 'message' => __("messages.record_saved")], 200);

    }

    public function saveLender(){

        Log::info("SaleDetailsController: create called");

        ## check input validation
        Log::info("SaleDetailsController: create validation check");
        $rules = [
            'house_id'  => 'required|numeric|exists:home_information,house_id',
            'user_id'   => 'nullable|numeric|unique:property_lender,user_id,null,null,house_id,'.$this->request->house_id
          ];
        $messages = [
            'unique' => 'The lender has already been taken.',
        ];
        $validator = Validator::make($this->request->all(), $rules,$messages);
        $validator->validate();

        # create information
        $info = $this->clientService->saveLender($this->request->all());

        return response()->json(['data' => [$info], 'message' => __("messages.record_saved")], 200);

    }

    public function wholesaleBuyer($house_id){

        $result = $this->clientService->findWholesaleBuyer($house_id);
        if (empty($result))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $result], 200);
    }

    public function lender($house_id){

        $result = $this->clientService->findFunderLender($house_id);
        if (empty($result))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $result], 200);
    }

    public function getRenovationCategory(){
        $result = $this->clientService->getRenovationCategory();
        if (empty($result))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data'=>$result,'status'=>'success'], 200);

    }

    public function saveRenovationCategory(){

        Log::info("saveRenovationCategory: create called");

        ## check input validation
        Log::info("saveRenovationCategory: create validation check");
        $rules = ['category_name'  => 'required'];
        $messages = ['unique' => 'Category Name is required.'];
        $validator = Validator::make($this->request->all(), $rules,$messages);
        $validator->validate();

        # create information
        $info = $this->clientService->saveUpdateRenovationCategory($this->request->all());

        return response()->json(['data' => [$info], 'message' => __("messages.record_saved")], 200);

    }
    public function removeRenovationCategory($id){
        $this->clientService->removeRenovationCategory($id);
        return response()->json(['status'=>'success','message' => __("messages.record_delete") , 'data'=>[]],200);
    }


  /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCreateBurnRate($house_id)
    {
        Log::info("McdController: updateCreateBurnRate called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        $info=$this->clientService->updateCreateBurnRate($id, $all);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBurnRate($house_id)
    {
        Log::info("McdController: index called");
        $info = $this->clientService->getBurnRateByHouseId($house_id);
        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

     /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyBurnRate($id)
    {

        Log::info("McdController: mcd called destroyBurnRate");
        try
        {
            $info = BurnRateModel::find($id);
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
    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function burnRateRecord()
    {
        Log::info("PropertyController: update called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $all['house_id'];
        $saveData[$all['name']] = $all['value'];

        if($all['name']=='actual_months'){
            $id = $all['id'];
            $info=$this->clientService->updateCreateBurnRate($id,$saveData);
        }else{
            $info=$this->clientService->updateOrCreateBurnRate($all['house_id'], $saveData);
        }

        return response()->json(['row' => __("messages.record_saved"),'data'=>$info->id,'status'=>'success'], 200);
    }


     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActualBurnRate()
    {
        Log::info("PropertyController: update called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $all['house_id'];
        $saveData['category_id'] = $all['category_id'];
        $saveData[$all['name']] = $all['value'];

        $info=$this->clientService->updateCreateActualBurnRate($saveData);

        return response()->json(['row' => __("messages.record_saved"),'data'=>$info,'status'=>'success'], 200);
    }

    public function getRenoCategory($house_id)
    {
        Log::info("clientController: index called");
        $info = $this->clientService->getRenoHomeBuyerCategory($house_id);
        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

    public function postMailing(){
        Log::info("postMailing: index called");
        $all = $this->request->all();

        $house_id=$all['house_id'];
        $mailing_info_data=$all['mailing_info_data'];
        foreach($mailing_info_data as $mailing){
            $mailing['house_id']=$house_id;
            $this->clientService->createUpdateMailing($mailing['id'],$mailing);
        }
        $info=$this->clientService->getMailingByHouseId($house_id);

        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data' => $info], 200);
    }


    public function getMailing($house_id)
    {
        Log::info("clientController: getMailing called");
        $info = $this->clientService->getMailingByHouseId($house_id);
        if (empty($info))
        {
            return response()->json(['data'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data' => $info], 200);
    }

    public function sendMailToUser($house_id)
    {
        Log::info("clientController: sendMailToUser called");
        $resultInfo = $this->clientService->sendEmailToUserByHouseId($house_id);
        if (empty($resultInfo))
        {
            return response()->json(['data'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }
        $data['subject']="MCD Detail: ".$resultInfo->county.' '.$resultInfo->address;
        $data['property_url'] = env("APP_FRONTEND") . 'home/showdetail/' . $house_id . '/' . CommonHelper::url_slug($resultInfo).'/accounting';
        foreach($resultInfo->mailing_list as $result){
            $data['email']=$result->email_id;
            $this->mailService->sendMcdEmailToUser($data);
        }
        return response()->json(['status' => 'success','message' => __("messages.email_send"),'data' => ''], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMailing($id)
    {
        Log::info("McdController: mcd called destroyMailing");
        try
        {
            $info = MailingModel::find($id);
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


     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInvoiceHistory()
    {
        Log::info("clientController: updateInvoiceHistory called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $all['house_id'];
        $saveData['user_id'] = $this->userService->user_id();
        $saveData['invoice_id'] = $all['invoice_id'];
        //$saveData['created_at'] = date('Y-m-d H:i:s');
        //$saveData['updated_at'] = date('Y-m-d H:i:s');

        $info=$this->clientService->updateCreateInvoiceHistory($saveData);

        return response()->json(['message' => __("messages.invoice_read"),'data'=>$info,'status'=>'success'], 200);
    }
      /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportClientRenovation()
    {
        Log::info("McdController: index called");
        $house_id=$this->request->post('house_id'); 
        $section_type=$this->request->post('section_type'); 

        $client_renovation= $this->clientService->findClientRenovation($house_id, $section_type,'DESC');

        $header   = [];
        	#Invoice	 			 						
        $header[] = "#Invoice";
        $header[] = 'Invoice Date';
        $header[] = 'Invoice Amount';
        $header[] = 'Sub Category';
        $header[] = 'Total Amount';
        $header[] = 'Funder';
        $header[] = 'Invoice Lock';
        // $header[] = 'Inv Read';
        $header[] = 'Bank Deposit';
        $header[] = 'Bank Statement';
        $header[] = 'Invoice Url';
        $temp[]   = $header;
       
        foreach ($client_renovation as $key => $value) {
            $parse_data                         = [];
            $parse_data['invoice_id']           = CommonHelper::emptyDefault($value->id);
            $parse_data['invoice_date']         = CommonHelper::emptyDefaultObject(@$value, 'invoice_date', 'date');
            $parse_data['amount']               = CommonHelper::emptyDefaultObject(@$value, 'amount', 'currency');
            $parse_data['sub_category']         = $this->getInvoiceItems($value->renovation_detail);
            $parse_data['total_amount']         = CommonHelper::emptyDefaultObject(@$value->total_renovation_amount, 'total_amount', 'currency');
            $parse_data['lender_name']          = CommonHelper::emptyDefaultObject(@$value->funder_info, 'lender_name', '');
            $parse_data['invoice_lock']         = $value->invoice_lock?'Yes':'No';
            //$parse_data['invoice_history']      = '';
            $parse_data['bank_deposit_url']     = CommonHelper::emptyDefault(@$value->bank_deposit_url);
            $parse_data['bank_statement_url']   = CommonHelper::emptyDefault(@$value->bank_statement_url);
            $parse_data['invoice_url']          = CommonHelper::emptyDefault(@$value->invoice_url);
            $temp[]                             = $parse_data;
        }

        $export = new CommonExport($temp);
        #ToDo: temporary fix, deleteFileAfterSend: false creating a lot of temp files, Please fix this.
        $info = $this->excel->download($export, 'ES_' . time() . '.xlsx')->deleteFileAfterSend(false);
        return $info;
    }

    function getInvoiceItems($renovationItems){
        $result='';
        if(!empty($renovationItems)){
            foreach($renovationItems as $items){
               $result.=$items->category->category_name.': '.$items->amount."\n";
            }
        }
        return substr($result,0,-2);
    }

}
