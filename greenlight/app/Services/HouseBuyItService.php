<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/14/19 7:28 PM
 */

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\HouseBuyItModel;
use App\Models\PropertyModel;
use App\Models\BuyitDesignationModel;
use App\Models\ContactRequestModel;
use App\Models\User;
use Log;
use DB;
class HouseBuyItService
{
    private $findOneById;
    private $userService;
    private $getBuyItInfoByUser;
    public  $houseTokenService;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(UserService $userService,HouseTokenService $houseTokenService)
    {
        Log::info("HouseBuyItService: __construct called");
        $this->userService = $userService;
        $this->houseTokenService = $houseTokenService;
    }


    /**
     * Find property record
     * @param $house_byit_id
     * @return mixed
     */
    public function findOneById($house_byit_id, $is_cache = false)
    {
        Log::info("HouseBuyItService: findOneById called");
        if ($is_cache == true)
        {
            return $this->findOneById;
        }

        $this->findOneById = HouseBuyItModel::find($house_byit_id);
        return $this->findOneById;
    }

    public function create($propertyData)
    {
        Log::info("HouseBuyItService: create called");
        return HouseBuyItModel::create($propertyData);
    }

    public function update($id, $updateData)
    {
        Log::info("HouseBuyItService: update called");

        unset($updateData['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateData);

    }

    public function updateOrInsert($where, $updateData)
    {
        Log::info("HouseBuyItService: updateOrInsert called");

         HouseBuyItModel::updateOrInsert(
            $where,
            $updateData
        );

        return HouseBuyItModel::where($where)->first();

    }

    public function updateOrCreateDesignation($where, $updateData)
    {
        Log::info("HouseBuyItService: updateOrCreateDesignation called");
        BuyitDesignationModel::updateOrInsert(
            $where,
            $updateData
        );

    }

    public function getBuyItNextPosition($house_id, $user_id = '',$designation='buyer')
    {
        Log::info("HouseBuyItService: getBuyItNextPosition called");

        $isBeforeBuyIt = $this->getBuyItInfoByUser($house_id, false, $user_id,$designation);
        if ($isBeforeBuyIt === false)
        {
            // $position = HouseBuyItModel::where([
            //     'house_id'     => $house_id,
            //     'request_type' => 'buyit'
            // ])->count();
            $position = BuyitDesignationModel::where([
                    'house_id'     => $house_id,
                    'designation' => $designation,
                    'user_status' => 'active'
            ])->count();

            $position++;
        }
        else
        {
            $position = $isBeforeBuyIt['position'];
        }
        $top_message = '';
        // check what is user status of buy it . first , second or repeated first second
        if ($position == 1)
        {
            if($designation=='lender'){
                $pos_message = "Congratulations! You are in ".ucfirst($designation)." Position.";

            }else{
                $pos_message = "Congratulations! You are in First Position as a ".ucfirst($designation);
            }
            $pos_        = 'First';
        }
        else
        {

            $temp_message = $this->getBuyitPositionLetterConvert($position);
            $pos_         = $temp_message;

            if($designation=='lender'){
                $pos_message = 'You are in ' .ucfirst($designation). ' Position. '."\n";

            }else{
                $pos_message = 'You are in ' . (($temp_message)) . ' Position as a '.ucfirst($designation) ."\n";
            }

            $head = $this->getTopBuyitUser($house_id, $position,$designation);
            foreach ($head as $usePosition)
            {
                $top_user = CommonHelper::nameFormat($usePosition);

                if ($usePosition->position < 10)
                    $u_position = ucwords($this->getBuyitPositionLetterConvert($usePosition->position));
                else
                    $u_position = $usePosition->position . 'th';

                if($designation=='lender'){
                    $top_message .= $top_user . ' in ' . ucfirst($designation) . ' Position  '."\n";
                    
                }else{
                    $top_message .= $top_user . ' is in ' . $u_position . ' Position '.ucfirst($designation).' '."\n";
                }
            }
        }

        return array('position' => $position, 'pos_message' => $pos_message, 'pos' => $pos_, 'top_message' => $top_message);
    }

    public function getTopBuyitUser($house_id, $position = 0,$designation='buyer')
    {

        if (empty($position))
            return [];

        $where = [
            'house_id' => $house_id,
        ];

        $info = BuyitDesignationModel::where(
            $where
        )->where('position', '<', $position)->where('designation','=',$designation)
            ->where('user_status', 'active')
            ->leftJoin('users', 'users.id', '=', 'buyit_designation.user_id')
            ->orderBy('position', 'asc')->get();
        return $info;
    }

    public function getTopBuyItUserNHouseIds($house_ids, $position = 0)
    {

        if (empty($position) || empty($house_ids))
            return [];


        $info = HouseBuyItModel:: whereIn(
            "house_id", $house_ids
        )->where('position', '<', $position)
                               ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
                               ->orderBy('position', 'asc')->get();
        return $info;
    }


    public function getDownBuyitUser($house_id, $position = 0,$designation='buyer')
    {

        if (empty($position))
            return [];

        $where = [
            'house_id' => $house_id,
        ];

        $info = BuyitDesignationModel::where(
            $where
        )->where('position', '>=', $position)
        ->where('user_status','active')
        ->where('designation', '=',$designation)
            ->leftJoin('users', 'users.id', '=', 'buyit_designation.user_id')
            ->orderBy('position', 'asc')->get();
        return $info;
    }

    public function getBuyItInfoByUser($house_id, $is_cache = false, $user_id = '',$designation)
    {
        Log::info("HouseBuyItService: getBuyItInfoByUser called");

        if ($is_cache === true && isset($this->getBuyItInfoByUser))
        {
            return $this->getBuyItInfoByUser;
        }

        if(empty($user_id))
        $user_id = $this->userService->user_id();

        $info    = BuyitDesignationModel::where(
            [
                'house_id'     => $house_id,
                'user_id'      => $user_id,
                'user_status' => 'active',
                'designation' => $designation
            ])->get()->first();

        if (!empty($info))
        {
            $info = $info->toArray();
        }
        else
        {
            $info = [];
        }

        $this->getBuyItInfoByUser = $info;
        if (empty($info))
            return false;
        else
            return $info;

    }

    public function isInviteToUser($house_id)
    {
        Log::info("HouseBuyItService: isInviteToUser called");

        $user_id = $this->userService->user_id();

        $info    = HouseBuyItModel::where(
            [
                'house_id' => $house_id,
                'user_id'  => $user_id,
            ])->get()->first();

        

        if (!empty($info))
        {
            $info = $info->toArray();
        }
        else
        {
            $info = [];
        }

        return $info;

    }

    public function isPassOnItProperty($house_id, $user_id)
    {
        Log::info("HouseBuyItService: isPassOnItProperty called");

        if (empty($house_id) || empty($user_id))
            return false;


        $is_exists = HouseBuyItModel::where(
            [
                'house_id' => $house_id,
                'user_id'  => $user_id,
            ])->get()->first();

        if( empty($is_exists))
        {
            return false;
        }
        else if($is_exists->request_type == 'passit')
        {
            return true;
        }

        return false;
    }

    public function getBuyitPositionLetterConvert($position)
    {
        $pos = array(
            1  => 'first',
            2  => 'second',
            3  => 'third',
            4  => 'fourth',
            5  => 'fifth',
            6  => 'sixth',
            7  => 'Seventh',
            8  => 'eighth',
            9  => 'ninth',
            10 => 'tenth',
        );

        $temp_message = $position;
        if ($temp_message < 10)
        {
            $temp_message = ucwords(@$pos[$position]);
        }
        else
        {
            if ($temp_message > 20 && $temp_message % 10 == 1)
            {
                $temp_message = $temp_message . 'st';
            }
            else if ($temp_message > 20 && $temp_message % 10 == 2)
            {
                $temp_message = $temp_message . 'nd';
            }
            else
                $temp_message = $temp_message . 'th';
        }

        return $temp_message;
    }

    function getSaleType($house_id){
        $saleType = PropertyModel::select([
            "home_information.house_id"## Needed this field, Important this line
        ])->with([
            "last_sale_details" => function ($query) {
                $query->select(['house_id', 'sale_id', 'sale_date', 'sale_time','sale_type' ]);
            }
            ])->where('home_information.house_id','=',$house_id)->first();
        return $saleType;
    }

    function getBuyItDesignation($house_id,$house_byit_id){
        $user_id = $this->userService->user_id();
        $info    = BuyitDesignationModel::where([
                        'house_id' => $house_id,
                        'user_id'  => $user_id,
                        'user_status'=> 'active',
                        'house_buyit_id' => $house_byit_id
                    ])->get();
                    return $info ;
    }

    public function getBuyItList($houseIds){

        $info = HouseBuyItModel::select([
            "house_buyit.position as old_position",
            "house_buyit.user_id",
            "buyit_designation.position",
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
        $info = $info->whereNull('home_information.deleted_at');
        $info->with(['user'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);
        $info->with([
                "last_sale_details" => function($query) {
                    $query->select(['house_id','sale_id','sale_date','redemption_expires','sale_type']);
                }]);

        if(!empty($houseIds) && $houseIds!='all'){
            $info = $info->whereIn('home_information.house_id',$houseIds);
        }
        
        $houseAll =$info->get();
        
        $header   = [];
        $header[] = "Address";
        $header[] = 'City';
        $header[] = 'State';
        $header[] = 'County';
        $header[] = 'zip';
        $header[] = 'Position';
        $header[] = 'Sale Date';
        $header[] = 'Sale Type';
        $header[] = "Redemption Expires Date";
        $header[] = 'Name';
        $header[] = 'Property Detail Url';

        $temp[]   = $header;
        foreach ($houseAll as $key => $value) {
          
            $address_url = $this->houseTokenService->address_url($value->house_id, $value);

            $parse_data                         = [];
            $parse_data['address']              = CommonHelper::emptyDefault($value->address);
            $parse_data['city']                 = CommonHelper::emptyDefault($value->city);
            $parse_data['state']                = CommonHelper::emptyDefault($value->state);
            $parse_data['county']               = CommonHelper::emptyDefault($value->county);
            $parse_data['zip']                  = CommonHelper::emptyDefault($value->zip);
            $parse_data['position']             = CommonHelper::emptyDefault($value->position);
            $parse_data['sale_date']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $sale_type_array                    = config('property_information.sale_type');
            $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
            $parse_data['redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'redemption_expires', 'date');
            $parse_data['name']                 = CommonHelper::emptyDefault($value->user->first_name).' '.CommonHelper::emptyDefault($value->user->last_name);
            $parse_data['Link to Estates']      = $address_url;
            $temp[]                             = $parse_data;
        }
        
        return $temp;
    }

    private function where($query, $field, $value, $condition = "=")
    {
        if (empty($value))
            return $query;
        return $query->where($field, $condition, $value);
    }

    function getBuyitUser($user_id){
        $buyItuserList=BuyitDesignationModel::where('user_id',$user_id)
        ->where('user_status','active');
        return $buyItuserList->get();;
       
    }

    function getBlockedUserList(){
        $userList=User::select(['id','current_role','status'])->where('status','blocked')->get();
        return $userList;
    }

    function updateUserPositions($house_id,$designation,$user_id,$position){
        $buyItuserList=BuyitDesignationModel::
                       where('designation',$designation)
                      ->where('house_id',$house_id)
                      ->where('user_id','<>',$user_id)
                      ->where('position','>',$position)
                      ->where('user_status','active')->update(['position'=>DB::raw('position-1')]);
      
    }

    function updateBlockedUserStatus($house_id,$designation,$user_id){
        $buyItuserList=BuyitDesignationModel::
                       where('designation',$designation)
                      ->where('house_id',$house_id)
                      ->where('user_id',$user_id)
                      ->where('user_status','active')
                      ->update(['user_status'=>'blocked']);

    }


    public function getContactReqestPositions($house_id, $position = 0,$designation='buyer')
    {

        if (empty($position))
            return [];

        $where = [
            'house_id' => $house_id,
        ];

        $info = ContactRequestModel::select(['contact_request.house_id','contact_request.position','contact_request.request_type','contact_request.user_id','users.first_name','users.last_name'])
        ->leftJoin('users','users.id','=','contact_request.user_id')
        ->where('house_id',$house_id)
        ->where('position', '>=', $position)
        ->where('status','active')
        ->where('request_type', '=',$designation)
            ->orderBy('position', 'asc')->get();
        return $info;
    }

}
