<?php

namespace App\Http\Controllers;

//use App\Http\Validations\GeoValidations;
use App\Services\McdService;
use App\Services\UserService;
use App\Services\ClientService;

use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Models\McdModel;
use App\Services\DocumentService;
use App\Models\McdLenderModel;
use App\Models\LenderStatementModel;
use App\Models\TradesmanTrackingModel;
use App\Models\ShortTermRentalModel;
use App\Models\BankStatementModel;
use App\Models\DepositSpreadsheetModel;
use App\Models\DepositSheetLenderModel;
use App\Models\DepositSheetLinkModel;
use App\Models\TradeshmanUserModel;
use Illuminate\Support\Facades\Crypt;
use App\Models\McdUsersModel;

class McdController extends Controller
{

    private $request;
    private $userService;
    private $mcdService;
    private $documentService;
    private $clientService;



    public function __construct(
        Request $request
        , UserService $userService
        , McdService $mcdService
        , DocumentService $documentService
        , ClientService $clientService
    ) {
        Log::info("McdController: __construct called");
        $this->request                = $request;
        $this->userService            = $userService;
        $this->mcdService             = $mcdService;
        $this->documentService        = $documentService;
        $this->clientService          = $clientService;
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {
        Log::info("McdController: index called");
        $info['list'] = $this->mcdService->findAllByHouseId($house_id);
        $info['total_by_lender']=$this->mcdService->getTotalMcdBylender($house_id);
        $info['client_list']=$this->mcdService->getClientInfoList();
        $info['payers_list']=$this->mcdService->getPayersInfoList();
        $info['other_mcd_info']=$this->mcdService->getOtherMcdInfo($house_id);
        $info['clientInoviceExpense']=$this->clientService->getLenderbyInvoices($house_id);

        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreate($house_id)
    {
        Log::info("McdController: updateOrCreate called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        $inventor_document = $this->documentService->documentUpload('document_investor','invenstor_document');
        
        if(!empty($inventor_document) && $inventor_document['store_name'] != $inventor_document['invenstor_document'])
        {
            $all['store_name'] = $inventor_document['store_name'];
            $all['org_name'] = $inventor_document['org_name'];
        }

        $all['funded_days']=!empty($all['funded_days'])?$all['funded_days']:0;
        
        $info['mcd_detail']=$this->mcdService->updateCreate($id, $all);
        $info['total_by_lender']=$this->mcdService->getTotalMcdBylender($house_id,$info['mcd_detail']->lender_name)[0];

        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = McdModel::find($id);
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
    public function updateOrCreateMcdLender($house_id)
    {
        Log::info("McdController: updateOrCreate called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
       
        $agreement = $this->documentService->documentUpload('document_agreement','document_agreement');
        $artical = $this->documentService->documentUpload('document_artical','artical');
        $ein = $this->documentService->documentUpload('document_ein','ein');
       
        if(!empty($agreement))
        {
            $all['agreement_store_name'] = $agreement['store_name'];
            $all['agreement_org_name'] = $agreement['org_name'];
        }
        if(!empty($artical))
        {
            $all['artical_store_name'] = $artical['store_name'];
            $all['artical_org_name'] = $artical['org_name'];
        }
        if(!empty($ein))
        {
            $all['ein_store_name'] = $artical['store_name'];
            $all['ein_org_name'] = $artical['org_name'];
        }
        if(empty($id) || $id=='null')
        {
            $all['created_at']       = time();
        }    
        $all['updated_at']       = time();
        $all['is_lender'] =$all['is_lender']??0;
        
        $info=$this->mcdService->updateCreateMcdLender($id, $all);
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

    public function getMcdLender($house_id){
        
        Log::info("McdController: getMcdLender called");
        $info = $this->mcdService->getMcdLender($house_id);
        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

    public function getMcdLenderDetails($house_id){
        
        Log::info("McdController: getMcdLenderDetail called");
        //$info = $this->mcdService->getMcdLenderDetail($house_id);
        $info = $this->mcdService->getTotalMcdBylender($house_id);
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
    public function destroyMcdLender($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = McdLenderModel::find($id);
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
    public function updateOrCreateLenderStatement($house_id)
    {
        Log::info("McdController: updateOrCreateLenderStatement called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        $inventor_document = $this->documentService->documentUpload('document_statement','document_statement');
        
        if(!empty($inventor_document) && $inventor_document['store_name'] != $inventor_document['document_statement'])
        {
            $all['doc_store_name'] = $inventor_document['store_name'];
            $all['doc_org_name'] = $inventor_document['org_name'];
        }
        
        $info=$this->mcdService->updateCreateLenderStatement($id, $all);

        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLenderStatement($house_id)
    {
        Log::info("McdController: index called");
        $info = $this->mcdService->getLenderStatement($house_id);
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
    public function destroyLenderStatement($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = LenderStatementModel::find($id);
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
    public function updateOrCreateTradesmanTracking($house_id)
    {
        Log::info("McdController: updateOrCreateTradesmanTracking called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();

        if(!is_numeric($all['name'])){
            $tradeshUser=TradeshmanUserModel::create(['username'=>$all['name']]);
            $all['name']=$tradeshUser->id;
        }
        $tradesman=$this->mcdService->updateCreateTradesmanTracking($id, $all);
        if(!empty($tradesman)){
            //foreach($tradesman_tracking as $tradesman){
                if(!empty($tradesman->user) && !empty($tradesman->user->wInfo)){
                    $tradesman->user->wInfo->social_security_number='XXX-XX-'.Crypt::decryptString($tradesman->user->wInfo->social_security_number);
                }
            //}
        }
        $info['tradesman_tracking']=$tradesman;
        $info['tradesman_summery']=$this->mcdService->getTradesmanTrackingByUser($house_id,$all['name']);
     
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }
    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreateTradesmanUser()
    {
        Log::info("McdController: updateOrCreateTradesmanUser called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $id              = $all['id'];
        
        if(!empty($all['name'])){
            $info=TradeshmanUserModel::updateOrCreate(["id" => $id], ['username'=>$all['name']]);
            $tradeshUser=$this->mcdService->getTradeshmanUser($info->id);
        }
        //$info['tradesman_tracking']=$this->mcdService->updateCreateTradesmanTracking($id, $all);
        //$info['tradesman_summery']=$this->mcdService->getTradesmanTrackingByUser($house_id,$all['name']);
     
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$tradeshUser[0]], 200);
    }
     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTradesmanTracking($house_id)
    {
        Log::info("McdController: index called");
        $tradesman_tracking = $this->mcdService->getTradesmanTrackingByHouseId($house_id);
        if(!empty($tradesman_tracking)){
            foreach($tradesman_tracking as $tradesman){
                if(!empty($tradesman->user) && !empty($tradesman->user->wInfo)){
                    $tradesman->user->wInfo->social_security_number='XXX-XX-'.Crypt::decryptString($tradesman->user->wInfo->social_security_number);
                }
            }
        }
        $info['tradesman_tracking']= $tradesman_tracking;

        $tradesman_users = $this->mcdService->getTradeshmanUser();
        if(!empty($tradesman_users)){
            foreach($tradesman_users as $userdetail){
                if(!empty($userdetail->wInfo) && !empty($userdetail->wInfo->social_security_number)){
                    $userdetail->wInfo->social_security_number='XXX-XX-'.Crypt::decryptString($userdetail->wInfo->social_security_number);
                }
            }
        }
        $info['tradesman_users']=$tradesman_users;
       
        $info['tradesman_summery']=$this->mcdService->getTradesmanTrackingByUser($house_id);
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
    public function destroyTradesmanTracking($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = TradesmanTrackingModel::find($id);
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
    public function updateCreateShortTermRental($house_id)
    {
        Log::info("McdController: updateCreateShortTermRental called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        // $rental_document = $this->documentService->documentUpload('document_rental','rental_doc');
        
        // if(!empty($rental_document) && $rental_document['store_name'] != $rental_document['rental_doc'])
        // {
        //     $all['rental_doc_store_name'] = $rental_document['store_name'];
        //     $all['rental_doc_org_name'] = $rental_document['org_name'];
        // }
        
        $info=$this->mcdService->updateCreateShortTermRental($id, $all);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShortTermRentalInfo($house_id)
    {
        Log::info("McdController: index called");
        $info = $this->mcdService->getShortTermRentalInfo($house_id);
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
    public function destroyShortTermRental($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = ShortTermRentalModel::find($id);
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
    public function updateCreateBankStatement($house_id)
    {
        Log::info("McdController: updateCreateBankStatement called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        $info=$this->mcdService->updateCreateBankStatement($id, $all);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBankStatement($house_id)
    {
        Log::info("McdController: index called");
        $info = $this->mcdService->getBankStatementByHouseId($house_id);
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
    public function destroyBankStatement($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = BankStatementModel::find($id);
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
    public function updateCreateDepositSpreadSheet($house_id)
    {
        Log::info("McdController: updateCreateDepositSpreadSheet called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id'];
        $info=$this->mcdService->updateCreateDepositSpreadSheet($id, $all);
        
        if(!empty($all['more_lender_data'])){
            foreach($all['more_lender_data'] as $lender){
                if(!empty($lender['lender_id'])){
                    $lender['house_id']=$house_id;
                    $lender['deposit_id']=$info->id;
                    $this->mcdService->updateCreateDepositSheetLender($lender['id'], $lender);
    
                }
            }
        }
        
        if(!empty($all['more_link_data'])){
            foreach($all['more_link_data'] as $link){
                if(!empty($link['link_name'])){
                    $link['house_id']=$house_id;
                    $link['deposit_id']=$info->id;
                    $this->mcdService->updateCreateDepositSheetLink($link['id'], $link);
                }
            }
        }

        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepositSpreadSheet($house_id)
    {
        Log::info("McdController: index called");
        $info['deposit_sheet']   = $this->mcdService->getDepositSpreadSheetByHouseId($house_id);
        $info['deposit_lender']  = $this->mcdService->getDepositLender();
        $info['lender_category'] = $this->mcdService->getDepositLenderCat($house_id);
        $info['link_category'] = $this->mcdService->getDepositLinkCat($house_id);

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
    public function destroyDepositSpreadSheet($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = DepositSpreadsheetModel::find($id);
            if ($info != null)
            {
                $info->delete();
                DepositSheetLenderModel::where('deposit_id',$id)->delete();
                DepositSheetLinkModel::where('deposit_id',$id)->delete();
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
    public function updateMcdLenderSingleRecord($house_id)
    {
        Log::info("PropertyController: update called");
        # update information
        $all = $this->request->all();
        $saveData=[];
       // $saveData['id'] = $all['id'];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $info=$this->mcdService->updateCreateMcdLender($all['id'], $saveData);
        return response()->json(['row' => __("messages.record_saved"),'data'=>$info->id,'status'=>'success'], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCreateDepositLender()
    {
        Log::info("McdController: updateCreateDepositLender called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $id              = intVal($all['id']);
        $info=$this->mcdService->updateCreateDepositLender($id, $all);
        
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function removeDepositAccount($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = DepositSheetLenderModel::find($id);
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
     * @param $id
     * @return JsonResponse
     */
    public function removeDepositLink($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = DepositSheetLinkModel::find($id);
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
    public function getAllDepositSheets()
    {
        Log::info("McdController: index called");
        $limit  = $this->request->get('limit') ? $this->request->get('limit') : 20;
        $offset = $this->request->get('offset') ? $this->request->get('offset') : 0;
        
        $address=$this->request->get('property_address')??'';
        $lender=$this->request->get('lender')??'';
        $client_id=$this->request->get('client_id')??'';

        // $info['deposit_lender']  = $this->mcdService->getDepositLender();
        // if(empty($lender))
        //     $lender=$info['deposit_lender'][0]->id;

        $info['deposit_sheet']   = $this->mcdService->getAllDeposit($address,$lender,$client_id,$offset,$limit);
        $lender_ids=$this->mcdService->getClientLender($client_id);
        
        if(empty($lender)){
            $lender=[];
            foreach($lender_ids as $lenderId){
                $lender[]=$lenderId->lender_id;
            }
        }else{
            $lender=[$lender];
        }
        $info['lender_category'] = $this->mcdService->get_deposit_lender($lender);
        $info['link_category'] = $this->mcdService->get_deposit_link($lender);

        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLenderDepositSheet()
    {
        $info['deposit_lender']  = $this->mcdService->getDepositLender();
        $info['client_list']  = $this->mcdService->getClientInfoList();
        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMcdOtherRecord($house_id)
    {
        Log::info("PropertyController: update called");
        # update information
        $all = $this->request->all();
        //var_dump($all);
        if($all['client_id']=='new'){
            $client=$this->mcdService->updateClientInfo('',$all);
            $all['client_id']=$client->id;
        }

        $payerArray=['id'=>$all['payers_id'],'payers_name'=>$all['payers_name'],
                    'payers_address'=>$all['payers_address'],'payers_tin'=>$all['payers_tin']];
        if($all['payers_id']=='new'){
            $payers=$this->mcdService->updatePayersInfo('',$payerArray);
            $all['payers_id']=$payers->id;
        }else{
            $this->mcdService->updatePayersInfo($all['payers_id'],$payerArray);
        }
        $all['house_id'] = $house_id;
        $this->mcdService->updateOtherMcdInfo($house_id, $all);
        $info=$this->mcdService->getOtherMcdInfo($house_id);
        return response()->json(['message' => __("messages.record_saved"),'data'=>$info,'status'=>'success'], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReschedule()
    {

        $all=$this->request->all();
        $reschedule_info  = $this->mcdService->getReschedule($all);
        $totalProfit=0;
        foreach($reschedule_info as $reschedule){
            $reschedule['net_profit']=$this->mcdService->getNetProfit($reschedule->house_id);
            $reschedule['lender_info']=[];
            $reschedule['craig_info']=['percentage'=>0,'total_percentage'=>0];
            $craigPer=0;
            if(!empty($reschedule->property_lender)){
                
                foreach($reschedule->property_lender as $property_lender){
                    if($property_lender->is_lender){
                        $reschedule['lender_info']= $property_lender;
                    }
                    if($property_lender->is_craig_per){
                        $craigPer+=$property_lender->percentage;

                        $property_lender->total_percentage=$craigPer;
                        $reschedule['craig_info']= $property_lender;
                    }
                }
              
                $totalProfit+=round(((!empty($reschedule['net_profit'])?$reschedule['net_profit']->netProfit:0)*$reschedule['craig_info']['total_percentage'])/100,2);

            }

            $loan_amount=0;
            if(!empty($reschedule->property_info->first_liens)){
                $mortgage=$reschedule->property_info->first_liens;
                if($mortgage->no_str_no_appt==1 && !empty( $mortgage->amortization_loan_estimate_balance)){
                    $loan_amount=$mortgage->amortization_loan_estimate_balance;
                }
                else if($mortgage->no_str_no_appt==0 && !empty( $mortgage->amortization_loan_estimate_balance))
                {
                    $loan_amount=$mortgage->amortization_loan_estimate_balance+$mortgage->est_late_payment_and_fees;
                }else{
                    $loan_amount=$mortgage->lien_amount??0;
                }  
            }
            $reschedule->loan_amount=$loan_amount;

        }
        $info['reschedule_info'] =$reschedule_info;
        $info['total_craig_profit']=$totalProfit;
        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addMcdUser()
    {
        Log::info("McdController: addMcdUser called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $mcdUser=[];
        $mcdUser['user_id'] =$all['user_id'];
        $mcdUser['house_id']=$all['house_id'];
        $mcdUser['created_at']=date('Y-m-d H:i:s');
        $mcdUser['updated_at']=date('Y-m-d H:i:s');

        $this->mcdService->createUpdateMcdUser($mcdUser);
        $info=$this->mcdService->getMcdUser($mcdUser['house_id'],$mcdUser['user_id']);
        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }
    
 /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMcdUser($house_id)
    {
        Log::info("PropertyController: update called");
        # update information
        $info['mcd_users'] = $this->mcdService->getMcdUserInfo($house_id);
        $info['buyer_list'] = $this->userService->findWholesaleBuyerList('');
        return response()->json(['message' => __("messages.record_saved"),'data'=>$info,'status'=>'success'], 200);
    }

    function destroyMcdUser($id){
        Log::info("McdController: mcd called");
        try
        {
            $info = McdUsersModel::find($id);
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
    public function isMcdAccess($house_id)
    {
        Log::info("PropertyController: update called");
        # update information
        $is_buyer = $this->userService->is_buyer();
        $mcd_users = $this->mcdService->getMcdUserInfo($house_id);
        $mcdAccess=true;
        if($is_buyer && !empty($mcd_users)){
            $user_id= $this->userService->user_id();
            $info=$this->mcdService->getMcdUser($house_id,$user_id);
            if(empty($info))
                $mcdAccess=false;
        }
        $info['buyer_list'] = $this->userService->findWholesaleBuyerList('');
        return response()->json(['message' => __("messages.record_saved"),'data'=>$mcdAccess,'status'=>'success'], 200);
    }
    
}
