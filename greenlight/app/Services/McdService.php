<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:41 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/31/18 1:18 AM
 */

namespace App\Services;
use App\Models\McdModel;
use App\Models\McdLenderModel;
use App\Models\LenderStatementModel;
use App\Models\ShortTermRentalModel;
use App\Models\TradesmanTrackingModel;
use App\Models\BankStatementModel;
use App\Models\DepositSpreadsheetModel;
use App\Models\PayoutDetailModel;
use App\Models\DepositLenderModel;
use App\Models\DepositSheetLenderModel;
use App\Models\DepositSheetLinkModel;
use App\Models\TradeshmanUserModel;
use App\Models\mcdOtherInfoModel;
use App\Models\ClientInfoModel;
use App\Models\PayersInfoModel;
use App\Models\PayoutModel;
use App\Models\PropertyModel;
use App\Models\McdUsersModel;

use Log;
use DB;

class McdService
{
    private $findOneById;
    private $findAllByHouseId;
    private $findByLenderStatementId;
    private $findByLenderStatementByHouseId;
    private $findByTradesmanTrackingId;
    private $findShortTermRentalById;
    private $findByBankStatementId;
    
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        Log::info("McdService: __construct called");
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false)
    {
        Log::info("McdService: findOneById called");
        if($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById =  McdModel::with(['mcdLender'])->find($id);
        return $this->findOneById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAllByHouseId($house_id, $is_cache = false)
    {
        Log::info("McdService: findAllByHouseId called");
        if($is_cache == true)
        {
            return $this->findAllByHouseId;
        }

        $this->findAllByHouseId =  McdModel::where('house_id',$house_id)
                                   ->with(['mcdLender'])->get();
        return $this->findAllByHouseId;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreate($id, $info){
        Log::info("McdService: updateCreate called");

       
        if($info['out_date']==='null'){
            unset($info['out_date']);
        }
          

        //print_r($info);
        $info= McdModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        unset($info['house_id']);

        $info = $this->findOneById($info->id, false);
        return $info;
    }

    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function createOwnerInfo($info){
        Log::info("OwnerService: createOwnerInfo called");

        return McdModel::create($info);
    }

    public function updateCreateMcdLender($id, $info){
        Log::info("McdService: updateCreate called");

      //  unset($info['house_id']);
        //$info['house_id'] = $house_id;
        return McdLenderModel::updateOrCreate(["id" => $id], $info);

        # we don't want to update houseID through input
        unset($updateInfo['house_id']);

        $info = $this->findMCDLenderOneById($id, false);
        return $info;
    }
    
    public function findMCDLenderOneById($id, $is_cache = false)
    {
        return McdLenderModel::find($id);
    }

    public function getMcdLender($house_id){
        return McdLenderModel::where('house_id',$house_id)->get();
    }

    public function getMcdLenderlist($house_id){
        return McdLenderModel::select(['id','lender_name'])->where('house_id',$house_id)->get();
    }

    public function getTotalMcdBylender($house_id,$lender_id=""){
        

        $query1=McdLenderModel::select(['mcd_lender.id as lender',
                                        DB::raw("IF(SUM(client_renovation_detail.amount) IS NULL,0,SUM(client_renovation_detail.amount)) as amount"),
                                        DB::raw('IF(client_renovation.total_amount IS NULL,0,client_renovation.total_amount) as returned_amount '),
                                        DB::raw('0 as pts'),
                                        DB::raw('0 as interest'),
                                        DB::raw('0 as lender_referral')
                                        ])
                    ->leftJoin(DB::raw("(SELECT funder,house_id,SUM(client_renovation.amount) as total_amount FROM client_renovation where house_id=$house_id group by funder)  as client_renovation"), 'client_renovation.funder', '=', 'mcd_lender.id')
                    ->leftJoin('client_renovation as clientReno', 'clientReno.funder', '=', 'mcd_lender.id')
                    ->leftJoin('client_renovation_detail', 'client_renovation_detail.invoice_id', '=', 'clientReno.id')
                    ->where('mcd_lender.house_id', $house_id)
                    ->groupBy('mcd_lender.id');
        
        $query2= McdModel::select('house_mcd.lender_name as lender',
                                DB::raw('sum(house_mcd.amount) as amount'),
                                DB::raw('sum(house_mcd.returned_amount) as returned_amount'),
                                DB::raw('sum(house_mcd.pts) as pts'),
                                DB::raw('sum(house_mcd.interest) as interest'),
                                DB::raw('sum(house_mcd.lender_referral) as lender_referral'));
        
        $query2= $query2->leftJoin('mcd_lender', 'house_mcd.lender_name', '=', 'mcd_lender.id')->where('house_mcd.house_id',$house_id);
        if(!empty($lender_id)){
            $query2= $query2->where('house_mcd.lender_name',$lender_id);
        }
        $query2= $query2->groupBy('house_mcd.lender_name');
        
        $query3 = $query2->unionAll($query1);


        $query = DB::query()->fromSub($query3, 'subquery');
        $query = $query->select('mcd_lender.*','lender','mcd_lender.lender_name',
                                DB::raw('sum(subquery.amount) as amount'),
                                DB::raw('sum(subquery.returned_amount) as returned_amount'),
                                DB::raw('sum(subquery.pts) as pts'),
                                DB::raw('sum(subquery.interest) as interest'),
                                DB::raw('sum(subquery.lender_referral) as lender_referral'));
        $query= $query->leftJoin('mcd_lender', 'subquery.lender', '=', 'mcd_lender.id')->where('mcd_lender.house_id',$house_id);
        $query= $query->groupBy('subquery.lender');
        $query= $query->orderBy('mcd_lender.percentage','desc');
        $query= $query->orderBy('mcd_lender.is_lender','asc');
        return  $query->get();
    }

    public function getNetProfit($house_id){
        
        $info = DB::query()->from('payout')->select(["payout.house_id"]);
        $info->addSelect(DB::raw("@totalBtoC:=((selling_price_btoc-temp.total_btoc)+temp6.total_adj_sett_btoc) as total_b_to_c_sale"));
        $info->addSelect(DB::raw("@totalOther:=(IFNULL(temp2.totalAmount,0)+IFNULL(temp4.amount_received,0)) as otherAmount"));
        $info->addSelect(DB::raw("@totalAtoB:=(IFNULL(purchase_price,0)+temp3.total_atob+COALESCE(aa_fee, 0)+COALESCE(buyer_ref_fee,0)+COALESCE(travel_office_fee,0)+COALESCE(labor_charges,0)+COALESCE(office_fee,0)+COALESCE(bookkeeping_fee,0)+COALESCE(assignment_fee,0)+totalNonHud+COALESCE(bonus_on_spread,0)+COALESCE(web_fee,0)) as total_a_to_b"));
        
        $info->addSelect(DB::raw("@preNetProfit:= CAST((@totalBtoC+@totalOther)-(@totalAtoB) as DECIMAL(12,2)) as preNetProfit"));
   // $info->addSelect(DB::raw("@netProfitBeforeFinancing:= CAST((@preNetProfit-@webFee-@totalBonus) as DECIMAL(12,2)) as netProfitBeforeFinancing"));
        $info->addSelect(DB::raw("@netProfit:= CAST((@preNetProfit-payout.financing_cost) as DECIMAL(12,2)) as netProfit"));
        
        // $info->addSelect(DB::raw("IFNULL(purchase_price,0)"),
        // DB::raw("COALESCE(aa_fee, 0)"),
        // DB::raw("COALESCE(buyer_ref_fee,0)"),
        // DB::raw("COALESCE(travel_office_fee,0)"),
        // DB::raw("COALESCE(labor_charges,0)"),
        // DB::raw("COALESCE(office_fee,0)"),
        // DB::raw("COALESCE(bookkeeping_fee,0)"),
        // DB::raw("COALESCE(assignment_fee,0)"),
        // DB::raw("totalNonHud"),DB::raw("COALESCE(bonus_on_spread,0)"),
        // );
        
        // $info->addSelect(DB::raw("@preNetProfit:= CAST((@totalBtoC+@totalOther)-(@totalAtoB) as DECIMAL(12,2)) as preNetProfit"));
        // $info->addSelect(DB::raw("@totalBonus:= IF(@preNetProfit>5000,(( FLOOR(@preNetProfit/10000)+1)*500-250),0) as bonusOnSpread"));
        // $info->addSelect(DB::raw("@webFee:= (CAST(IF(@preNetProfit>21000,1000,0) as DECIMAL(12,2))) as webFee"));
        // $info->addSelect(DB::raw("@netProfitBeforeFinancing:= CAST((@preNetProfit-@webFee-@totalBonus) as DECIMAL(12,2)) as netProfitBeforeFinancing"));
        // $info->addSelect(DB::raw("@netProfit:= CAST((@netProfitBeforeFinancing-payout.financing_cost) as DECIMAL(12,2)) as netProfit"));
        
        $info->leftJoin(DB::raw("(SELECT house_id,SUM((case when (payout_type = 'b_to_c') then amount else 0 end)) as total_btoc FROM `payout_detail`WHERE house_id=$house_id) as temp"), 'temp.house_id', '=', 'payout.house_id');
        $info->leftJoin(DB::raw("(SELECT house_id,SUM((case when (payout_type='btoc_settlement' OR payout_type='btoc_adjustment') then amount else 0 end)) as total_adj_sett_btoc FROM `payout_detail`WHERE house_id=$house_id) as temp6"), 'temp6.house_id', '=', 'payout.house_id');
        $info->leftJoin(DB::raw("(SELECT house_id,sum(field_value) as totalAmount FROM additional_field WHERE house_id=$house_id AND lender_id=0) AS temp2"), 'temp2.house_id', '=', 'payout.house_id');
        $info->leftJoin(DB::raw("(SELECT house_id,SUM((case when (payout_type = 'a_to_b' OR payout_type='settlement' OR payout_type='adjustment') then amount else 0 end)) as total_atob FROM `payout_detail`WHERE house_id=$house_id) as temp3"), 'temp3.house_id', '=', 'payout.house_id');
        $info->leftJoin(DB::raw("(SELECT house_id,SUM(amount_received) as amount_received  FROM `short_term_rental` WHERE house_id=$house_id and rental_type='rental') As temp4"), 'temp4.house_id', '=', 'payout.house_id');
        $info->leftJoin(DB::raw("(SELECT house_id,SUM(amount) as totalNonHud FROM `client_renovation_detail` WHERE house_id=$house_id) AS temp5"), 'temp5.house_id', '=', 'payout.house_id');
        $info->where('payout.house_id',$house_id);
        
        return $info->first();
        

    }

    public function getMcdLenderDetail($house_id){
        return McdLenderModel::with(['invenstorInfo'=>function($query) use ($house_id){
            $query->where('house_id',$house_id);
        }])->where('house_id',$house_id)->get();
    }


     /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateLenderStatement($id, $info){
        Log::info("McdService: updateCreateLenderStatement called");
       
        $info= LenderStatementModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        unset($info['house_id']);

        $info = $this->findLenderStatementById($info->id, false);
        return $info;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findLenderStatementById($id, $is_cache = false)
    {
        Log::info("McdService: findByLenderStatementId called");
        if($is_cache == true)
        {
            return $this->findByLenderStatementId;
        }

        $this->findByLenderStatementId =  LenderStatementModel::find($id);
        return $this->findByLenderStatementId;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getLenderStatement($house_id, $is_cache = false)
    {
        Log::info("McdService: findByLenderStatementId called");
        if($is_cache == true)
        {
            return $this->findByLenderStatementByHouseId;
        }
        $this->findByLenderStatementByHouseId =  LenderStatementModel::where('house_id',$house_id)->get();
        return $this->findByLenderStatementByHouseId;
    }
    
    /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateShortTermRental($id, $info){
        Log::info("McdService: updateCreateShortTermRental called");

        if($id) $info['updated_at']=date('Y-m-d H:i:s');
        //print_r($info);
        $info= ShortTermRentalModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        unset($info['house_id']);

        $info = $this->findShortTermRentalById($info->id, false);
        return $info;
    }

    public function findShortTermRentalById($id, $is_cache = false)
    {
        Log::info("McdService: findShortTermRentalById called");
        if($is_cache == true)
        {
            return $this->findShortTermRentalById;
        }

        $this->findShortTermRentalById =  ShortTermRentalModel::find($id);
        return $this->findShortTermRentalById;
    }

    public function getShortTermRentalInfo($house_id){
        return ShortTermRentalModel::where('house_id',$house_id)->get();
    }
    
     /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateTradesmanTracking($id, $info){

        Log::info("McdService: updateCreateTradesmanTracking called");
       
        $info= TradesmanTrackingModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        unset($info['house_id']);

        $info = $this->findTradesmanTrackingById($info->id, false);
        return $info;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findTradesmanTrackingById($id, $is_cache = false)
    {
        Log::info("McdService: findTradesmanTrackingById called");
        if($is_cache == true)
        {
            return $this->findByTradesmanTrackingId;
        }

        $this->findByTradesmanTrackingId =  TradesmanTrackingModel::with(['user','user.wInfo'=> function ($query) {
            $query->select(['user_id',
                        DB::raw("social_security_number_3 as social_security_number")
                       ]);
            }])->find($id);
        return $this->findByTradesmanTrackingId;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getTradesmanTrackingByHouseId($house_id){
        return TradesmanTrackingModel::with(['user','user.wInfo'=> function ($query) {
            $query->select(['user_id',
                        DB::raw("social_security_number_3 as social_security_number")
                       ]);
            }])->where('house_id',$house_id)->get();
    }
 /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getTradesmanTrackingByUser($house_id,$userId=""){
        $summery=TradesmanTrackingModel::select(['name',DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `total_work_hours` ) ) ) AS timeSum')])
        ->with(['user'])->groupBy('name');
        if(!empty($userId)){
            $summery= $summery->where('name',$userId); 
        }
        $summery= $summery->where('house_id',$house_id)->get();
        return $summery;
    }
    public function getTradeshmanUser($id=""){
        $info= TradeshmanUserModel::with(['wInfo'=> function ($query) {
                $query->select(['user_id',
                            DB::raw("social_security_number_3 as social_security_number")
                           ]);
                }]);

        if(!empty($id)){
            $info=$info->where('id',$id);
        }
        $info=$info->get();
        return $info;
    }
     /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateBankStatement($id, $info){

        Log::info("McdService: updateCreateBankStatement called");
       
        $info= BankStatementModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        unset($info['house_id']);

        $info = $this->findBrankStatementById($info->id, false);
        return $info;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findBrankStatementById($id, $is_cache = false)
    {
        Log::info("McdService: findBrankStatementById called");
        if($is_cache == true)
        {
            return $this->findByBankStatementId;
        }

        $this->findByBankStatementId =  BankStatementModel::find($id);
        return $this->findByBankStatementId;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getBankStatementByHouseId($house_id){
        return BankStatementModel::where('house_id',$house_id)->get();
    }

     /**
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateCreateDepositSpreadSheet($id, $info){

        Log::info("McdService: updateCreateBankStatement called");
       
        $info= DepositSpreadsheetModel::updateOrCreate(["id" => $id], $info);
        # we don't want to update houseID through input
        //unset($info['house_id']);

        //$info = $this->findBrankStatementById($info->id, false);
        return $info;
    }
     /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function getDepositSpreadSheetByHouseId($house_id){
        
        return DepositSpreadsheetModel::with(['deposit_lender','deposit_lender.lender','deposit_link'])->where('house_id',$house_id)->get();
    }

    public function getDepositLenderCat($house_id){
        
        return DepositSheetLenderModel::select(['deposit_lender.id','deposit_lender.lender_name'])->distinct()
               ->leftjoin('deposit_lender','deposit_lender.id','deposit_sheet_lender.lender_id')->where('house_id',$house_id)->get();
    }

    public function getDepositLinkCat($house_id){
        
        return DepositSheetLinkModel::select(['link_name'])->distinct()->where('house_id',$house_id)->get(['link_name']);
    }


    public function updateCreateDepositLender($id, $info){
        $info= DepositLenderModel::updateOrCreate(["id" => $id], $info);
        return $info;
    }

    public function getDepositLender(){
        return DepositLenderModel::get();
    }

    function updateCreateDepositSheetLender($id,$info){
        $info= DepositSheetLenderModel::updateOrCreate(["id" => $id], $info);
        return $info;
        
    }

    function updateCreateDepositSheetLink($id,$info){
        $info= DepositSheetLinkModel::updateOrCreate(["id" => $id], $info);
        return $info;
        
    }

    function getAllDeposit($address,$lender,$client_id,$offset, $limit){
        $info= DepositSpreadsheetModel::
                with(['deposit_lender',
                      'deposit_lender.lender',
                      'deposit_link'
                    ])
                ->join('home_information','home_information.house_id','deposit_spreadsheet.house_id')
                ->join('deposit_sheet_lender','deposit_sheet_lender.deposit_id','deposit_spreadsheet.id')
                ->join('mcd_other_info','mcd_other_info.house_id','deposit_spreadsheet.house_id');
                $info->select(['deposit_spreadsheet.*','home_information.address','deposit_sheet_lender.*']);
        if(!empty($address)){
            $info->where('home_information.address', 'LIKE', "%$address%");
        }
        if(!empty($lender)){
            $info->where('deposit_sheet_lender.lender_id',$lender);
        }
        if(!empty($client_id) && $client_id !='null'){
            $info->where('mcd_other_info.client_id',$client_id);
        }
       // $info  = $info->skip(intval($offset))->take(intval($limit));
        return $info->get();

    }

    public function get_deposit_lender($lender_id){

        return DepositLenderModel::select(['id','lender_name'])
        ->distinct()
        ->whereIn('id', $lender_id)->get();

        // return DepositSheetLenderModel::select(['deposit_lender.id','house_id','deposit_lender.lender_name'])
        // ->distinct()
        // ->leftjoin('deposit_lender','deposit_lender.id','deposit_sheet_lender.lender_id')
        // ->whereIn('deposit_id',
        // function($query) use($lender_id){
        //     $info=$query->select('deposit_id')
        //         ->distinct()
        //         ->from(with(new DepositSheetLenderModel)->getTable());
        //     if(is_array($lender_id)){
        //         $info->whereIn('lender_id', $lender_id);
        //     }else if(!empty($lender)){

        //         $info->where('lender_id', $lender_id);
        //     }
        // })->get();
    }

    public function getClientLender($client_id){
        $info=mcdOtherInfoModel::select(DB::raw("lender_id"))->distinct()
            ->join('deposit_sheet_lender','deposit_sheet_lender.house_id','mcd_other_info.house_id')
            ->where('client_id',$client_id);
        return $info->get();

    }

    public function get_deposit_link($lender_id){

        return DepositSheetLinkModel::select(['link_name'])
        ->distinct()
        ->whereIn('deposit_id',
        function($query) use($lender_id){
            $info=$query->select('deposit_id')
                ->distinct()
                ->from(with(new DepositSheetLenderModel)->getTable());
            if(is_array($lender_id)){
                $info->whereIn('lender_id', $lender_id);
            }else{
                $info->where('lender_id', $lender_id);
            }
        })->get();
    }

    function updateOtherMcdInfo($house_id,$info){
        $info= mcdOtherInfoModel::updateOrCreate(["house_id" => $house_id], $info);
        return $info;
    }

    function updateClientInfo($id,$info){
        return ClientInfoModel::updateOrCreate(["id" => $id], $info);
    }
    function updatePayersInfo($id,$info){
       return  PayersInfoModel::updateOrCreate(["id" => $id], $info);
    }

    function getClientInfoList(){
        return ClientInfoModel::get();
    }

    function getPayersInfoList($payersId=''){
        if(!empty($payersId)){
            return PayersInfoModel::where('id',$payersId)->first();
        }else{
            return PayersInfoModel::get();
        }
    }

    function getOtherMcdInfo($house_id){
        return mcdOtherInfoModel::with(['client_info','payers_info'])->where('house_id',$house_id)->first();
    }


    public function getLenderFund($house_id){
        

        $query1=McdLenderModel::select(['mcd_lender.id as lender',
                                        DB::raw("IF(SUM(client_renovation_detail.amount) IS NULL,0,SUM(client_renovation_detail.amount)) as amount"),
                                        DB::raw('IF(client_renovation.total_amount IS NULL,0,client_renovation.total_amount) as returned_amount ')
                                        ])
                    ->leftJoin(DB::raw("(SELECT funder,house_id,SUM(client_renovation.amount) as total_amount FROM client_renovation where house_id=$house_id group by funder)  as client_renovation"), 'client_renovation.funder', '=', 'mcd_lender.id')
                    ->leftJoin('client_renovation as clientReno', 'clientReno.funder', '=', 'mcd_lender.id')
                    ->leftJoin('client_renovation_detail', 'client_renovation_detail.invoice_id', '=', 'clientReno.id')
                    ->where('mcd_lender.house_id', $house_id)
                    ->where('mcd_lender.is_lender',1)
                    ->groupBy('mcd_lender.id');
        
        $query2= McdModel::select('house_mcd.lender_name as lender',
                                DB::raw('sum(house_mcd.amount) as amount'),
                                DB::raw('sum(house_mcd.returned_amount) as returned_amount'));
        
        $query2= $query2->leftJoin('mcd_lender', 'house_mcd.lender_name', '=', 'mcd_lender.id')->where('house_mcd.house_id',$house_id);
        $query2= $query2->where('mcd_lender.is_lender',1);
        $query2= $query2->groupBy('house_mcd.lender_name');
        
        $query3 = $query2->unionAll($query1);


        $query = DB::query()->fromSub($query3, 'subquery');
        $query = $query->select('mcd_lender.lender_name',
                                DB::raw('(sum(subquery.amount)-sum(subquery.returned_amount)) as total_funder_amount'));
        $query= $query->leftJoin('mcd_lender', 'subquery.lender', '=', 'mcd_lender.id')->where('mcd_lender.house_id',$house_id);
        $query= $query->groupBy('subquery.lender');

        return  $query->get();
    }

    function getOtherInfo($house_id){
        return mcdOtherInfoModel::with(['client_info'])->where('house_id',$house_id)->first();
    }

    public function getTotalLenderAmount($house_id){
        

        $query1=McdLenderModel::select(['mcd_lender.id as lender',
                                        DB::raw("IF(SUM(client_renovation_detail.amount) IS NULL,0,SUM(client_renovation_detail.amount)) as amount"),
                                        DB::raw('IF(client_renovation.total_amount IS NULL,0,client_renovation.total_amount) as returned_amount ')
                                        ])
                    ->leftJoin(DB::raw("(SELECT funder,house_id,SUM(client_renovation.amount) as total_amount FROM client_renovation where house_id=$house_id group by funder)  as client_renovation"), 'client_renovation.funder', '=', 'mcd_lender.id')
                    ->leftJoin('client_renovation as clientReno', 'clientReno.funder', '=', 'mcd_lender.id')
                    ->leftJoin('client_renovation_detail', 'client_renovation_detail.invoice_id', '=', 'clientReno.id')
                    ->where('mcd_lender.house_id', $house_id)
                    ->where('mcd_lender.is_lender',1)
                    ->groupBy('mcd_lender.id');
        
        $query2= McdModel::select('house_mcd.lender_name as lender',
                                DB::raw('sum(house_mcd.amount) as amount'),
                                DB::raw('sum(house_mcd.returned_amount) as returned_amount'));
        
        $query2= $query2->leftJoin('mcd_lender', 'house_mcd.lender_name', '=', 'mcd_lender.id')->where('house_mcd.house_id',$house_id);
        $query2= $query2->where('mcd_lender.is_lender',1);
        $query2= $query2->groupBy('house_mcd.lender_name');
        
        $query3 = $query2->unionAll($query1);


        $query = DB::query()->fromSub($query3, 'subquery');
        $query = $query->select(DB::raw('(sum(subquery.amount)-sum(subquery.returned_amount)) as total_funder_amount'));
        $query= $query->leftJoin('mcd_lender', 'subquery.lender', '=', 'mcd_lender.id')->where('mcd_lender.house_id',$house_id);
        return  $query->first();
    }

    function getReschedule($request){
        
        $info= mcdOtherInfoModel::select(['mcd_other_info.house_id','is_sold','payers_id'])
              ->with(['payout'=>function($query){
                $query->select(['house_id','close_date_a_to_b']);
               },
               'payers_info',
               'property_info'=>function($query){
                    $query->select(['house_id','address','state','county','city','zip','parcel_id1','lot_acreage_sf']);
                },
                'property_info.first_liens'=>function($query){
                    $query->select(['house_id','maturity_date','lien_amount','modification_annual_interest',
                                    'loan_type','dt_book_page','lender','amortization_loan_estimate_balance',
                                    'est_late_payment_and_fees','no_str_no_appt']);
                },
                'property_info.last_cma_arv_recommendations'=>function($query){
                    $query->select(['house_id','recommended_cma_arv']);
                },
                'property_lender'=>function($query){
                    $query->select(['house_id','lender_name','percentage','is_lender','is_craig_per']);
                }
            ]);
        
        $info = $info->leftJoin('home_information', 'home_information.house_id', '=', 'mcd_other_info.house_id');
        $info = $info->whereNull('home_information.deleted_at');
        
        if(!empty($request['property_address'])){
            $info = $info->where('home_information.address','like','%'.$request['property_address'].'%');  
        }
        if(!empty($request['is_sold'])){
           // $info = $info->leftJoin('mcd_other_info', 'mcd_other_info.house_id', '=', 'payout.house_id');
            $info = $info->where('is_sold',$request['is_sold']);  
        }

        return $info->get();
    }

    function getDeed($house_id){
        $info=PayoutModel::select(['close_date_a_to_b','close_date_b_to_c'])->where('house_id',$house_id)->first();
        $diff=0;
        if(!empty($info)){
            $diff=date_diff(date_create($info->close_date_a_to_b),date_create($info->close_date_b_to_c))->format('%a');
        }
        return $diff;
    }

    function createUpdateMcdUser($mcdInfo){
        return McdUsersModel::updateOrCreate(['user_id'=>$mcdInfo['user_id'],'house_id'=>$mcdInfo['house_id']],$mcdInfo);
    }

    function getMcdUserInfo($house_id){
        return McdUsersModel::with(['user'])->where('house_id',$house_id)->get();
    }

    function getMcdUser($house_id,$userId){
        return McdUsersModel::with(['user'])->where('user_id',$userId)->where('house_id',$house_id)->first();
    }

    function getMcdAccess(){
        
    }

}
