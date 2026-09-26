<?php
/**
 * Created By Mranalinee Chouhan
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;
use DB;
use App\Models\ClientMasterModel;
use App\Models\ClientMasterClosingDocModel;

use App\Models\ClientRenovationtModel;
use App\Models\ClientRenovationDetailModel;

use App\Models\ClientInformationModel;
use App\Models\ClientInformationManagerModel;
use App\Models\ClientInformationMemberModel;
use App\Models\DocumentClientModel;

use App\Models\PropertyWholesaleBuyerModel;
use App\Models\PropertyLenderModel;
use App\Models\RenovationCategoryModel;
use App\Models\McdLenderModel;
use App\Models\BurnRateModel;
use App\Models\BurnRateModelDetail;
use App\Models\MailingModel;
use App\Models\PropertyModel;
use App\Models\ActualBurnRateModel;
use App\Models\InvoiceHistoryModel;


class ClientService
{
    private $findAllByHouseId;

    /**
     * ClientService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("ClientService: __construct called");
        $this->request = $request;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createClientMasterInfo($info){
        Log::info("ClientService: Client called");

        return ClientMasterModel::create($info);
    }



    /**
     * @param $id
     * @param $info
     * @return mixed
     */

    public function updateClientMasterInfo($house_id, $updateData){
        Log::info("ClientService: updateProperty called");

        return ClientMasterModel::updateOrCreate(["house_id"=>$house_id],$updateData);

    }



     /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */


    public function findClientMasterClosingDoc($house_id, $is_cache = false)
    {
        Log::info("ClientService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findClientMasterClosingDoc;
        }

        $this->findClientMasterClosingDoc =  ClientMasterClosingDocModel::where('house_id',$house_id)->get();

        #$this->getClientDocument = ClientMasterClosingDocModel::where('house_id',$house_id)->get();
        return $this->findClientMasterClosingDoc;
    }


    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */


    public function findClientMaster($house_id, $is_cache = false)
    {
        Log::info("ClientService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  ClientMasterModel::where('house_id',$house_id)->first();
        return $this->findAllByHouseId;
    }



    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getClientDocument($house_id, $is_cache = false)
    {
        Log::info("ClientService: getClientDocument called");
        if ($is_cache == true)
        {
            return $this->getClientDocument;
        }

        $this->getClientDocument = DocumentClientModel::with([
            'user' => function ($query) {
                $query->select(['id',
                    'first_name',
                    'last_name',
                    'username'
                ]);
            }
        ])
            ->where('house_id', $house_id)->get();

        #$this->getClientDocument = DocumentClientModel::where('house_id',$house_id)->get();

        return $this->getClientDocument;
    }






    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findClientRenovation($house_id,$section_type,$orderBy)
    {
        Log::info("DocumentService: findClientRenovation called");


        $this->findClientRenovation = ClientRenovationtModel
            ::select([
                '*'
            ])->with(
                [
                    'user' => function ($query) {
                        $query->select(['id',
                            'first_name',
                            'last_name',
                            'username'
                        ]);
                    },
                    'funder_info' => function ($query) {
                        $query->select(['id',
                            'lender_name',
                        ]);
                    }
                    ,'renovation_detail'
                    ,'renovation_detail.category'
                    ,'total_renovation_amount'
                    ,'renovation_detail.recipient'
                    ,'invoice_history.user'

                ]
            )->where('house_id', $house_id)
            ->where('section_type',$section_type)
            ->orderBy('invoice_date',$orderBy)
            ->get();

        return $this->findClientRenovation;
    }


     /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findClientRenovationById($id)
    {
        Log::info("DocumentService: findClientRenovation called");


        $invoiceDetail = ClientRenovationtModel
            ::select([
                '*'
            ])->with(
                [
                    'user' => function ($query) {
                        $query->select(['id',
                            'first_name',
                            'last_name'
                        ]);
                    },
                    'funder_info' => function ($query) {
                        $query->select(['id',
                            'lender_name',
                        ]);
                    }
                    ,'renovation_detail'
                    ,'renovation_detail.category'
                    ,'total_renovation_amount'
                    ,'renovation_detail.recipient'
                ]
            )->where('id', $id)
            ->first();

        return $invoiceDetail;
    }


    public function clientRenovationCreate($info)
    {
        Log::info("ClientService: clientRenovation called");
        $info['created_at'] = time();
        $info['added_by']     = $this->request->auth->id;
        return ClientRenovationtModel::create($info);
    }


    public function clientRenovationUpdate($id, $updateData){
        Log::info("ClientService: updateProperty called");
        unset($updateData['house_id']);
        return ClientRenovationtModel::updateOrCreate(["id"=>$id],$updateData);

    }

    public function clientRenovationDetailCreate($info){
        Log::info("ClientService: clientRenovationDetailCreate called");
        $info['created_at'] = time();
        return ClientRenovationDetailModel::create($info);
    }

    public function clientRenovationDetailUpdateOrCreate($info){
        Log::info("ClientService: clientRenovationDetailUpdateOrCreate called");
        return ClientRenovationDetailModel::updateOrCreate(["id"=>$info['id']],$info);
    }



    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createClientInformation($info){
        Log::info("ClientService: createClientInformation called");
        $info['added_by']     = $this->request->auth->id;
        //print_r($info);die;
        return ClientInformationModel::updateOrCreate(["house_id"=>$info['house_id']],$info);
    }


    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createUpdateClientInfoMember($info){
        Log::info("ClientService: createClientInformationMember called");

        return ClientInformationMemberModel::updateOrCreate(['id'=>$info['id']],$info);
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createUpdateClientInfoManager($info){
        Log::info("ClientService: createClientInformationManager called");

        return ClientInformationManagerModel::updateOrCreate(['id'=>$info['id']],$info);
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findClinetInformation($house_id, $is_cache = false)
    {
        Log::info("ClientService: findClinetInformation called");
        if($is_cache == true)
        {
            return $this->findClinetInformation;
        }

        $client_information = ClientInformationModel::where('house_id',$house_id)->first();
        if($client_information == true){
            $client_information->member=ClientInformationMemberModel::where('client_information_id',$client_information->id)->get();
            $client_information->manager=ClientInformationManagerModel::where('client_information_id',$client_information->id)->get();

        }


        $this->findClinetInformation =$client_information;
        return $this->findClinetInformation;
    }

    function removeClientMember($id){
        $clientMember = ClientInformationMemberModel::find($id);
        $clientMember->delete();
    }

    function removeClientManager($id){
        $clientManager = ClientInformationManagerModel::find($id);
        $clientManager->delete();
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */

    public function updateClientInformation($updateData, $id){
        Log::info("ClientService: updateClientInformation called");

        return ClientInformationModel::updateOrCreate(["id"=>$id], $updateData);

    }


    function saveClient($info){

        return PropertyWholesaleBuyerModel::updateOrCreate(["user_id"=>$info['id'],'house_id'=>$info['house_id']],$info);

    }

    function saveLender($info){
        return PropertyLenderModel::updateOrCreate(["user_id"=>$info['id'],'house_id'=>$info['house_id']],$info);

    }

    function findWholesaleBuyer($house_id){

        $result = PropertyWholesaleBuyerModel::select(["users.id","users.email","users.first_name","users.last_name"
            ])->leftJoin('users', 'property_wholesale_buyer.user_id', '=', 'users.id')
            ->where('property_wholesale_buyer.house_id', '=', $house_id)->get();

        return $result;

    }

    function findFunderLender($house_id){

        $result = PropertyLenderModel::select(["users.id","users.email","users.first_name","users.last_name"
        ])->leftJoin('users', 'property_lender.user_id', '=', 'users.id')
            ->where('property_lender.house_id', '=', $house_id)->get();

        return $result;

    }

    public function findClientInvoiceExpenses($house_id)
    {
        Log::info("DocumentService: findClientRenovation called");

        $clientInvoiceNonHub = ClientRenovationDetailModel::select(['sub_category',DB::raw("SUM(amount) as total_amount")])
            ->with(['category',
                    'homebuyer_reno_category' => function ($query)  use ($house_id) {
                        $query->where('house_id', $house_id);
                    }])
            ->where('house_id', $house_id)
            ->groupBy('sub_category')
            ->get();

        return $clientInvoiceNonHub;
    }

   public function getRenovationDetail($house_id,$classification=''){
     $renovationDetail = ClientRenovationDetailModel
         ::select(['client_renovation_detail.*','renovation_category.*','client_renovation.invoice_date','client_renovation.invoice_url'])
         ->with(['category'])
         ->where('client_renovation_detail.house_id', $house_id)
         ->where('classification',$classification)
         ->join('client_renovation', 'client_renovation.id', '=', 'client_renovation_detail.invoice_id')
         ->join('renovation_category', 'renovation_category.id', '=', 'client_renovation_detail.sub_category')
         ->orderBy('renovation_category.category_name', 'ASC')
         ->get();
         return $renovationDetail;
   }

   public function getRenovationByCat($house_id){
        $renovationDetail = ClientRenovationDetailModel
        ::select(['sub_category',DB::raw("SUM(amount) as total_amount")])
        ->with(['category'])
        ->where('house_id', $house_id)
        ->groupBy('sub_category')
        ->join('renovation_category', 'renovation_category.id', '=', 'client_renovation_detail.sub_category')
         ->orderBy('renovation_category.category_name', 'ASC')
        ->get();
        return $renovationDetail;
   }

   function getRenovationCategory(){
        return RenovationCategoryModel::orderBy('category_name', 'ASC')->get();
   }

   function saveUpdateRenovationCategory($all){
        $info=RenovationCategoryModel::updateOrCreate(["id"=>$all['id']], $all);
        return $info;
   }

   function removeRenovationCategory($id) {
        $category = RenovationCategoryModel::where('id', $id);
        if ($category == true) {
            $category->delete();
        }
    }
    
    public function getInvoiceTotal($house_id,$section_type='invoices'){

        return $totalInvoice=ClientRenovationtModel::
                select(['client_renovation.house_id',DB::raw("SUM(client_renovation.amount) as total_amount"),DB::raw("SUM(client_renovation_detail.amount) as bill_total_amount")])
                ->leftJoin('client_renovation_detail', 'client_renovation_detail.invoice_id', '=', 'client_renovation.id')
                ->where('client_renovation.house_id', $house_id)
                ->where('client_renovation.section_type',$section_type)
                ->groupBy('client_renovation.house_id')
                ->first();

    }
    

    public function getLenderByAmount($house_id,$section_type='invoices',$funderId=''){
        $totalInvoice=McdLenderModel::select(['mcd_lender.id','mcd_lender.lender_name',DB::raw('client_renovation.total_amount'),DB::raw("SUM(client_renovation_detail.amount) as bill_total_amount")])
                ->leftJoin(DB::raw("(SELECT funder,house_id,section_type,SUM(client_renovation.amount) as total_amount FROM client_renovation where house_id=$house_id and section_type = '$section_type' group by funder)  as client_renovation"), 'client_renovation.funder', '=', 'mcd_lender.id')
                ->leftJoin('client_renovation as clientReno', 'clientReno.funder', '=', 'mcd_lender.id')
                ->leftJoin('client_renovation_detail', 'client_renovation_detail.invoice_id', '=', 'clientReno.id')
                ->where('client_renovation.house_id', $house_id)
                ->where('client_renovation.section_type',$section_type);
            
            if(!empty($funderId)){
                $totalInvoice=$totalInvoice->where('client_renovation.funder', $funderId);
            }
            $totalInvoice=$totalInvoice->groupBy('mcd_lender.id')->get();
               
        return $totalInvoice;
    } 

    public function getLenderbyInvoices($house_id){
        return $totalInvoice=McdLenderModel::select(['mcd_lender.id','mcd_lender.lender_name',DB::raw('client_renovation.total_amount'),DB::raw("SUM(client_renovation_detail.amount) as bill_total_amount")])
                ->leftJoin(DB::raw("(SELECT funder,house_id,SUM(client_renovation.amount) as total_amount FROM client_renovation where house_id=$house_id group by funder)  as client_renovation"), 'client_renovation.funder', '=', 'mcd_lender.id')
                ->leftJoin('client_renovation as clientReno', 'clientReno.funder', '=', 'mcd_lender.id')
                ->leftJoin('client_renovation_detail', 'client_renovation_detail.invoice_id', '=', 'clientReno.id')
                ->where('client_renovation.house_id', $house_id)
                ->groupBy('mcd_lender.id')
                ->get();
    } 

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateBurnRate($id, $info){
        Log::info("McdService: updateCreateBankStatement called");
        $info= BurnRateModel::updateOrCreate(["id" => $id], $info);
        return $this->getBurnRateById($info->id);
    }
     /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getBurnRateByHouseId($house_id){
        return BurnRateModel::with(['category'])->where('house_id',$house_id)->get();
    }

    public function burnRateSummeryByHouseId($house_id){
       $resultA= BurnRateModel::select([DB::raw("sum(temp.total_amount) as total_amount"),DB::raw('sum(actual_months) as total_actual_month')
                ,DB::raw("sum(temp.total_amount/actual_months) as monthly_total_amount")])
        ->leftJoin(DB::raw("(SELECT sub_category,sum(amount) as total_amount from client_renovation_detail WHERE house_id=$house_id GROUP by sub_category) as temp"),'temp.sub_category', '=', 'burn_rate.category')
        ->where('burn_rate_type','estimated')
        ->where('house_id',$house_id)->first();

       $resultB= BurnRateModel::select([DB::raw("sum(amount) as total_amount"),DB::raw('sum(actual_months) as total_actual_month')])->where('burn_rate_type','actual')->where('house_id',$house_id)->first();
       
       $info['total_actualburn_rate']=round(($resultA->total_amount+$resultB->total_amount),2); 
       $info['total_actual_month']=($resultA->total_actual_month??0+$resultB->actual_months??0);
       $info['monthly_total_actualburn_rate']=round(($resultA->monthly_total_amount+$resultB->total_amount/12),2); 
       $info['weekly_total_actualburn_rate']=round((($resultA->monthly_total_amount)/4+$resultB->total_amount/48),2); 
       $info['daily_total_actualburn_rate']=round((($resultA->monthly_total_amount)/30+$resultB->total_amount/360),2); 
       return $info;
    }

    public function estimateRateSummeryByHouseId($house_id){
        $result= BurnRateModel::select([DB::raw("sum(amount) as total_amount"),DB::raw('sum(actual_months) as total_actual_month')])->where('burn_rate_type','estimated')->where('house_id',$house_id)->first();
        return $result;
    }
    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function getBurnRateById($id){
        return BurnRateModel::with(['category'])->where('id',$id)->first();
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getActualBurnRateByHouseId($house_id){
        // return BurnRateModel::with(['category'])
        //         ->select(['burn_rate.id','burn_rate.actual_months','burn_rate.category',DB::raw('IFNULL(actual_burn_rate.amount,IFNULL(temp.total_amount,0)) as total_amount')])
        //         ->leftJoin(DB::raw("(SELECT sub_category,sum(amount) as total_amount from client_renovation_detail WHERE house_id=$house_id GROUP by sub_category) as temp"),'temp.sub_category', '=', 'burn_rate.category')
        //         ->leftJoin('actual_burn_rate','actual_burn_rate.category_id','burn_rate.category')
        //         ->where('burn_rate_type','estimated')
        //         ->where('burn_rate.house_id',$house_id)->get();

        $burnRateInfo= BurnRateModel::with(['category'])
        ->select(['burn_rate.id','burn_rate.actual_months','burn_rate.category',DB::raw('burn_rate.category as category_id'),DB::raw('IFNULL(temp.total_amount,0) as total_amount')])
        ->leftJoin(DB::raw("(SELECT sub_category,sum(amount) as total_amount from client_renovation_detail WHERE house_id=$house_id GROUP by sub_category) as temp"),'temp.sub_category', '=', 'burn_rate.category')
        ->where('burn_rate_type','estimated')
        ->where('house_id',$house_id)->get();

        $actualBurnRate= ActualBurnRateModel::where('house_id',$house_id)->get();
        if(!empty($burnRateInfo)){
            foreach($burnRateInfo as $burnRate){
                if(!empty($actualBurnRate)){
                    foreach($actualBurnRate as $actual){
                        if($actual->category_id==$burnRate->category_id){
                            $burnRate->total_amount=$actual->amount;  
                        }
                    }
                }
            }
        }
        return $burnRateInfo;
        
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateOrCreateBurnRate($id, $info){

        Log::info("McdService: updateCreateBankStatement called");
        $info= BurnRateModelDetail::updateOrCreate(["house_id" => $id], $info);
        return $info;
    }  
    /**
    * Find property record
    * @param $id
    * @return mixed
    */
   public function getBurnRateDetailByHouseId($house_id){
       return BurnRateModelDetail::where('house_id',$house_id)->first();
   }

   public function getRenoHomeBuyerCategory($house_id){
    
        return RenovationCategoryModel::select(['renovation_category.id','renovation_category.category_name','reno.amount','reno.est_amount','reno.buyer_amount'])
        ->leftJoin(DB::raw("(SELECT sub_category,SUM(amount) as amount,sum(est_amount) as est_amount,SUM(buyer_amount) as buyer_amount FROM client_renovation_detail WHERE house_id=$house_id GROUP by sub_category) as reno"),'renovation_category.id', '=', 'reno.sub_category')
        ->where('homebuyer_category',1)
        ->get();
   
    }

    public function createUpdateMailing($id,$info){
        $info= MailingModel::updateOrCreate(["id" => $id], $info);
        return $info;
    }

    public function getMailingByHouseId($house_id){
       return MailingModel::where('house_id',$house_id)->get();
    }

    public function sendEmailToUserByHouseId($house_id){
       return PropertyModel::with(['mailing_list'])->where('house_id',$house_id)->first();
    }

 /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateActualBurnRate($info){

        Log::info("McdService: updateCreateActualBurnRate called");
        $result= ActualBurnRateModel::updateOrCreate(["house_id" => $info['house_id'],'category_id'=>$info['category_id']], $info);
        return $result;
    }  

    public function updateCreateInvoiceHistory($info){
        $result= InvoiceHistoryModel::updateOrCreate(["house_id" => $info['house_id'],'invoice_id'=>$info['invoice_id'],'user_id'=>$info['user_id']], $info);
        return InvoiceHistoryModel::with(['user'])->where('id',$result->id)->first();
    }

}
