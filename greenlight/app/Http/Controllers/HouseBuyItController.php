<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:27 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/22/19 2:53 AM
 */

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;
use App\Models\HouseBuyItModel;
use App\Models\PropertyModel;
use App\Models\BuyitDesignationModel;
use App\Services\HouseBuyItHistoryService;
use App\Services\HouseBuyItService;
use App\Services\HouseTokenService;
use App\Services\InviteService;
use App\Services\MailService;
use App\Services\PropertyService;
use App\Services\UserService;
use App\Models\ContactRequestModel;
use Validator;
Use Log;
use Illuminate\Http\Request;

class HouseBuyItController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;
    private $houseBuyItService;
    private $houseBuyItHistoryService;
    private $houseTokenService;
    private $propertyService;
    private $mailService;
    private $inviteService;


    public function __construct(Request $request
        , HouseBuyItService $houseBuyItService
        , HouseBuyItHistoryService $houseBuyItHistoryService
        , UserService $userService
        , HouseTokenService $houseTokenService
        , PropertyService $propertyService
        , MailService $mailService
        , InviteService $inviteService
    )
    {
        Log::info("HouseBuyItController: __construct called");
        $this->request                  = $request;
        $this->houseBuyItService        = $houseBuyItService;
        $this->houseBuyItHistoryService = $houseBuyItHistoryService;
        $this->userService              = $userService;
        $this->houseTokenService        = $houseTokenService;
        $this->propertyService          = $propertyService;
        $this->mailService              = $mailService;
        $this->inviteService              = $inviteService;

    }

    private function likeWhere($query, $field, $value)
    {
        if (empty($value))
            return $query;
        return $query->where($field, 'LIKE', "%$value%");
    }

    private function where($query, $field, $value, $condition = "=")
    {
        if (empty($value))
            return $query;
        return $query->where($field, $condition, $value);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyitList()
    {
        Log::info("HouseBuyItController: buyitList called");

        $limit=$offset='';
        if($this->request->get('limit')){
          $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
          if($limit > 50) $limit = 50;
          $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;
        }
        $address = $this->request->get('address');
        $city    = $this->request->get('city');
        $county  = $this->request->get('county');
        $state   = $this->request->get('state');
        $zip     = $this->request->get('zip');

        $info = HouseBuyItModel::select([
            "house_buyit.position as old_position",
            "house_buyit.user_id",
            "buyit_designation.position",
//            "house_buyit.question",
            "house_buyit.request_type",
            "house_buyit.created_at",
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip"
            , "home_information.house_id" ## Needed this field, Important this line
        ])
        ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
        ->leftJoin('buyit_designation', 'house_buyit.house_buyit_id', '=', 'buyit_designation.house_buyit_id');

        $info = $this->where($info, 'house_buyit.user_id', $this->userService->user_id());
        $info = $this->where($info, 'house_buyit.request_type', 'buyit');
        $info = $this->where($info, 'buyit_designation.designation', 'buyer');

        $info = $this->likeWhere($info, 'address', $address);
        $info = $this->likeWhere($info, 'city', $city);
        $info = $this->likeWhere($info, 'county', $county);
        $info = $this->where($info, 'state', $state);
        $info = $this->where($info, 'zip', $zip);
        $info = $info->whereNull('home_information.deleted_at');
        $total = $total = $info->count();

        $info->with(['user'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);

        $info->with([
                "last_sale_details" => function($query) {
                    $query->select(['house_id','sale_id','sale_date','redemption_expires','sale_type']);
                }]);

        if($limit && $offset){
          $info  = $info->skip(intval($offset))->take(intval($limit))->get();
        }else{
          $info  =$info->get();
        }

        if (empty($info))
        {
            return response()->json(['status' => 'success', 'total' => $total, 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success', 'total' => $total, 'data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function passitList()
    {
        Log::info("HouseBuyItController: buyitList called");
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;
        $address = $this->request->get('address');
        $city    = $this->request->get('city');
        $county  = $this->request->get('county');
        $state   = $this->request->get('state');
        $zip     = $this->request->get('zip');

        $info = HouseBuyItModel::select([
            "house_buyit.position",
            "house_buyit.user_id",
            //            "house_buyit.notes",
            //            "house_buyit.question",
            "house_buyit.request_type",
            "house_buyit.created_at",
            "home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip"
            , "home_information.house_id" ## Needed this field, Important this line
        ])
            ->leftJoin('home_information', 'house_buyit.house_id', '=', 'home_information.house_id')
        ;
        $info = $this->where($info, 'house_buyit.user_id', $this->userService->user_id());
        $info = $this->where($info, 'house_buyit.request_type', 'passit');

        $info = $this->likeWhere($info, 'address', $address);
        $info = $this->likeWhere($info, 'city', $city);
        $info = $this->likeWhere($info, 'county', $county);
        $info = $this->where($info, 'state', $state);
        $info = $this->where($info, 'zip', $zip);

        $total = $total = $info->count();

        $info->with(['user'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);
        $info  = $info->skip(intval($offset))->take(intval($limit))->get();

        if (empty($info))
        {
            return response()->json(['status' => 'success', 'total' => $total, 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success', 'total' => $total, 'data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreate($house_id)
    {
        $request_info = $this->request->all();

        $notes = @$request_info['notes'];

        $question                               = array();
        $question['did_you_buy']                = $did_you_buy = @$request_info['did_you_buy'];
        $question['did_you_picture']            = $did_you_picture = @$request_info['did_you_picture'];
        $question['please_submit']              = $please_submit = @$request_info['please_submit'];
        $question['do_money_finance']           = $do_money_finance = @$request_info['do_money_finance'];
        $question['buyit_repair_cost']          = $buyit_repair_cost = @$request_info['buyit_repair_cost'];
        $question['buyit_estimation_arv_value'] = $buyit_estimation_arv_value = @$request_info['buyit_estimation_arv_value'];
        $question['highest_offer_bid']          = $highest_offer_bid = @$request_info['highest_offer_bid'];
        $question['buyitnotes']                 = $notes;
        $question['designation']          = $designation = @$request_info['designation'];

        if (empty($house_id))
        {
            return response()->json(['status' => "failed", 'message' => __("error_messages.house_id_empty")], 200);
        }

        # first check record exists or not
        $property_info = $this->propertyService->findOneById($house_id);
        if (empty($property_info))
        {
            return response()->json(['status' => "failed", 'message' => __("error_messages.house_id_exists")], 200);
        }

       // $invite_data = $this->inviteService->isInviteePropertyBool($house_id, $this->userService->user_id());
        // $invite_data = $this->inviteService->isTwoYearPropertyAccess($house_id, $this->userService->user_id());
        // if ( $invite_data === false)
        // {
        //     return response()->json(['status' => "failed", 'message' => __("error_messages.invite_error")], 200);
        // }

        if (empty($notes))
        {
            return response()->json(['status' => "failed", "message" => __("error_messages.notes_empty"), "data" => array()], 200);
        }
        if (
            empty($highest_offer_bid) || empty($buyit_estimation_arv_value) || ($did_you_buy != 'yes') || empty($designation)
            //($did_you_picture != 'yes') ||
            // ($please_submit != 'yes') ||
            // ($do_money_finance != 'yes')
        )
        {
            return response()->json(['status' => "failed", "message" => __("error_messages.6_questions"), "data" => array()], 200);
        }

        $saleType = $this->houseBuyItService->getSaleType($house_id);
        $sale_type='';
        if(!empty($saleType) && !empty($saleType->last_sale_details) && $saleType->last_sale_details->sale_type){
            $sale_type_array  = config('property_information.sale_type');
            $sale_type=@$sale_type_array[$saleType->last_sale_details->sale_type];
        }                        

        $sale_date='';
        if(!empty($saleType) && !empty($saleType->last_sale_details) && $saleType->last_sale_details->sale_date){
            $sale_date=date('m/d/Y',strtotime(@$saleType->last_sale_details->sale_date));
        }  

        $position_info = $this->houseBuyItService->getBuyItNextPosition($house_id);
        $pos_message   = $position_info['pos_message'];
        $top_message   = $position_info['top_message'];

        ## ToDo: work on save alarm part is remaining.
        //$alarm_id = $this->alarm_model->save_alarm_me($house_id);

        // Make one entry into table for buy it
        $insert_data                 = array();
        $insert_data['position']     = $position_info['position'];
        $insert_data['house_id']     = $house_id;
        $insert_data['request_type'] = 'buyit';
        $insert_data['notes']        = $notes;
        $insert_data['user_id']      = $this->userService->user_id();
        $insert_data['question']     = json_encode($question);
        $insert_data['created_at']   = time();
        $insert_data['updated_at']   = time();

        // $isBuyItBefore = $this->houseBuyItService->getBuyItInfoByUser($house_id, true);

        $info=$this->houseBuyItService->updateOrInsert(
            [
                'house_id' => $house_id,
                'user_id'  => $this->userService->user_id(),
            ], $insert_data);
        
        //print_r($info);die;
        $pos_message=$top_message=[];
        if(!empty($designation)){
            $insert_designation['house_buyit_id']=$info->house_buyit_id;
            $insert_designation['house_id']=$house_id;
            $insert_designation['user_id']=$this->userService->user_id();

            foreach($designation as $design){

                $positionInfo = $this->houseBuyItService->getBuyItNextPosition($house_id,'',$design);

                $insert_designation['designation']=$design;
                $insert_designation['position']=$positionInfo['position'];
                $this->houseBuyItService->updateOrCreateDesignation(
                    [
                        'house_id' => $house_id,
                        'user_id'  => $this->userService->user_id(),
                        'house_buyit_id'=>$info->house_buyit_id,
                        'designation'=>$design
                    ],$insert_designation
                );

                $pos_message[]=$positionInfo['pos_message'];
                $top_message[]=$positionInfo['top_message'];
            }
        }

        unset($insert_data['created_at']);
        unset($insert_data['updated_at']);
        $this->houseBuyItHistoryService->create($insert_data);

        $token = $this->houseTokenService->token($house_id);
        $link                = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);

        $info                = ($question);
        $info['notes']       = $notes;
        $info['pos_message'] = implode('<br/>' ,$pos_message);
        $info['top_message'] = implode(' ',$top_message);
        $info['position']    = $position_info['pos'];
        $info['address_url'] = $link;
        $info['address']     = trim($info['address_url']);
        $info['house_id']    = $house_id;
        $info['sale_type']   = $sale_type;
        $info['sale_date']   = $sale_date;
        $this->mailService->sendBuyItEmailToAM($info);
        // send one email to requesting person
        $this->mailService->sendBuyItEmailToUser($info);

        return response()->json(['status' => "success", 'message' => $pos_message], 200);
    }


    public function updateOrCreatePassIt($house_id)
    {
        $request_info = $this->request->all();

        $notes         = @$request_info['notes'];
        $isBuyItPassIt = $this->houseBuyItService->isInviteToUser($house_id);

        if (empty($house_id))
        {
            return response()->json(['status' => "failed", 'message' => "Unknown record, Please refresh page."], 200);
        }

        if (empty($notes))
        {
            return response()->json(['status' => "failed", 'message' => 'Please enter notes.'], 200);
        }

        // $invite_data = $this->inviteService->isInviteePropertyBool($house_id, $this->userService->user_id());
        // if (empty($isBuyItPassIt) || $invite_data === false)
        // {
        //     Log::emergency('You are not authorize to modify this property, You don\'t have an invite.');
        //     Log::emergency('isBuyItPassIt', $isBuyItPassIt);
        //     Log::emergency('Invite Data.', $invite_data);
        //     return response()->json(['status' => "failed", 'message' => 'You are not authorize to modify this property, You don\'t have an invite.'], 200);
        // }

        // $this->mail_model->send_buyit_email_to_am($info);
        ## ToDo: ALARM me changes are remaining.
        // $alarm_id = $this->alarm_model->save_alarm_me($house_id);

        // Make one entry into table for buy it
        $insert_data                 = array();
        $insert_data['position']     = 0;
        $insert_data['house_id']     = $house_id;
        $insert_data['request_type'] = 'passit';
        $insert_data['notes']        = $notes;
        $insert_data['user_id']      = $this->userService->user_id();
        $insert_data['question']     = '';
        $insert_data['created_at']   = time();
        $insert_data['updated_at']   = time();

        // $isBuyItBefore = $this->houseBuyItService->getBuyItInfoByUser($house_id, true);

        $this->houseBuyItService->updateOrInsert(
            [
                'house_id' => $house_id,
                'user_id'  => $this->userService->user_id(),
            ], $insert_data);


        $this->houseBuyItHistoryService->create($insert_data);

        //Buy IT Request -> We need to set it up in the system to where if they hit a buy it ad it puts them
        // in a position and then later they hit pass on it.
        // Then it needs to remove them from the position and move everyone up in the position.

        // first remove current buyt it request from buy it table
        // then reduce position of next property by 1
        // First Get current position of user

        // If record was buyit and we have to change into passit, then we will update the position for other records.
        if ($isBuyItPassIt['request_type'] == 'buyit')
        {

            $designation = $this->houseBuyItService->getBuyItDesignation($house_id,$isBuyItPassIt['house_buyit_id']);
            if(!empty($designation)){
                foreach($designation as $desig){
                    $position=$desig->position;
                    BuyitDesignationModel::where(['id'=>$desig->id])->delete();

                    BuyitDesignationModel::where([
                        ['house_id', '=', $house_id],
                        ['designation', '=',$desig->designation],
                        ['position', '>', $position]
                        ,])->decrement('position');
                }
            }
            // HouseBuyItModel::where([
            //     ['house_id', '=', $house_id],
            //     ['request_type', '=', 'buyit'],
            //     ['position', '>', $isBuyItPassIt['position']]
            //     ,])->decrement('position');
        }


        # first check record exists or not
        $property_info = $this->propertyService->findOneById($house_id, true);

        $link                = env("APP_FRONTEND") . 'home/showdetail/' . $house_id . '/' . CommonHelper::url_slug($property_info);
        $info['address_url'] = $link;
        $info['address']     = trim($info['address_url']);
        $info['house_id']    = $house_id;
        $info['notes']       = $notes;
        $info['position']    = $isBuyItPassIt['position'];


        ## Send position update email to all users, if it is in buy it
        ## If record was buyit and we have to change into passit, then we will update the position for other records.
        if ($isBuyItPassIt['request_type'] == 'buyit')
        {
            ## send pass it email to AM
            $this->mailService->sendPassItEmailToAM($info);
            $this->mailService->sendBuyItPositionUpdate($info);
        }

         // also send one email to requesting person
        $this->mailService->sendPassItEmailTouser($info);

        return response()->json(['status' => "success", 'message' => 'Thank You for your feedback.'], 200);
    }

    public function exportBuyIt(){

        $house_ids=$this->request->get('house_ids');
        $csvArray=$this->houseBuyItService->getBuyItList($house_ids);
        $str = CommonHelper::arrayToCSV($csvArray);
        return response($str, 200)
            ->header('Content-Type', 'application/csv')
            ->header('Content-Disposition', 'attachment; filename=ES_15643723071.csv');
    }

    function contactRequest(){
        $all=$this->request->all();
        $houseId=$this->request->get('house_id');
        $requestType=$this->request->get('request_type');
        $all['user_id']=$userrId=$this->userService->user_id();
        
        $requested=ContactRequestModel::where('house_id',$all['house_id'])->where('user_id',$all['user_id'])->where('request_type',$all['request_type'])->count();
        if($requested==0){
            $positionCount=ContactRequestModel::where('house_id',$all['house_id'])->where('request_type',$all['request_type'])->count();
            $all['position']=$positionCount+1;
            ContactRequestModel::updateOrCreate(['house_id'=>$all['house_id'],
                                                'user_id'=>$all['user_id'],
                                                'request_type'=>$all['request_type']],$all);
        }
        return response()->json(['status' => "success", 'message' => 'Thank You for Request.'], 200);

    }
}
