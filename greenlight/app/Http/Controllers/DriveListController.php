<?php
namespace App\Http\Controllers;

use Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Services\DriveListService;
use App\Services\PropertyService;
use App\Services\UserService;
use App\Http\Validations\UserFavoritesValidations;
use App\Models\DriveListDetailModel;
use App\Models\UserDriveListModel;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Storage;

class DriveListController extends Controller
{

    private $driveListService;
    private $userDriveListService;
    private $userService;
    private $disk;

    public function __construct(Request $request, 
                                DriveListService $driveListService,
                                PropertyService $propertyService,
                                UserService $userService) {
        Log::info("DriveListController: __construct called");
        $this->request                = $request;
        $this->driveListService       = $driveListService;
        $this->propertyService        = $propertyService;
        $this->userService            = $userService;
        $this->disk = Storage::disk('public');
    }


    function storeDriveList($house_id){

        Log::info("DriveListController: storeDriveList called");

        ## check input validation
        Log::info("DriveListController: update validation check");
        
        $rules = ['name'=>'required','value'=>'required'];

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);

        if (empty($info)) {
            return response()->json(['status'=>'error','message' => __("error_messages.house_id_exists")], 400);
        }

        $all = $this->request->all();
        $saveData=[];
        $saveData['drive_list_id'] ='';
        $saveData['house_id'] = $house_id;
        $saveData['created_at'] = date('Y-m-d H:i:s');
        $saveData['updated_at'] = date('Y-m-d H:i:s');
        $saveData[$all['name']] = $all['value'];

        # update information
        $info = $this->driveListService->storeDriveList($saveData);

