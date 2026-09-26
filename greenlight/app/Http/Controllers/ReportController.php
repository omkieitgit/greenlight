<?php

namespace App\Http\Controllers;

use App\Exports\CommonExport;
use App\Helpers\PaymentHelper;
use App\Models\CmaArvModel;
use App\Models\DocumentBidderModel;
use App\Models\DocumentSaleModel;
use App\Models\SaleDetailsModel;
use App\Models\HouseBuyItModel;
use App\Services\GeoService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Models\PropertyModel;
use App\Services\PropertyService;
use App\Models\DocumentOwnerModel;
use App\Services\ReportService;

class ReportController extends Controller
{

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;
    private $geoService;
    private $excel;
    private $propertyService;
    private $reportService;

    public function __construct(
        Request $request
        , UserService $userService
        , GeoService $geoService
        , Excel $excel 
        , PropertyService $propertyService
        , ReportService $reportService
    ) {
        Log::info("GeoController: __construct called");
        $this->request                = $request;
        $this->userService            = $userService;
        $this->geoService             = $geoService;
        $this->excel                  = $excel;
        $this->propertyService        = $propertyService;
        $this->reportService          = $reportService;  
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info("ReportController: index called");
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;

        $from  = $this->request->get('from') ;
        $to  = $this->request->get('to') ;
        $keywords  = $this->request->get('keywords') ? $this->request->get('keywords') : '';
        $type  = $this->request->get('user_type') ? $this->request->get('user_type') : '';

        CommonHelper::startEndDateCheck($from,$to);
        $user_id = $this->userService->user_id();
        $where = [
        ];

        if($type == 'first_dtc'){
            $where[] = ['info_added_by', '=', 'first_dtc'];
        }
        else if($type == 'second_dca'){
            $where[] = ['info_added_by', '=', 'second_dca'];
        }
        else if($type == 'third_dca'){
            $where[] = ['info_added_by', '=', 'third_dca'];
        }
        else if($type == 'chief_dca'){
            $where[] = ['info_added_by', '=', 'final_dca'];
        }
        else if($type == 'nos_by')
        {}
        else if($type == 'im_by')
        {}
        else if($type == 'trustee_caller')
        {}
        else if($type == 'auction_by')
        {}
        else if($type == 'clear_report')
        {
            
        }
        else{
            return response()->json(['status' => 'failed','message'=>"Invalid request.",'data' => []], 200);
        }

        $query = '';
        if(in_array($type, ['first_dtc','second_dca','third_dca','chief_dca'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['user_id', '=', $user_id];
            }

            $query = CmaArvModel::where($where)->select('user_id')->addSelect(DB::raw('count(*) as total_records'));
            $query->join('home_information', 'home_information.house_id', '=', 'cma_arv_recommendations.house_id');
            //$query->distinct('home_information.house_id');
            $query->whereNull('home_information.deleted_at');
            $query->whereNotNull('home_information.address');
            $query->with(['user']);
            $query->whereNotNull('user_id');
            $this->whereBetween($query, 'date', $from, $to);
            if(!empty($keywords)){
              $query->leftJoin('users', 'cma_arv_recommendations.user_id', '=', 'users.id');
              $query->where('users.first_name', 'LIKE', "%$keywords%");
            }
            $query->groupBy('user_id');
        }
        else if(in_array($type, ['nos_by'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['nos_by', '=', $user_id];
            }

            $query = SaleDetailsModel::where($where)->select(['nos_by','nos_by as user_id'])->addSelect(DB::raw('count(*) as total_records'));
            $query->with(['nos']);
            $query->whereNotNull('nos_by');
            $this->whereBetween($query, 'nos_date', $from, $to);

            $query->leftJoin('users', 'sale_details.nos_by', '=', 'users.id');
            $query->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id');
            $query->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id');
            $query->where('role_key','nos_by');

            if(!empty($keywords)){
              $query->where('users.first_name', 'LIKE', "%$keywords%");
            }
            $query->groupBy('nos_by');
        }
        else if(in_array($type, ['im_by'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['added_by', '=', $user_id];
            }

            $query1 = DocumentSaleModel::where($where)->select(['id','added_by','added_by as user_id']);//->addSelect(DB::raw('count(*) as total_records'));
            $query1->leftJoin('sale_details', 'document_sale.sale_id', '=', 'sale_details.sale_id');
            $query1->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query1->whereNotNull('added_by');
            $query1->whereNull('home_information.deleted_at');
            $this->whereBetween($query1, 'document_sale.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));

            $query2 = DocumentBidderModel::where($where)->select(['id','added_by','added_by as user_id']);//->addSelect(DB::raw('count(*) as total_records'));
            $query2->leftJoin('sale_bidder', 'document_bidder.bidder_id', '=', 'sale_bidder.bidder_id');
            $query2->leftJoin('sale_details', 'sale_bidder.sale_id', '=', 'sale_details.sale_id');
            $query2->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query2->whereNotNull('added_by');
            $query2->whereNull('home_information.deleted_at');
            $query2->whereNull('sale_bidder.deleted_at');
            $this->whereBetween($query2, 'document_bidder.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));


            $query3 = $query2->unionAll($query1);

            $query = DB::query()->fromSub($query3, 'subquery');
            //            $query = DB::table(DB::raw("(" . $query3->toSql() . ") as res"))
            //                ->mergeBindings($query3->getQuery());
            $query->groupBy('added_by')
            ->select(['added_by','added_by as user_id'])
            ->addSelect([DB::raw("first_name as first_name"),DB::raw("last_name as last_name")])
            ->addSelect(DB::raw('count(*) as total_records'))
            ;
            $query->leftJoin('users', 'user_id', '=', 'users.id');
            $query->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id');
            $query->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id');
            $query->where('role_key','im_by');
            if(!empty($keywords)){
                $query->where('users.first_name', 'LIKE', "%$keywords%");
            }
            //$info->whereNull('home_information.deleted_at');
            //return $query->get();

        }

        else if(in_array($type, ['trustee_caller'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['trustee_caller', '=', $user_id];
            }

            $query = SaleDetailsModel::where($where)->select(['trustee_caller','trustee_caller as user_id'])->addSelect(DB::raw('count(*) as total_records'));
            $query->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query->whereNull('home_information.deleted_at');
            $query->with(['trustee_callers']);
            $query->whereNotNull('trustee_caller');
            $this->whereBetween($query, 'trustee_caller_date', $from, $to);
            if(!empty($keywords)){
              $query->leftJoin('users', 'sale_details.trustee_caller', '=', 'users.id');
              $query->where('users.first_name', 'LIKE', "%$keywords%");
            }
            $query->groupBy('trustee_caller');
        }

        else if(in_array($type, ['auction_by'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['auction_by', '=', $user_id];
            }

            $query = SaleDetailsModel::where($where)->select(['auction_by','auction_by as user_id'])->addSelect(DB::raw('count(*) as total_records'));
            $query->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query->whereNull('home_information.deleted_at');
            $query->whereNotNull('auction_by');
            $query->with(['auction']);
            $this->whereBetween($query, 'auction_date', $from, $to);
            if(!empty($keywords)){
              $query->leftJoin('users', 'sale_details.auction_by', '=', 'users.id');
              $query->where('users.first_name', 'LIKE', "%$keywords%");
            }
            $query->groupBy('auction_by');
        }

        else if(in_array($type, ['clear_report'])){

            $info = DocumentOwnerModel::select(["house_id"]);
            $result = $this->whereBetween($info, 'document_owner.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
            $resultInfo = $result->get();
           
            $houseIds=[];
            foreach($resultInfo as $key=>$property){
                if(array_key_exists($property->house_id, $houseIds)){
                    $houseIds[$property->house_id]=$houseIds[$property->house_id]+1;
                }else{
                    $houseIds[$property->house_id]=1;
                }
            }
            $house_ids =  array_keys($houseIds);
            $property_result = PropertyModel::select(["house_id","address","county","state"])
                            ->whereIn("house_id", $house_ids)->whereNull('deleted_at')->get();

            foreach($property_result as $property){
                if(array_key_exists($property->house_id, $houseIds)){
                    $property->clear_record=$houseIds[$property->house_id];
                }
            }
            return response()->json(['status' => 'success','reprot_type'=>$type,'data' => $property_result], 200);
        }
        

        $query->orderBy('total_records','desc');
        $query->skip(intval($offset))->take(intval($limit));

        $result = $query->get();

        $result = $result->map(function ($info) {
            $temp = @$info->user;
            if(empty($temp))
                $temp = @$info->nos;
            if(empty($temp))
                $temp = @$info->im;
            if(empty($temp))
                $temp = @$info->trustee;
            if(empty($temp))
                $temp = @$info->auction;

            if(!empty($temp))
            {
                $info->username = @$temp->username;
                $info->first_name = @$temp->first_name;
                $info->last_name = @$temp->last_name;
            }

            if(isset($info->user))
            $info->makeHidden('user');
            if(isset($info->nos))
            $info->makeHidden('nos');
            if(isset($info->url))
            $info->makeHidden('url');
            return $info;
        });

        $data = [];
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        $data['row'] = $result;
        Log::info("ReportController: index end");
        return response()->json(['status' => 'success','data' => $data], 200);
    }

    public function exportMe()
    {
        Log::info("ReportController: exportMe called");
        $uid  = $this->request->get('uid') ;
        $from  = $this->request->get('from') ;
        $to  = $this->request->get('to') ;
        $keywords  = $this->request->get('keywords') ? $this->request->get('keywords') : '';
        $type  = $this->request->get('user_type') ? $this->request->get('user_type') : '';
        if(empty($uid))
            $uid= $this->userService->user_id();

        $query = $this->exportMeViewMoreFilter();

        $result = $query->get();
        $total_records =  $result->count();
        $userInfo = $this->userService->findOneById($uid);

        $userName = CommonHelper::nameFormat($userInfo);
        $header = [
            ['Name',$userName,"","User Type", $type, "" ,""],
            ['From',$from,"","Total Records", $total_records, "" ," "],
            ['To',$to,"","", "", "" ," "],
            ['',"","","", "", "" ," "],

        ];

        // Calculate User Pay Rates
        $pay_rates = PaymentHelper::calculatePayRates($type, $userInfo);
        $header[] =  ["Work Profile",ucwords(str_replace("_"," ", @$userInfo->work_profile_team )),"User Type",ucwords(str_replace("_"," ", $type)), "", "","" ];

        $total_earnings = 0;
        $data = [];
        if(in_array($type, ['im_by']))
        {
            $header[] =  ['HouseId',"Address","County","State", "Date", "Price Per Record","Data Type" ,"Link","Image Link"];

            $rate = floatval(@$pay_rates);
            foreach($result as $key=>$value){
                $total_earnings = $total_earnings + $rate;

                $link = CommonHelper::showdetail_url($value->house_id, $value);
                if($value->data_type == 'sale')
                {
                    $folder_name = 'document_sale/';
                }
                else
                {
                    $folder_name = 'document_bidder/';
                }
                $url = CommonHelper::getPublicMediaURl($value->store_name, $folder_name);
                $data[] = [$value->house_id,$value->address,$value->county,$value->state
                    , CommonHelper::intToDate($value->date),"$".($rate),ucwords($value->data_type), $link, $url];
            }

            $header[] =  ['Total Record Earnings',"$".$total_earnings,"","", "", "" ,""];
            $header[] =  ['Internet Fees',"$".PaymentHelper::calculateInternetFees($type, $total_records),"","", "", "" ,""];
            $header[] =  ['',"","","", "", "" ,""];
            $header[] =  ['HouseId',"Address","County","State", "Date", "Price Per Record" ,"Link"];

        }
        else
        {
            $totalForeClosure = $totalTaxSale = $totalWithTitleTaxSale =$totalWithoutTitleTaxSale = "0";
            foreach($result as $key=>$value){
                $rate  = '0.00';
                $saleTypeVal = @$value->sale_type;
                $lenderInfo = @$value->lender;
                if(in_array($type, ['first_dtc','second_dca','third_dca','chief_dca'])){
                    $rate = floatval(@$pay_rates);
                    $saleTypeVal = @$value->last_sale_details->sale_type;
                    $lenderInfo = @$value->first_lien->lender;

                }
                $total_earnings = $total_earnings + $rate;
                $link = CommonHelper::showdetail_url($value['house_id'], $value);

                if($saleTypeVal == 6){
                    $totalTaxSale++;
                    if(!empty($lenderInfo)){
                        $totalWithTitleTaxSale++;
                    }else{
                        $totalWithoutTitleTaxSale++;
                    }
                }else{
                    $totalForeClosure++;
                }

                $sale_type_array                    = config('property_information.sale_type');
                $sale_type                          = @$sale_type_array[@$saleTypeVal];
                
                $data[] = [$value->house_id,$value->address,$value->county,$value->state, $value->date, $sale_type,"$".($rate) ,$link];
                
               
            }

            $header[] =  ['Total Record Earnings',"$".$total_earnings,"","", "", "" ,""];
            $header[] =  ['Internet Fees',"$".PaymentHelper::calculateInternetFees($type, $total_records),"","", "", "" ,""];
            $header[] =  ['',"","","", "", "" ,""];
            $header[] = ["Total Records",count($result)];
            $header[] = ["TOTAL FORECLOSURE",$totalForeClosure];
            $header[] = ["TOTAL TAX SALE",$totalTaxSale];
            $header[] = ["TAX SALE WITH TITLE/MORTGAGE",$totalWithTitleTaxSale];
            $header[] = ["TAX SALE WITH NO TITLE",$totalWithoutTitleTaxSale];
            $header[] =  ['',"","","", "", "" ,""];
            $header[] =  ['HouseId',"Address","County","State", "Date","Sale Type", "Price Per Record" ,"Link"];
        }

        $export = new CommonExport(array_merge($header,$data));

        #ToDo: temporary fix, deleteFileAfterSend: false creating a lot of temp files, Please fix this.
        #ToDo: make a history of all export or file download from all section.

        Log::info("ReportController: exportMe end");
        $info = $this->excel->download($export, $userName.'_'.$from.'_'.$to.'_'.time() . '.xlsx')
            ->deleteFileAfterSend(false);
        return $info;
    }

    public function viewMore()
    {
        Log::info("ReportController: viewMore called");
        $type  = $this->request->get('user_type') ? $this->request->get('user_type') : '';
        $uid  = $this->request->get('uid') ;

        if(empty($uid))
            $uid= $this->userService->user_id();

        $userInfo = $this->userService->findOneById($uid);

        $query = $this->exportMeViewMoreFilter();
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;

        $query->skip(intval($offset))->take(intval($limit));
        $result = $query->get();
        // Calculate User Pay Rates
        $pay_rates = PaymentHelper::calculatePayRates($type, $userInfo);
        $total_earnings = 0;
        $data = [];
        if(in_array($type, ['im_by']))
        {

            $rate = floatval(@$pay_rates);
            foreach($result as $key=>$value){
                $total_earnings = $total_earnings + $rate;

                $link = CommonHelper::showdetail_url($value->house_id, $value);
                if($value->data_type == 'sale')
                {
                    $folder_name = 'document_sale/';
                }
                else
                {
                    $folder_name = 'document_bidder/';
                }
                $url = CommonHelper::getPublicMediaURl($value->store_name, $folder_name);
                $data[] = [$value->house_id,$value->address,$value->county,$value->state, CommonHelper::intToDate($value->date), "$"."".$rate ,ucwords($value->data_type), $link, $url];
            }
        }
        else
        {
            foreach($result as $key=>$value){
                $sale_type = $value->sale_type;
                $rate  = '0.00';
                if(in_array($type, ['first_dtc','second_dca','third_dca','chief_dca'])){
                    $rate = floatval(@$pay_rates);
                    $sale_type = @$value->last_sale_details->sale_type;
                }

                $total_earnings = $total_earnings + $rate;
                $link = CommonHelper::showdetail_url($value['house_id'], $value);
                $data[] = [$value->house_id,$value->address,$value->county,$value->state, $value->date,$sale_type, "$".($rate) ,$link];
            }
        }

        return response()->json(['status' => 'success','data' => $data], 200);
    }


    private function exportMeViewMoreFilter()
    {
        $uid  = $this->request->get('uid') ;
        $from  = $this->request->get('from') ;
        $to  = $this->request->get('to') ;
        $keywords  = $this->request->get('keywords') ? $this->request->get('keywords') : '';
        $type  = $this->request->get('user_type') ? $this->request->get('user_type') : '';

        CommonHelper::startEndDateCheck($from,$to);
        $user_id = $this->userService->user_id();
        $where = [
        ];

        if($type == 'first_dtc'){
            $where[] = ['info_added_by', '=', 'first_dtc'];
        }
        else if($type == 'second_dca'){
            $where[] = ['info_added_by', '=', 'second_dca'];
        }
        else if($type == 'third_dca'){
            $where[] = ['info_added_by', '=', 'third_dca'];
        }
        else if($type == 'chief_dca'){
            $where[] = ['info_added_by', '=', 'final_dca'];
        }
        else if($type == 'nos_by')
        {}
        else if($type == 'im_by')
        {}
        else if($type == 'trustee_caller')
        {}
        else if($type == 'auction_by')
        {}
        else{
            return response()->json(['status' => 'failed','message'=>"Invalid request.",'data' => []], 200);
        }

        $query = '';
        if(in_array($type, ['first_dtc','second_dca','third_dca','chief_dca'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['user_id', '=', $user_id];
            }
            else{
                if(empty($uid))
                    $uid= $this->userService->user_id();
                $where[] = ['user_id', '=', $uid];
            }

            $query = PropertyModel::where($where)->select(['user_id','date']);
            $query->addSelect(['home_information.house_id','home_information.address','home_information.state','home_information.county','home_information.zip']);
            $query->join('cma_arv_recommendations', 'home_information.house_id', '=', 'cma_arv_recommendations.house_id');
            $query->with([ 
                "last_sale_details" => function ($query) {
                $query->select(['house_id', 'sale_id', 'sale_type']);
                },
                "first_liens"=> function ($query) {
                    $query->select(['house_id','lien_amount','lender']);
                },    
            ]);
            $query->distinct('home_information.house_id');
            $query->whereNull('home_information.deleted_at');
            $query->whereNotNull('home_information.address');
            $this->whereBetween($query, 'date', $from, $to);
            //$query->orderBy('cma_arv_recommendations.date','asc');
        }
        else if(in_array($type, ['nos_by'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['nos_by', '=', $user_id];
            }
            else{
                if(empty($uid))
                    $uid= $this->userService->user_id();
                $where[] = ['nos_by', '=', $uid];
            }

            $query = SaleDetailsModel::where($where)->select(['nos_by as user_id','nos_date as date','sale_type','lender']);
            $query->addSelect(['home_information.house_id','home_information.address','home_information.state','home_information.county','home_information.zip']);
            $query->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query->leftJoin('mortgage_liens', function($join){
                $join->on('home_information.house_id', '=', 'mortgage_liens.house_id');
                $join->where('mortgage_liens.lien_type','=', 1);
            });
            //$query->leftJoin(DB::raw("(SELECT sale_id,house_id,sale_type from sale_details where sale_date IS NOT NULL order by sale_id desc limit 0,1) as last_sale_date"),'home_information.house_id','=','last_sale_date.house_id' );
            $query->whereNull('home_information.deleted_at');
            $this->whereBetween($query, 'nos_date', $from, $to);
            $query->orderBy('sale_details.nos_date','asc');
        }
        else if(in_array($type, ['im_by'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['added_by', '=', $user_id];
            }
            else{
                if(empty($uid))
                    $uid= $this->userService->user_id();
                $where[] = ['added_by', '=', $uid];
            }

            $query1 = DocumentSaleModel::where($where)->select(['added_by as user_id','document_sale.created_at as date','store_name',DB::raw("'sale' as data_type")]);
            $query1->addSelect(['home_information.house_id','home_information.address','home_information.state','home_information.county','home_information.zip']);
            $query1->leftJoin('sale_details', 'sale_details.sale_id', '=', 'document_sale.sale_id');
            $query1->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query1->whereNull('home_information.deleted_at');
            $query1->whereNotNull('added_by');
            $this->whereBetween($query1, 'document_sale.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));



            $query2 = DocumentBidderModel::where($where)->select(['added_by as user_id','document_bidder.created_at as date','store_name',DB::raw("'bidder' as data_type")]);//->addSelect(DB::raw('count(*) as total_records'));
            $query2->addSelect(['home_information.house_id','home_information.address','home_information.state','home_information.county','home_information.zip']);
            $query2->leftJoin('sale_bidder', 'sale_bidder.bidder_id', '=', 'document_bidder.bidder_id');
            $query2->leftJoin('home_information', 'home_information.house_id', '=', 'sale_bidder.house_id');
            $query2->whereNull('home_information.deleted_at');
            $query2->whereNotNull('added_by');
            $query2->whereNull('sale_bidder.deleted_at');
            $this->whereBetween($query2, 'document_bidder.created_at', CommonHelper::strToTime($from),$this->getConvertedToDate($to));

            $query3 = $query2->unionAll($query1);
            $query = DB::query()->fromSub($query3, 'subquery');
            $query
                ->select(['user_id','date','store_name','data_type'])
                ->addSelect(['house_id','address','state','county','zip']);
            if(!empty($keywords)){
                //$query->where('users.first_name', 'LIKE', "%$keywords%");
            }
        }
        else if(in_array($type, ['trustee_caller'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['trustee_caller', '=', $user_id];
            }
            else{
                if(empty($uid))
                    $uid= $this->userService->user_id();
                $where[] = ['trustee_caller', '=', $uid];
            }

            $query = SaleDetailsModel::where($where)->select(['trustee_caller as user_id','trustee_caller_date as date','sale_type','lender']);
            $query->addSelect(['home_information.house_id','home_information.address','home_information.state','home_information.county','home_information.zip']);
            $query->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query->leftJoin('mortgage_liens', function($join){
                $join->on('home_information.house_id', '=', 'mortgage_liens.house_id');
                $join->where('mortgage_liens.lien_type','=', 1);
            });
            $query->whereNull('home_information.deleted_at');
            $this->whereBetween($query, 'trustee_caller_date', $from, $to);

            $query->orderBy('sale_details.trustee_caller_date','asc');
        }
        else if(in_array($type, ['auction_by'])){
            if(!$this->allUsersReportAccess()){
                $where[] = ['auction_by', '=', $user_id];
            }
            else{
                if(empty($uid))
                    $uid= $this->userService->user_id();
                $where[] = ['auction_by', '=', $uid];
            }

            $query = SaleDetailsModel::where($where)->select(['auction_by as user_id','auction_date as date','sale_type','lender']);
            $query->addSelect(['home_information.house_id','home_information.address','home_information.state','home_information.county','home_information.zip']);
            $query->leftJoin('home_information', 'home_information.house_id', '=', 'sale_details.house_id');
            $query->leftJoin('mortgage_liens', function($join){
                $join->on('home_information.house_id', '=', 'mortgage_liens.house_id');
                $join->where('mortgage_liens.lien_type','=', 1);
            });
            $query->whereNull('home_information.deleted_at');
            $this->whereBetween($query, 'auction_date', $from, $to);

            $query->orderBy('sale_details.auction_date','asc');
        }
        return $query;
    }

    /**
     * @param $query
     * @param $field
     * @param $from
     * @param $to
     * @return mixed
     */
    private function whereBetween($query, $field, $from, $to)
    {
        if (!empty($from))
            $query->where($field, '>=', $from);

        if (!empty($to))
            $query->where($field, '<=', $to);

        return $query;
    }

    function getAaReport(){

        $from  = $this->request->get('from') ;
        $to  = $this->request->get('to') ;
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $info=HouseBuyItModel::select([DB::raw('home_information.state,count(house_buyit.house_buyit_id) as total')])
              ->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id')
              ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id')
              ->groupBy('home_information.state');
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $info->whereNull('home_information.deleted_at');
        //$info = $this->where($info, 'users.status', 'active');
        $info = $this->where($info, 'buyit_designation.user_status', 'active');
        $this->whereBetween($info, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
        if ( !empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
            $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
        }
        
        $data=$info->get();

        return response()->json(['status' => 'success','data' => $data], 200);

    }

    function getCountyReport(){
        
        $info=HouseBuyItModel::select([DB::raw('home_information.state,home_information.county,count(house_buyit_id) as total')])
              ->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id')
              ->groupBy('home_information.state','home_information.county');
              $this->where($info, 'house_buyit.request_type', 'buyit');
              $info->whereNull('home_information.deleted_at');
        $data=$info->get();
        
        return response()->json(['status' => 'success','data' => $data], 200);

    }

    function getTotalBuyItbyBuyer(){

        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $info=HouseBuyItModel::select([DB::raw('users.id,users.first_name,users.last_name,count(house_buyit.house_buyit_id) as total')])
              ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
              ->groupBy('users.id')
              ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
              ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id');

        $this->whereBetween($info, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
        //$this->where($info, 'buyit_designation.designation', 'buyer');
        if ( !empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
            $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
        }
        $info = $this->where($info, 'users.status', 'active');
        $info = $this->where($info, 'buyit_designation.user_status', 'active');
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $info->whereNull('home_information.deleted_at');

        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;
        
        $info->skip(intval($offset))->take(intval($limit));
        $data=$info->get();

        return response()->json(['status' => 'success','data' => $data], 200);

    }

    function getTotalBuyItbySaleType(){
        
        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $query1 = HouseBuyItModel::select(["house_buyit.house_id"])->with([
            "last_sale_details" => function ($query) {
                $query->select(['house_id', 'sale_id', 'sale_date','sale_type' ]);
            }
            ])
            ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
            ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id');

            $this->whereBetween($query1, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
            $this->where($query1, 'house_buyit.request_type', 'buyit');

            if ( !empty($sale_date_from) || !empty($sale_date_to)) {
                $query1->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
                $query1 = $this->whereBetween($query1, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
            }
            //$this->where($query1, 'buyit_designation.designation', 'buyer');
            $query1->whereNull('home_information.deleted_at');
            $data=$query1->get();
        
            $data=$data->map(function ($info) {
                $temp=$info->last_sale_details;
                $info->makeHidden('last_sale_details');
                return $temp;
            });
        
       
        
            $collection=collect($data);
            $counted = $collection->countBy(function ($sale) {
                $aa_sale_type_array  = config('property_information.aa_report_sale_type');
                if(@$sale->sale_type && in_array($sale->sale_type,$aa_sale_type_array)){
                    return $sale->sale_type;
                }else{
                    return false;
                }
            });
            $counted->all();
            unset($counted[0]);
        
        return response()->json(['status' => 'success','data' => $counted], 200);

    }

    function getBuyItbyBuyerId(){

        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $id= $this->request->get('id');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $info = HouseBuyItModel::select([
            "house_buyit.position as old_position",
            "buyit_designation.designation",
            "buyit_designation.position",
            "house_buyit.user_id",
            "house_buyit.request_type",
            "house_buyit.created_at",
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip",
            "home_information.house_id"
        ])
        ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
        ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id')
        ->with(["last_sale_details" => function ($info){
            $info->select(['house_id', 'sale_id', 'sale_date','sale_type' ]);
            //$query->where('sale_type','=',$sale_type);
        }
        ]);
        $info = $this->where($info, 'buyit_designation.user_status', 'active');
        $info = $this->where($info, 'house_buyit.user_id', $id);
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $this->whereBetween($info, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        if ( !empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
            $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
        }
        //$info = $this->where($info, 'buyit_designation.designation', 'buyer');
        $info->whereNull('home_information.deleted_at');
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 100) $limit = 100;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;
        
        $info->skip(intval($offset))->take(intval($limit));
        $data=$info->get();

        return response()->json(['status' => 'success','data' => $data], 200);

    }

    public function getSaleTypeDetailAaReport(){
        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $sale_type= $this->request->get('sale_type');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $info = HouseBuyItModel::select([
            "house_buyit.position as old_position",
            "buyit_designation.designation",
            "buyit_designation.position",
            "house_buyit.user_id",
            "house_buyit.request_type",
            "house_buyit.created_at",
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip",
            "home_information.house_id",
            "users.first_name",
            "users.last_name"
        ])
        ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
        ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id')
        ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
        ->with(["last_sale_details" => function ($query) use($sale_type) {
                $query->select(['house_id', 'sale_id', 'sale_date','sale_type' ]);
                $query->where('sale_type','=',$sale_type);
            }
        ]);
       
        $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
        $info = $this->where($info, 'sale_details.sale_type', $sale_type);
        //$info = $this->where($info, 'buyit_designation.designation', 'buyer');
        $info = $this->where($info, 'users.status', 'active');
        $info = $this->where($info, 'buyit_designation.user_status', 'active');
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $this->whereBetween($info, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
        $info->whereNull('home_information.deleted_at');
        if ( !empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
            $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
        }
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 100) $limit = 100;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;
        
        $info->skip(intval($offset))->take(intval($limit));
        $data=$info->get();

        return response()->json(['status' => 'success','data' => $data], 200);
    }

    private function where($query, $field, $value, $condition = "=")
    {
        if (empty($value))
            return $query;
        return $query->where($field, $condition, $value);
    }

    function allUsersReportAccess(){
        
        $userReportAccess=[1859];

        if($this->userService->is_admin() 
        || $this->userService->is_accounting() 
        || $this->userService->is_chief_dca()
        || ($this->userService->is_first_dtc() && in_array($this->userService->user_id(),$userReportAccess))
        ){
            return true;
        }else{
            return false;
        }
    }

    function getConvertedToDate($to){
        return CommonHelper::strToTime($to.' 23:59:59');
    }


    public function exportSaleTypeDetailAaReport(){
        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $sale_type= $this->request->get('sale_type');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $info = HouseBuyItModel::select([
            "house_buyit.position as old_position",
            "buyit_designation.designation",
            "buyit_designation.position",
            "house_buyit.user_id",
            "house_buyit.request_type",
            "house_buyit.created_at",
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip",
            "home_information.house_id",
            "users.first_name",
            "users.last_name"
        ])
        ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
        ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id')
        ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
        ->with(["last_sale_details" => function ($query) use($sale_type) {
                $query->select(['house_id', 'sale_id', 'sale_date','sale_type' ]);
                $query->where('sale_type','=',$sale_type);
            }
        ]);
       
        $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
        $info = $this->where($info, 'sale_details.sale_type', $sale_type);
        //$info = $this->where($info, 'buyit_designation.designation', 'buyer');
        $info = $this->where($info, 'users.status', 'active');
        $info = $this->where($info, 'buyit_designation.user_status', 'active');
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $this->whereBetween($info, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
        $info->whereNull('home_information.deleted_at');
        if ( !empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
            $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
        }
        // $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        // if($limit > 100) $limit = 100;
        // $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;
        
       // $info->skip(intval($offset))->take(intval($limit));
        $data=$info->get();

        if(!empty($data)){
            $header   = [];
            $header[] = "Property Address";
            $header[] = "County";
            $header[] = "City";
            $header[] = "State";
            $header[] = "Zip";
            $header[] = "First Name";
            $header[] = "Last Name";
            $header[] = "Designation";
            $header[] = "Sale Date";
          
            $temp[]   = $header;
            foreach($data as $sale){
                $parse_data = [];
                $parse_data['property_address'] = $sale->address;
                $parse_data['county'] = $sale->county;
                $parse_data['city'] = $sale->city;
                $parse_data['state'] = $sale->state;
                $parse_data['zip'] = $sale->zip;
                $parse_data['first_name'] = $sale->first_name;
                $parse_data['last_name'] = $sale->last_name;
                $parse_data['designation'] = $sale->designation;
                $parse_data['sale_date'] = $sale->last_sale_details->sale_date;
                $temp[]  = $parse_data;
            }
        }
        $str = CommonHelper::arrayToCSV($temp);
        return response($str, 200)
        ->header('Content-Type', 'application/csv')
        ->header('Content-Disposition', 'attachment; filename=ES_15643723071.csv');
    }

    public function exportBuyitAaReport(){
        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $id= $this->request->get('id');
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');

        $info = HouseBuyItModel::select([
            "house_buyit.position as old_position",
            "buyit_designation.designation",
            "buyit_designation.position",
            "house_buyit.user_id",
            "house_buyit.request_type",
            "house_buyit.created_at",
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip",
            "home_information.house_id",  
            "users.first_name",
            "users.last_name"
        ])
        ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
        ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id')
        ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
        ->with(["last_sale_details" => function ($info){
            $info->select(['house_id', 'sale_id', 'sale_date','sale_type' ]);
            //$query->where('sale_type','=',$sale_type);
        }
        ]);
        $info = $this->where($info, 'buyit_designation.user_status', 'active');
        $info = $this->where($info, 'house_buyit.user_id', $id);
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $this->whereBetween($info, 'house_buyit.created_at', CommonHelper::strToTime($from), $this->getConvertedToDate($to));
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        if ( !empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin(DB::raw("(SELECT house_id, MAX(sale_date) as sale_date FROM sale_details where sale_date is not null group by house_id having max(sale_date) between '$sale_date_from' AND '$sale_date_to') as latestSaleDate"), 'home_information.house_id', '=', 'latestSaleDate.house_id');
            $info = $this->whereBetween($info, 'latestSaleDate.sale_date', $sale_date_from, $sale_date_to);
        }
        //$info = $this->where($info, 'buyit_designation.designation', 'buyer');
        $info->whereNull('home_information.deleted_at');
        $data=$info->get();

        if(!empty($data)){
            $header   = [];
            $header[] = "Property Address";
            $header[] = "County";
            $header[] = "City";
            $header[] = "State";
            $header[] = "Zip";
            $header[] = "First Name";
            $header[] = "Last Name";
            $header[] = "Designation";
            $header[] = "Sale Date";
            $header[] = "Position";
            $temp[]   = $header;
            foreach($data as $sale){
                $parse_data = [];
                $parse_data['property_address'] = $sale->address;
                $parse_data['county'] = $sale->county;
                $parse_data['city'] = $sale->city;
                $parse_data['state'] = $sale->state;
                $parse_data['zip'] = $sale->zip;
                $parse_data['first_name'] = $sale->first_name;
                $parse_data['last_name'] = $sale->last_name;
                $parse_data['designation'] = $sale->designation;
                $parse_data['sale_date'] = $sale->last_sale_details->sale_date;
                $parse_data['position'] = $sale->position;

                $temp[]  = $parse_data;
            }
        }
        $str = CommonHelper::arrayToCSV($temp);
        return response($str, 200)
        ->header('Content-Type', 'application/csv')
        ->header('Content-Disposition', 'attachment; filename=ES_15643723071.csv');
    }


    function getCountyByReport(){

      
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');
        $state       = $this->request->get('state');

        $result=SaleDetailsModel::select(['home_information.house_id','sale_date','sale_type'])
                ->join('home_information','home_information.house_id','sale_details.house_id')
                ->whereNotNull('sale_date')
                ->where('state', $state)
                ->where('sale_date' ,'>=',$sale_date_from)
                ->where('sale_date' ,'<=',$sale_date_to)
                ->distinct()->get();

        $houseIds=[];
        foreach($result as $data){
            $houseIds[]=$data->house_id;

        }
        $info = PropertyModel::select([DB::raw('home_information.county,home_information.house_id')]);
        $info->whereIn("home_information.house_id", $houseIds)
            ->whereNull('home_information.deleted_at')
            ->where('state', $state);

              $info->with([
                "first_liens"  => function ($query){
                    $query->select(['house_id',
                        'lender',
                        'lien_amount',
                        'loan_type',
                        'lien_foreclosing']);
                },
                "second_liens" => function ($query) {
                    $query->select(['house_id',
                        'lender',
                        'lien_amount',
                        'loan_type',
                        'lien_foreclosing']);
                },
                "third_liens" => function ($query) {
                    $query->select(['house_id',
                        'lender',
                        'lien_amount',
                        'loan_type',
                        'lien_foreclosing']);
                },
                "hoa_liens" => function ($query) {
                    $query->select(['house_id',
                        'hoa_lien_amount',
                        ]);
                },
                "tax_liens" => function ($query) {
                    $query->select(['house_id',
                        'date_of_tax_lien',
                        ]);
                },
                "subto_property"=> function ($query) {
                    $query->select(['house_id',
                        'sub_to_property',
                    ]);
                },
                ]);
        $data=$info->get();

        $finalResult=[]; 
        foreach($data as $result){
          
            $countyName=$result->county;
            
            if(array_key_exists($countyName,$finalResult)){
                $finalResult[$countyName]['total']=(!empty($finalResult[$countyName]['total'])?$finalResult[$countyName]['total']:0)+1;
            }else{
                $finalResult[$countyName]=['county'=>$countyName,
                'first_liens'=>0,'inferior_liens'=>0,
                'hoa_liens'=>0,'tax_liens'=>0,'sub_to'=>0,'subto_property'=>0
                ];
                $finalResult[$countyName]['total']=1;
            }

            if(!empty($result->first_liens->lien_foreclosing)){
                $finalResult[$countyName]['first_liens']=$finalResult[$countyName]['first_liens']+1;
            }else{
                $finalResult[$countyName]['inferior_liens']=$finalResult[$countyName]['inferior_liens']+1;
            }
            if(!empty($result->hoa_liens)){
                $finalResult[$countyName]['hoa_liens']=$finalResult[$countyName]['hoa_liens']+1;
            }
            if(!empty($result->tax_liens)){
                $finalResult[$countyName]['tax_liens']=$finalResult[$countyName]['tax_liens']+1;
            }
            if(!empty($result->subto_property->sub_to_property)){
                $finalResult[$countyName]['subto_property']=$finalResult[$countyName]['subto_property']+1;
            }
        }
        return response()->json(['status' => 'success','data' => array_values($finalResult)], 200);

    }

    public function exportyCountyReport(){
       
        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');
        $state       = $this->request->get('state');
        $county       = $this->request->get('county');

        $result=SaleDetailsModel::select(['home_information.house_id'])
                ->join('home_information','home_information.house_id','sale_details.house_id')
                ->whereNotNull('sale_date')
                ->where('state', $state)
                ->where('county', $county)
                ->where('sale_date' ,'>=',$sale_date_from)
                ->where('sale_date' ,'<=',$sale_date_to)
                ->distinct()->get();

        $houseIds=[];
        foreach($result as $data){
            $houseIds[]=$data->house_id;
        }

        $info = PropertyModel::select(["home_information.house_id",
                "home_information.address",
                "home_information.city",
                "home_information.county",
                'home_information.year_built',
                'home_information.specific_property_type'
            ]);

        $info->whereIn("home_information.house_id", $houseIds);
        $info->with([
            "last_sale_details"=> function ($query) {
                $query->select(['house_id', 'sale_id','sale_date','sale_time',
                    'sale_place','case_number','opening_bid','sale_type','trustee']);
            },
            "property_descriptions",
            "last_cma_arv_recommendations" => function ($query) {
                $query->select(['house_id','recommended_cma_arv']);
            },
            "last_owner_info" =>   function ($query) {
                $query->select(['house_id','full_name','full_address','phone']);
            },
            "first_liens" => function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'amortization_loan_estimate_balance',
                    'total_est_debt',
                    'no_str_no_appt']);
            },
            "second_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'amortization_loan_estimate_balance',
                    'total_est_debt',
                    'no_str_no_appt']);
            },
            "third_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'amortization_loan_estimate_balance',
                    'total_est_debt',
                    'no_str_no_appt']);
            },
            "local_real_estate_details"=> function ($query) {
                $query->select(['house_id',
                                'zestimate'
                               ]);
            }
        ]);

        $records=$info->get();
        if(!empty($records)){
            $header   = [];
            $header[] = "Trustee";
            $header[] = "Sale Date";
            $header[] = "Sale Type";
            $header[] = "Street Name";
            $header[] = "County";
            $header[] = "Year Built";
            $header[] = "Specific Property Type";
            $header[] = "Legal Description";
            $header[] = "Owner 1";
            $header[] = "CMA/ARV";
            $header[] = "Zestimate";
            $header[] = "First Lien No STR flag";
            $header[] = "Loan Estimated Balance First Lien";
            $header[] = "First Lien Total Estimated Debt w/ Late Payments & Attorney Fees $";
            $header[] = "Second Lien No STR flag";
            $header[] = "Loan Estimated Balance Second Lien";
            $header[] = "Second Lien Total Estimated Debt w/ Late Payments & Attorney Fees $";
            $header[] = "Third Lien No STR flag";
            $header[] = "Loan Estimated Balance Third Lien";
            $header[] = "Third Lien Total Estimated Debt w/ Late Payments & Attorney Fees $";
            $header[] = "Amount Owed HOA Lien";
            $header[] = "Amount Owed Taxes";
            $header[] = "Property URL"; 
            $header[] = "Number of NOSs";
            $temp[]   = $header;
            foreach($records as $value){
                $link        = env("APP_FRONTEND") . 'home/showdetail/' . $value->house_id . '/' . CommonHelper::url_slug($value);

                $parse_data = [];
                $parse_data['Trustee']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
                $parse_data['sale_date']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
                $sale_type_array                    = config('property_information.sale_type');
                $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
                $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
                $parse_data['property_address'] = $value->address;
                $parse_data['county'] = $value->county;
                $parse_data['year_built'] = $value->year_built;
                $property_type_array         = config('property_information.specific_property_types');
                $property_types_array         = config('property_information.property_types');
    
                $spPropertyType='N/A';
                if(!empty($value->property_type)){
                    $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                    $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                    $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];
    
                }
                $parse_data['specific_property_type'] =$spPropertyType;

                $parse_data['legal_description'] = $value->property_descriptions->legal_description;
                $parse_data['Owner'] = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);;
                $parse_data['recommended_cma_arv'] =  CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');;
                $parse_data['zestimates']         = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'zestimate', 'currency');
                $parse_data['1st STR Y/N']  = @$value->first_liens->no_str_no_appt?'Yes':'No';
                $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
                $parse_data['first_total_est_debt'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'total_est_debt', 'currency');
                $parse_data['2nd STR Y/N']  = @$value->second_liens->no_str_no_appt?'Yes':'No';
                $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
                $parse_data['second_total_est_debt'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'total_est_debt', 'currency');
                $parse_data['3rd STR Y/N']  = @$value->third_liens->no_str_no_appt?'Yes':'No';
                $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
                $parse_data['third_total_est_debt'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'total_est_debt', 'currency');
                $parse_data['Amount Owed HOA Lien'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
                $parse_data['Amount Owed Taxes']=$this->propertyService->getSumPropertyOwed($value->house_id); 
                $parse_data['Property URL'] = $link;
                $parse_data['number_of_nos'] = $this->propertyService->getSaleDocumentByHouseId($value->house_id);
                $temp[]  = $parse_data;
            }
        }
        $str = CommonHelper::arrayToCSV($temp);
        return response($str, 200)
        ->header('Content-Type', 'application/csv')
        ->header('Content-Disposition', 'attachment; filename=ES_15643723071.csv');
    }

    function monthlyReport(){
        $state       = $this->request->get('state');
        $county       = $this->request->get('county');
        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $result = $this->reportService->getMonthlyReport($state,$county,$from,$to);
        return response()->json(['status' => 'success','data' => $result], 200);

    }

    public function exportMonthlyReport(){
       
        $state       = $this->request->get('state');
        $county       = $this->request->get('county');
        $from  = $this->request->get('from');
        $to  = $this->request->get('to');
        $records = $this->reportService->getMonthlyReport($state,$county,$from,$to);
        
        if(!empty($records)){
            $header   = [];
            $header[] = "Month";
            $header[] = "Total Estimated Sale";	
            $header[] = "Total Actual Sale";	
            $header[] = "Total Estimated 1st Lien Frcl Sale";	
            $header[] = "Total Actual 1st Lien frcl sale";
            $header[] = "Total Estimated 2nd Lien Frcl Sale";	
            $header[] = "Total Actual 2nd Lien frcl sale";
            $header[] = "Total Estimated 3rd Lien Frcl Sale";	
            $header[] = "Total Actual 3rd Lien Frcl Sale";
            $header[] = "Total Estimated HOA Sale";
            $header[] = "Total Actual HOA sale";
            $header[] = "Total Estimated Tax Sale";	
            $header[] = "Total Actual Tax sale";
            $header[] = "No Sale Type of Estimated Sale";	
            $header[] = "No Sale Type of ActualSale";
            $header[] = "Estimated Timeshare Sale";
            $header[] = "Actual Timeshare Sale";
            $temp[]   = $header;
            foreach($records as $value){
                
                $parse_data = [];
                $parse_data['month']                                    = CommonHelper::emptyDefault(@$value->months,0);
                $parse_data['total_estimated_sale']                     = CommonHelper::emptyDefault(@$value->total_estimated_sale,0);
                $parse_data['total_atual_estimated_sale']               = CommonHelper::emptyDefault(@$value->total_atual_estimated_sale,0);
                $parse_data['total_est_1st_lien_frcl_sale']             = CommonHelper::emptyDefault(@$value->total_est_1st_lien_frcl_sale,0);;
                $parse_data['total_actual_est_1st_lien_frcl_sale']      = CommonHelper::emptyDefault(@$value->total_actual_est_1st_lien_frcl_sale,0);
                $parse_data['total_est_2nd_lien_frcl_sale']             = CommonHelper::emptyDefault(@$value->total_est_2nd_lien_frcl_sale,0);
                $parse_data['total_actual_est_2nd_lien_frcl_sale']      = CommonHelper::emptyDefault(@$value->total_actual_est_2nd_lien_frcl_sale,0);
                $parse_data['total_est_3rd_lien_frcl_sale']             = CommonHelper::emptyDefault(@$value->total_est_3rd_lien_frcl_sale,0);
                $parse_data['total_actual_est_3rd_lien_frcl_sale']      = CommonHelper::emptyDefault(@$value->total_actual_est_3rd_lien_frcl_sale,0);
                $parse_data['hoa_sale']                                 = CommonHelper::emptyDefault(@$value->hoa_sale,0);
                $parse_data['total_actual_hoa_sale']                    = CommonHelper::emptyDefault(@$value->total_actual_hoa_sale,0);
                $parse_data['tax_sale']                                 = CommonHelper::emptyDefault(@$value->tax_sale,0);
                $parse_data['total_actual_tax_sale']                    = CommonHelper::emptyDefault(@$value->total_actual_tax_sale,0); 
                $parse_data['total_no_sale_type_of_est_sale']           = CommonHelper::emptyDefault(@$value->total_no_sale_type_of_est_sale,0);
                $parse_data['total_actual_no_sale_type_of_est_sale']    = CommonHelper::emptyDefault(@$value->total_actual_no_sale_type_of_est_sale,0);
                $parse_data['total_est_timeshare_sale']                 = CommonHelper::emptyDefault(@$value->total_est_timeshare_sale,0);
                $parse_data['total_actual_est_timeshare_sale']          = CommonHelper::emptyDefault(@$value->total_actual_est_timeshare_sale,0);
                
                $temp[]  = $parse_data;
            }
        }
        $str = CommonHelper::arrayToCSV($temp);
        return response($str, 200)
        ->header('Content-Type', 'application/csv')
        ->header('Content-Disposition', 'attachment; filename=GLPF_Monthly_REPORT_15643723071.csv');
    }
}
