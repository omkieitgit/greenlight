<?php

namespace App\Http\Controllers;


use App\Models\AssessmentModel;
use App\Models\DocumentPropertyModel;
use App\Models\PriceHistoryModel;
use App\Models\PropertyModel;
use App\Models\SchoolNeighborhoodModel;
use App\Services\AssessmentService;
use App\Services\LocalRealEstateService;
use App\Services\PriceHistoryService;
use App\Services\PropertyDescriptionsService;
use App\Services\SchoolNeighborhoodService;
use App\Services\UserFavoritesService;
use Illuminate\Validation\Rule;
use Nexmo\Account\Price;
use Validator;
Use Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\CommonHelper;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use App\Http\Validations\PropertyValidations;
use App\Http\Validations\CommonValidations;
use App\Services\DocumentService;
use DB;
use App\Models\SectionHistoryModel;
use App\Services\UserService;
use App\Services\McdService;
use App\Services\ClientService;

use App\Exports\CommonExport;
use App\Exports\PropertyExport;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Excel;

class PropertyController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $validations;
    private $propertyService;
    private $propertyDescriptionsService;
    private $assessmentService;
    private $localRealEstateService;
    private $priceHistoryService;
    private $schoolNeighborhoodService;
    private $documentService;
    private $userFavoritesService;
    private $userService;
    private $clientService;
    private $excel;


    /**
     * PropertyController constructor.
     * @param Request $request
     * @param PropertyService $propertyService
     * @param PropertyDescriptionsService $propertyDescriptionsService
     * @param AssessmentService $assessmentService
     * @param LocalRealEstateService $localRealEstateService
     * @param PriceHistoryService $priceHistoryService
     * @param SchoolNeighborhoodService $schoolNeighborhoodService
     */
    public function __construct(Request $request
        , PropertyService $propertyService
        , PropertyDescriptionsService $propertyDescriptionsService
        , AssessmentService $assessmentService
        , LocalRealEstateService $localRealEstateService
        , PriceHistoryService $priceHistoryService
        , SchoolNeighborhoodService $schoolNeighborhoodService
        , DocumentService $documentService
        , UserFavoritesService $userFavoritesService
        , UserService $userService
        , McdService $mcdService
        , ClientService $clientService
        , Excel $excel
    )
    {
        Log::info("PropertyController: __construct called");
        $this->request                     = $request;
        $this->propertyService             = $propertyService;
        $this->propertyDescriptionsService = $propertyDescriptionsService;
        $this->assessmentService           = $assessmentService;
        $this->localRealEstateService      = $localRealEstateService;
        $this->priceHistoryService         = $priceHistoryService;
        $this->schoolNeighborhoodService   = $schoolNeighborhoodService;
        $this->documentService             = $documentService;
        $this->userFavoritesService        = $userFavoritesService;
        $this->userService                 = $userService;
        $this->mcdService                  = $mcdService;
        $this->clientService               = $clientService;  
        $this->excel                       = $excel;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {
        Log::info("PropertyController: index called");


        $info = $this->propertyService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['row'=>[],'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAll($house_id)
    {
        Log::info("PropertyController: indexAll called");
        $info                                       = [];
        $info['property_info']                      = $this->propertyService->findPropertyInformation($house_id);
        #$info['property_info']['document_property'] = $this->documentService->getPropertyDocument($house_id);
        $info['isFavourite'] = $this->userFavoritesService->isFavourite($house_id);
        
        if (empty($info['property_info']))
        {
            return response()->json(['row'=>[],'message' => __("error_messages.record_not_exists")], 200);
        }

        $headers = ['Content-Type' => 'application/json; charset=UTF-8'];
        return response()->json(['row' => $info], 200, $headers, JSON_INVALID_UTF8_SUBSTITUTE);

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($house_id)
    {

        Log::info("PropertyController: update called");

        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        # if record exists update information
        ## check input validation
        Log::info("PropertyController: update validation check");
        $rules = PropertyValidations::createEditPropertyValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $all = $this->request->all();
        //$all['county_value'] = CommonHelper::dbNumberFormat(@$all['county_value']);
        $this->propertyService->updateProperty($house_id, $all);
        $this->propertyDescriptionsService->updateOrCreate($house_id, $all);

        return response()->json(['row' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        Log::info("PropertyController: update called");

        # first check record exists or not
       $propertyObj = $this->propertyService->propertyAddress($this->request->input('address'),$this->request->input('state'),$this->request->input('county'));
       if($propertyObj->count()){
           return response()->json(['status'=>'error','message' => __("error_messages.record_exist")], 400);
       }

        # if record exists update information
        ## check input validation
        Log::info("PropertyController: update validation check");
        $rules = PropertyValidations::createEditPropertyValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $house_id=$this->propertyService->createProperty($this->request->all());
        $this->propertyDescriptionsService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['row'=>['house_id'=>$house_id],'message' => __("messages.record_saved")], 200);

    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assessment($house_id)
    {
        Log::info("PropertyController: assessment called");

        $info = $this->assessmentService->findAllByHouseId($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function assessmentCreate()
    {
        Log::info("PropertyController: assessmentCreate called");

        ## check input validation
        Log::info("PropertyController: update validation check");
        $rules = PropertyValidations::assessmentCreateValidation();


        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->assessmentService->createAssessment($this->request->all());

        return response()->json(['row' => ['id' => $info->id], 'message' => __("messages.record_saved")], 200);
    }


    public function assessmentUpdate($id)
    {
        Log::info("PropertyController: assessmentUpdate called");

        # first check record exists or not
        $info = $this->assessmentService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("PropertyController: update validation check");
        $rules = PropertyValidations::assessmentUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->assessmentService->updateAssessment($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }


    public function assessmentUpdateOrCreateAll()
    {
        Log::info("PropertyController: assessmentUpdateOrCreateAll called");
        $messages = [
            'json.*.house_id.required'    => 'house_id field is required.',
            'numeric'    => ':input must be a number.',
            'max'    => 'The :input may not be greater than :max.',
            'json.*.house_id.exists'    => 'The selected house_id is invalid.',
            'json.*.id.exists'    => 'Invalid record :input.',
        ];
        $all = $this->request->all();
        $rules = PropertyValidations::assessmentUpdateOrCreateAllValidation();
        $validator = Validator::make($all, $rules, $messages);
        $validator->validate();
        $house_id  = '';
        foreach ($all['json'] as $key=>$value)
        {
            $house_id  = $value['house_id'];
            if(empty(@$value['id']))
            {
                $this->assessmentService->createAssessment($value);
            }
            else{
                $this->assessmentService->update($value);
            }
        }

        # update information
        $info = $this->assessmentService->findAllByHouseId($house_id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['data' => $info], 200);
    }


    public function assessmentDelete($id)
    {
        Log::info("PropertyController: assessmentDelete called");

        try
        {
            $info = AssessmentModel::find($id);

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

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function localReal($house_id)
    {
        Log::info("PropertyController: localReal called");


        $info = $this->localRealEstateService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'row' => []], 200);
        }

        return response()->json(['row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function localRealUpdate($house_id)
    {
        Log::info("PropertyController: localRealUpdate called");

        ## check input validation
        Log::info("PropertyController: localRealUpdate update validation check");
        $rules = PropertyValidations::localRealEstateValidation();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $this->localRealEstateService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function priceHistory($house_id)
    {
        Log::info("PropertyController: priceHistory called");

        $info = $this->priceHistoryService->findAllByHouseId($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function priceHistoryCreate()
    {
        Log::info("PropertyController: priceHistoryCreate called");

        ## check input validation
        Log::info("PropertyController: update validation check");
        $rules = PropertyValidations::priceHistoryCreateValidation();


        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->priceHistoryService->createPriceHistory($this->request->all());

        return response()->json(['row' => ['id' => $info->id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function priceHistoryUpdate($id)
    {
        Log::info("PropertyController: priceHistoryUpdate called");

        # first check record exists or not
        $info = $this->priceHistoryService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("PropertyController: update validation check");
        $rules = PropertyValidations::priceHistoryUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->priceHistoryService->updatepriceHistory($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    public function priceHistoryDelete($id)
    {
        Log::info("PropertyController: priceHistoryDelete called");

        try
        {
            $info = PriceHistoryModel::find($id);

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


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function school($house_id)
    {
        Log::info("PropertyController: school called");


        $info = $this->schoolNeighborhoodService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'row' => []], 200);
        }

        return response()->json(['row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function schoolUpdate($house_id)
    {
        Log::info("PropertyController: schoolUpdate called");

        ## check input validation
        Log::info("PropertyController: schoolUpdate update validation check");
        $rules = PropertyValidations::schoolNeighborhoodValidation();

        $all             = $this->request->all();
        $all['house_id'] = $all;
        $validator       = Validator::make($this->request->all(), $rules);
        $validator->validate();

        $this->schoolNeighborhoodService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function propertyDocumentUpload()
    {
        #var_dump($this->request->all());die;
        Log::info("propertyDocumentUpload: propertyDocumentUpload called");

        try
        {
            ## check input validation
            Log::info("documentUpload: update validation check");
            $rules = commonValidations::documentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_property');
            if ($doc_info != false)
            {
                $doc = $this->documentService->property($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'row'   => ['url' => $doc_info['document_url'], 'id' => $doc->id]], 200);
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

    public function propertyDocumentDelete($document_id)
    {
        Log::info("propertyDocumentUpload: propertyDocumentDelete called");

        try
        {
            $info = DocumentPropertyModel::find($document_id);

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


    public function propertyCommonInfo($house_id)
    {
        Log::info("propertyDocumentUpload: propertyCommonInfo called");
        $info = PropertyModel::where('house_id',$house_id)->select(
            ['house_id','total_living_sqft','bed','bath','year_built','lot_acreage_sf','stories','cost_per_sqft','address','city','county','state','zip','county_value','property_type','specific_property_type','drive_list_link',"legal_summary_report"]
        )->with([
            "last_cma_arv_recommendations"        => function ($query) {
                $query->select(['house_id',
                    'recommended_cma_arv',
                ]);
            },
            "local_real_estate_details",
            "geo",
            "subto_property"=> function ($query) {
                $query->select(['house_id',
                    'sub_to_property',
                ]);
            },
            "mapVideo"=> function ($query) {
                $query->select(['house_id',
                    'google_map_url',
                ]);
            },
        ])->get()->first();
        if($info){
            $info->recommended_cma_arv = @$info->last_cma_arv_recommendations->recommended_cma_arv;
            $info->zestimates = @$info->local_real_estate_details->zestimates;
            $info->zestimate = @$info->local_real_estate_details->zestimate;

            if(!empty($info->recommended_cma_arv) && !empty((int)$info->total_living_sqft)){
            $info->cost_per_sqft = round(@$info->recommended_cma_arv/$info->total_living_sqft,2);
            }
            else{
            $info->cost_per_sqft =0;
            }
            if(!empty($info->property_type) && $info->property_type==2){
                $info->calculate_pvalue=$info->lot_acreage_sf;
            }else{
                $info->calculate_pvalue=$info->total_living_sqft;
            }

            $info->makeHidden('last_cma_arv_recommendations');
            $info->makeHidden('local_real_estate_details');
        }

        return response()->json(['row' => $info], 200);

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSingleRecord($house_id)
    {

        Log::info("PropertyController: update called");

        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        # if record exists update information
        ## check input validation
        Log::info("PropertyController: update validation check");
        // $rules = PropertyValidations::createEditPropertyValidation();

        // $validator = Validator::make($this->request->all(), $rules);
        // $validator->validate();

        # update information
        $all = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];

        if($all['name']=='pick_from'){
            $saveData['tax_sale']=1;
        }

        //$saveData['county_value'] = CommonHelper::dbNumberFormat(@$saveData['county_value']);
        $this->propertyService->updateProperty($house_id, $saveData);

        $this->propertyDescriptionsService->updateOrCreate($house_id, $saveData);

        $this->localRealEstateService->updateOrCreate($house_id, $saveData);

        $this->schoolNeighborhoodService->updateOrCreate($house_id, $saveData);


        return response()->json(['row' => __("messages.record_saved")], 200);
    }
  

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePriceHistoryRecord($house_id)
    {

        Log::info("PropertyController: update called");

        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        
        if($all['name']=='price'){
            $saveData['cost_per_sqft'] = $all['cost_per_sqft'];
        }
        $info=$this->priceHistoryService->updateOrCreate($all['id'], $saveData);

        return response()->json(['row' => __("messages.record_saved"),'data'=>$info->id], 200);
    }


    public function updateSingleAssessmentRecord($house_id)
    {
        
        $all = $this->request->all();
        $value=[];
        $value['house_id']  = $house_id;
        $value[$all['name']] = $all['value'];
        $value['id']  = $all['id'];

        if(empty(@$all['id']))
        {
            $result=$this->assessmentService->createAssessment($value);
            $info=$result['id'];
        }
        else{
            $this->assessmentService->update($value);
            $info=$value['id'];
        }
        # update information
        //$info = $this->assessmentService->findAllByHouseId($house_id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['row' => __("messages.record_saved"),'data' => $info], 200);

    }

    public function getSubToInfo($house_id)
    {
        Log::info("propertyDocumentUpload: propertyCommonInfo called");
        $info = PropertyModel::where('house_id',$house_id)->select(
            ['house_id','total_living_sqft','bed','bath','year_built','lot_acreage_sf','stories','cost_per_sqft','address','city','county','state','zip','county_value']
        )->with([
            "last_cma_arv_recommendations" => function ($query) {
                $query->select(['house_id', 'recommended_cma_arv']);
            },
            "last_sale_details"=> function($query) {
                $query->select(['house_id','sale_id','sale_date','opening_bid','sale_type','trustee','trustee_file_no'])->whereNotNull('sale_date')->orderBy('sale_id','desc');
            },
            "last_sale_details.last_bidder"=>function($query){
                $query->select(['house_id','sale_id','amount_of_bid as winning_bid']);
            },
            "first_liens"=> function ($query) {$query->select(['house_id','amortization_monthly_payment','amortization_loan_estimate_balance','total_est_debt','es_excess_funds','lien_amount','loan_term','total_est_debt']);},
            "second_liens"=> function ($query) {$query->select(['house_id','amortization_monthly_payment','amortization_loan_estimate_balance','total_est_debt','es_excess_funds','lien_amount','loan_term','total_est_debt']);},
            "third_liens"=> function ($query) {$query->select(['house_id','amortization_monthly_payment','amortization_loan_estimate_balance','total_est_debt','es_excess_funds','lien_amount','loan_term','total_est_debt']);},
            "hoa_liens"=> function ($query) {$query->select(['house_id','hoa_name','hoa_lien_amount','date_of_hoa_lien','redemption_expires','total_debt','trdeed_date']);},
            "owner_info" => function ($query) {
                $query->select(['house_id','full_name','full_address','phone']);
            },
            "assessment"=>function ($query){
                $query->select(['house_id',DB::raw("SUM(property_taxes_owed) as total_property_taxes_owed")]);
            }
        ])->get()->first();

        $info->recommended_cma_arv = @$info->last_cma_arv_recommendations->recommended_cma_arv;
        $info->property_link = CommonHelper::showdetail_url($info->house_id, $info);

        if(!empty($info->recommended_cma_arv) && !empty((int)$info->total_living_sqft)){
          $info->cost_per_sqft = round(@$info->recommended_cma_arv/$info->total_living_sqft,2);
        }
        else{
          $info->cost_per_sqft =0;
        }
        $info->makeHidden('last_cma_arv_recommendations');
        $info->makeHidden('local_real_estate_details');

        return response()->json(['row' => $info], 200);

    }

    public function propertySoftDeleted()
    {
        $all=$this->request->all();
        $house_ids=$all['house_ids'];
        if(!empty($house_ids)){
            foreach($house_ids as $house_id){
                $property = PropertyModel::find($house_id);
                if (!is_null($property)) {
                    SectionHistoryModel::create([
                        "house_id" => $house_id,
                        "foreign_id" => $house_id,
                        'user_id' => $this->userService->user_id(),
                        'date_by' => date('Y-m-d'),
                        'section_type' => 'property_deleted',
                        'modify_by' => $this->userService->user_id()
                    ]);
                    $property->delete();
                    return response()->json(['status'  => 'success','message' => __("messages.record_delete") , 'data'=>[]],200);
                } else {
                    return response()->json(['status'  => 'failed','message' => __("error_messages.record_not_exists"), 'data' => []], 200);
                }
            }
        }
    }


    function mergeProperty(){
        $all=$this->request->all();
        $primary_id =  $all['primaryPropertyId']??'';
        $merge_house_id = $all['house_ids'];

        if (empty($primary_id)) {
            return response()->json(['status'  => 'error','message' => 'Please select Primary house id' , 'data'=>[]],200);
        }
        if (empty($merge_house_id)) {
            return response()->json(['status'  => 'error','message' => 'Please select merge house ids.' , 'data'=>[]],200);
        }
        // // Search if primary house id already slected in merge id than remove it 
         $pos = array_search($primary_id, $merge_house_id);
        // // Remove from array
         if ($pos != false)
             unset($merge_house_id[$pos]);

        $merge1_data = $this->propertyService->get_merge_data($primary_id);
        $merge1_data_filter = array_filter($merge1_data);
        $merge2_delete_data = $this->propertyService->get_merge2_delete_data($merge_house_id);
        $total_merge2_delete_data = count($merge2_delete_data);

        $merge_record_all_th = '';
        $merge_array = array();
        for ($i = 0; $i < $total_merge2_delete_data; $i++) {
            $merge_record_all_th .= '<th>Merge Data ' . ($i + 2) . '</th>';
            $merge_array = array_merge(array_filter($merge2_delete_data[$i]), $merge_array);
        }
        $merge_array = array_merge($merge_array, array_filter($merge1_data));
        $mergeFinalData=[];
        foreach ($merge2_delete_data as $key => $value) {

            $merge2_delete_data_filter = array_filter($value);
            $match_result_array = array_intersect_assoc($merge1_data_filter, $merge2_delete_data_filter);
            $diff_result_array = array_diff_assoc($merge1_data_filter, $merge2_delete_data_filter);
            
            $match_data['primary_house_id'] =$primary_id;
            $match_data['primary_house_count'] =count($merge1_data_filter);
            $match_data['merge_house_id']=$value['house_id'];
            $match_data['merge_house_count']=count($merge2_delete_data_filter);
            $match_data['merge_data']=count($match_result_array);
            $match_data['diff_data']= count($diff_result_array);
            array_push($mergeFinalData,$match_data);
        }
        return response()->json(['status'  => 'success','message' => __("messages.record_saved") , 'data'=>$mergeFinalData],200);
    }

    function mergeRecords(){
        $all=$this->request->all();
        $merge_house_data =  $all['merge_property_data'];
        $house_ids=[];
        foreach($merge_house_data as $mergeId){
            array_push($house_ids,$mergeId['merge_house_id']);
            $this->storeHistoryInfo($mergeId['primary_house_id'],$mergeId['merge_house_id'],'property_merged');
        }
        $info=$this->propertyService->merge_property_records($house_ids);
        return response()->json(['status'  => 'success','message' => __("messages.record_saved") , 'data'=>[$info]],200);

    }

    function storeHistoryInfo($primaryId,$houseId,$sectionType){
        SectionHistoryModel::create([
            "house_id" => $houseId,
            "foreign_id" => $primaryId,
            'user_id' => $this->userService->user_id(),
            'date_by' => date('Y-m-d'),
            'section_type' =>$sectionType,
            'modify_by' => $this->userService->user_id()
        ]);
    }

    function getClientPayersInfo($house_id){
        $info['lender_list']=$this->mcdService->getLenderFund($house_id);
        $info['client_info']=$this->mcdService->getOtherInfo($house_id);
        $info['burnrate_info']=$this->clientService->burnRateSummeryByHouseId($house_id);
        $info['estimate_info']=$this->clientService->estimateRateSummeryByHouseId($house_id);
        $info['deed_info']=$this->mcdService->getDeed($house_id);

        return response()->json(['status'  => 'success','message' => __("messages.record_saved") , 'data'=>$info],200);

    }

   
}