        return response()->json(['status'=>'success','row' => ['data'=>$info], 'message' => __("messages.record_saved")], 200);

    }

    function storeDriveListDetail(){

        Log::info("DriveListController: storeDriveListDetail called");

        ## check input validation
        Log::info("DriveListController: update validation check");
        
        $rules = ['house_id'        => 'required|numeric|exists:home_information,house_id',
                  'drive_list_id'   => 'required|numeric',
                   'prop_key'       => 'required',
                   'prop_value'     => 'required' ];

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # first check record exists or not
        $info = $this->propertyService->findOneById($this->request->input('house_id'));

        if (empty($info)) {
            return response()->json(['status'=>'error','message' => __("error_messages.house_id_exists")], 400);
        }

        $saveInfo =$this->request->all();
        //$saveInfo['drive_list_detail_id']='';
        # update information
        $info = $this->driveListService->storeDriveListDetail($saveInfo);

        return response()->json(['status'=>'success','data' => $info, 'message' => __("messages.record_saved")], 200);
    }

    function getDriveList($house_id){
        
        $info = $this->driveListService->getDriveList($house_id);

        return response()->json(['status'=>'success','data' =>$info, 'message' => __("messages.record_saved")], 200);
    }

    function removeDriveListDetail($drive_list_detail_id){

        try
        {  
            $info = DriveListDetailModel::find($drive_list_detail_id);
            if ($info == true){
                $info->delete();
                return response()->json(['message' => __("messages.record_delete") , 'data'=>[]],200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data'=>[]], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreateUserDriveList()
    {
        Log::info("UserDriveListController: updateOrCreate called");

        ## check input validation
        Log::info("UserDriveListController: create validation check");
        $rules = UserFavoritesValidations::updateOrCreateDriveList();
        $all = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $saveInfo = [];
        
        $saveInfo['user_id'] = $this->userService->user_id();
        $saveInfo['house_id'] = $all['house_id'];
        $info = $this->driveListService->updateOrCreateUserDriveList($saveInfo);
        return response()->json(['status' => 'success','data' => $info, 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($house_id)
    {
        Log::info("UserDriveListController: delete called");
        try
        {
            $info = UserDriveListModel::where(['house_id'=>$house_id,"user_id"=>$this->userService->user_id()]);
            if ($info->count() > 0)
            {
                $info->delete();
                return response()->json(['status' => 'success','message' => __("messages.favourite_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['status' => 'failed','message' => __("messages.favourite_not_exists"), 'data' => []], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status' => 'failed','message' => __("error_messages.something_wrong")], 400);
        }
    }

    public function getAllUserDriveList(){
        Log::info("DriveListController: delete called");
        $user_id = $this->userService->user_id();
        $info = $this->driveListService->getUserDriveList($user_id);
        return response()->json(['status'=>'success','data' =>$info, 'message' => __("messages.record_saved")], 200);
    }

    public function getPropertyDriveList($house_id){
        Log::info("DriveListController: delete called");
       // $user_id = $this->userService->user_id();
        $info = $this->driveListService->getPropertyDriveListInfo($house_id);
        $address_url = CommonHelper::showdetail_url($info->house_id, $info);
        return response()->json(['status'=>'success','data' =>$info, 'message' => __("messages.record_saved")], 200);
    }

    public function exportDriveList(){

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();

        $user_id = $this->userService->user_id();
        $result = $this->driveListService->getUserDriveList($user_id);

        $index = 1;
        $description="";

        $section_style = $section->getStyle();
        $position = $section_style->getPageSizeW();
        $pageWidth= $section_style->getPageSizeW() - $section_style->getMarginLeft() - $section_style->getMarginRight();

        $phpWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>12));
        $fullLineHorizontalRule = array('weight' => 1, 'width' =>1000,'bold' => true, 'height' => 0,'dash'=> \PhpOffice\PhpWord\Style\Line::DASH_STYLE_DASH);
        
       
        $phpWord->addParagraphStyle( 'leftRight', ['tabs' => [new \PhpOffice\PhpWord\Style\Tab('right', $position)]]);



        foreach($result as $info){
             
            $section->addText("PROPERTY # ".$index.": ".$info->address."\t".$info->county, ['bold' => true,'size'=>14], 'leftRight');
            $section->addTextBreak();
            $section->addText("DESCRIPTION:",['bold' => true,'size'=>14]);
            $section->addText(date("m/d/Y H:i a"));
            $section->addTextBreak();
            $section->addLine($fullLineHorizontalRule);
        
            $address_url = CommonHelper::showdetail_url($info->house_id, $info);

            $section->addText("ADDRESS: ".$info->address);   										       
            $section->addText("Link: ".$address_url );
            $section->addText("Sale Date: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'sale_date', 'date'));
            $section->addText("Sale Type: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'sale_type', ''));
            $section->addText("Case Number: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'case_number', ''));
            $section->addText("Sale Place: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'sale_place', ''));
            $section->addText("Trustee: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'trustee', ''));
            $section->addText("Trustee Number: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'trustee_file_no', ''));
            $section->addText("1ST LIEN AMOUNT: ".CommonHelper::emptyDefaultObject(@$info->first_liens, 'lien_amount', ''));
            $section->addText("LOAN BALANCE:".CommonHelper::emptyDefaultObject(@$info->first_liens, 'amortization_loan_estimate_balance', ''));
            $section->addText("Total EST Debt w/ Late Payments  Atty Fees:".CommonHelper::emptyDefaultObject(@$info->first_liens, 'total_est_debt', '') );
            $section->addText("HOA NAME:".CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'hoa_name', '' ));
            $section->addText("HOA LIEN: ".CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'hoa_lien_amount', '') );
            $section->addText("HOA LIEN DATE: ".CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'date_of_hoa_lien', 'date') );
            $section->addText("HOA DEBT: ".CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'total_debt', ''));
            $section->addText("CMA/ARV VALUE: ".CommonHelper::emptyDefaultObject(@$info->last_cma_arv_recommendations, 'recommended_cma_arv', '') );
            $section->addText("OPENING BID: ".CommonHelper::emptyDefaultObject(@$info->last_sale_details, 'opening_bid', ''));
            $section->addText("WINNING BID: ".CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'winning_bid', ''));
            
            $section->addText("EXCESS FUNDS: ".CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'es_excess_funds', '')); 	                       					
            
            $total_equity = floatval(@$info->first_liens->est_equity ) + floatval(@$info->second_liens->est_equity ) + floatval(@$info->third_liens->est_equity ) ;
            $section->addText("EST EQUITY: ".CommonHelper::moneyFormat($total_equity));

            $section->addText("EST EQUITY: ".CommonHelper::moneyFormat($total_equity));
            $section->addText("REDEMPTION EXPIRATION:", ['bold' => true,'size'=>14],['align'=>'center']);
            $section->addText(CommonHelper::emptyDefaultObject(@$info->hoa_liens, 'redemption_expires', ''), ['bold' => true,'size'=>14],['align'=>'center']);

            $j=1;
            foreach($info->owner_info as $owner){
                 	
                $section->addText("OWNER NAME ".$j.":",['bold' => true,'size'=>14]);
                $section->addText("OWNER ".$j." ADDRESS:");
                $section->addText("Beenverified URL: ".CommonHelper::emptyDefault(@$owner->beenverified_url));
                $section->addText("Owner Full Name: ".CommonHelper::emptyDefault(@$owner->full_name));
                $section->addText("Owner Full Address: ".CommonHelper::emptyDefault(@$owner->full_address));
               
                $section->addText("Pacer URL: ".CommonHelper::emptyDefault(@$owner->pacer_url));
                $section->addText("Deceased Recorded Date: ".CommonHelper::emptyDefault(@$owner->deed_recorded_date));
                $section->addText("OWNER ".$j." CONTACT LIST:");
                $section->addText("PHONE NUMBER 1:");
                $section->addText("PHONE NUMBER: ".CommonHelper::emptyDefault(@$owner->phone));
                $section->addText("EMAIL/S:");
                $section->addText(CommonHelper::emptyDefault(@$owner->email));
                $j++;
            }
            $section->addPageBreak();
            ++$index;

        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(storage_path('driveListReport.docx'));
        } catch (Exception $e) {
        }
        $store_file_name = "GLPF__DRIVELIST_REPORT_" . time() . $this->userService->user_id() . '' . '.docx';
        $objWriter->save(storage_path('app/public/temp/'.$store_file_name));
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return response()->json(['status'=>'success','data' =>$document_url, 'message' => __("messages.record_saved")], 200);
        //return response()->download(storage_path('driveListReport.docx'));

    }

}
