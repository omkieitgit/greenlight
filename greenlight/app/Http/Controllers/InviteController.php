<?php
/**
 * Created By Rativardhan Singh Sengar  4/14/19 7:01 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/14/19 4:05 PM
 */

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;
use App\Helpers\CustomHelper;
use App\Models\HoaEmailsModel;
use App\Models\HouseBuyItModel;
use App\Models\InviteHistoryModel;
use App\Models\InviteModel;
use App\Models\UserInviteSettingsModel;
use App\Services\HouseBuyItHistoryService;
use App\Services\HouseBuyItService;
use App\Services\HouseTokenService;
use App\Services\InviteHistoryService;
use App\Services\InviteService;
use App\Services\MailService;
use App\Services\PropertyService;
use App\Services\UserService;
use App\Services\WholesaleBuyerNService;
use Validator;
Use Log;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;
    private $houseTokenService;
    private $propertyService;
    private $mailService;
    private $houseBuyItService;
    private $inviteService;
    private $inviteHistoryService;
    private $wholesaleBuyerNService;


    public function __construct(Request $request
        , UserService $userService
        , HouseTokenService $houseTokenService
        , PropertyService $propertyService
        , MailService $mailService
        , HouseBuyItService $houseBuyItService
        , InviteService $inviteService
        , InviteHistoryService $inviteHistoryService
        , WholesaleBuyerNService $wholesaleBuyerNService
    )
    {
        Log::info("InviteController: __construct called");
        $this->request                = $request;
        $this->userService            = $userService;
        $this->houseTokenService      = $houseTokenService;
        $this->propertyService        = $propertyService;
        $this->mailService            = $mailService;
        $this->houseBuyItService      = $houseBuyItService;
        $this->inviteService          = $inviteService;
        $this->inviteHistoryService          = $inviteHistoryService;
        $this->wholesaleBuyerNService = $wholesaleBuyerNService;

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
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function isInvited($house_id)
    {
        if (empty($house_id))
        {
            return response()->json(['status' => "failed", 'message' => __("error_messages.house_id_empty")], 200);
        }

        $invite_data = $this->inviteService->isInviteePropertyBool($house_id, $this->userService->user_id());
        if ( $invite_data === false)
        {
            return response()->json(['status' => "failed", 'message' => __("error_messages.invite_error")], 200);
        }


        return response()->json(['status' => "success",  'messages' => "You ae invited on it."], 200);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function list($house_id)
    {
        Log::info("InviteController: list called");
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;


        $info = InviteModel::select([
            "invitations_info.*",
        ])
        ;

        # ToDo: Later add role wise condition and fetch all invite on this property
        $info = $this->where($info, 'invitations_info.house_id', $house_id);
        $info = $this->where($info, 'invitations_info.invitee_from', $this->userService->user_id());
        $info->where('invitations_info.is_deleted',0);

        $total = $info->count();

        $info->with(['invitee_to'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);
        $info->with(['invitee_from'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);
        $info->orderBy('invitations_info_id','desc');
        $info  = $info->skip(intval($offset))->take(intval($limit))->get();

        if (empty($info))
        {
            return response()->json(['status' => 'success', 'total' => $total, 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success', 'total' => $total, 'data' => $info], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function all($house_id)
    {
        Log::info("InviteController: list called");
        $limit   = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset  = $this->request->get('offset') ? $this->request->get('offset') : 0;


        $info = InviteHistoryModel::select([
            "invitations_info_history.*",
        ])
        ;

        # ToDo: Later add role wise condition and fetch all invite on this property
        $info = $this->where($info, 'invitations_info_history.house_id', $house_id);
        $info->where('invitations_info_history.is_deleted',0);
        if(($this->userService->is_buyer()))
        {
            $info = $this->where($info, 'invitations_info_history.invitee_from', $this->userService->user_id());
        }

        $total = $total = $info->count();

        $info->with(['invitee_to'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);
        $info->with(['invitee_from'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }]);
        $info->orderBy('invitations_info_id','desc');
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
        Log::info("InviteController: updateOrCreate called");

        $request_info = $this->request->all();

        $user_ids  = @$request_info['user_ids'];
        $subject = @$request_info['subject'];
        $message = @$request_info['message'];
        $is_all_invite = @$request_info['is_all_invite'];

        if (empty($house_id))
        {
            return response()->json(['status' => "failed", 'message' => __("error_messages.house_id_empty")], 200);
        }

        if (empty($user_ids))
        {
            return response()->json(['status' => "failed", "message" => "Please enter email or emails.", "data" => array()], 200);
        }

        # first check record exists or not
        $property_info = $this->propertyService->findOneById($house_id);

        if (empty($property_info))
        {
            return response()->json(['status' => "failed", 'message' => __("error_messages.house_id_exists")], 200);
        }

        if (empty($subject))
        {
            $subject = "Property Invitation.";
        }

        ## Sale date and sale type

        $invitee_email_mail = [];
        $invitee_error      = [];

        if($is_all_invite == 1)
        {
            ## Send invite email to invite settings person.
            ## Get records from invite_settings table
            $sale_type = @$property_info->last_sale_details->sale_type;

            $inviteSettings = UserInviteSettingsModel::
            leftJoin('users', 'user_invite_settings.user_id', '=', 'users.id')
            ->where("users.status","!=",'blocked')
                ->where(function ($query) use ($property_info, $sale_type)
                {
                    $query->where(function ($query) use ($property_info, $sale_type)
                    {
                        $query->where('user_invite_settings.request_type', 1);
                        $query->where('user_invite_settings.state', $property_info->state);
                        $query->where(function ($queryChild) use ($property_info, $sale_type)
                        {
                            $queryChild->where('user_invite_settings.sale_type', 0);
                            $queryChild->orWhere('user_invite_settings.sale_type', $sale_type);
                        });
                    })->orWhere(function ($query) use ($property_info, $sale_type)
                    {
                        $query->where('user_invite_settings.request_type', 2);
                        $query->where('user_invite_settings.state', $property_info->state);
                        $query->where('user_invite_settings.county', $property_info->county);
                        $query->where(function ($queryChild) use ($property_info, $sale_type)
                        {
                            $queryChild->where('user_invite_settings.sale_type', 0);
                            $queryChild->orWhere('user_invite_settings.sale_type', $sale_type);
                        });
                    });
                })
                ->get();

            foreach ($inviteSettings as $inviteUserId) {
                $temp = [
                    'id'=>$inviteUserId->user_id,'name'=>$inviteUserId->id];
                $user_ids[] =  $temp;
            }

            $user_ids = collect($user_ids)->unique('id')->all();;
        }

        foreach ($user_ids as $user)
        {
            $validator = Validator::make(
                ['email' => $user['id']], [
                'email' => 'required',
            ]);

            // We are not using this because , we want to return all error into one array box
            if (empty($user['id']))
            {
                $invitee_error[$user['name']] = CommonHelper::customValidatorMessageArray($validator);
                continue;
            }

            ## create user, if not exists into database as non_register user.
            $inviteToInfo = $this->userService->findOneById($user['id']);

            // One more condition to make sure status is not blocked.
            if($inviteToInfo->status == 'blocked')
            {
                Log::info("InviteController: blocked user processed somehow");
                continue;
            }

            // make invite entry into database
            // storing invitees info
            $email = $inviteToInfo->email;

            $invitees_info = array(
                'house_id'        => $house_id,
                'address'         => CommonHelper::addressFormat($property_info),
                'invitee_to'      => $inviteToInfo->id,
                'invitee_from'    => $this->userService->user_id(),
                'invitee_email'   => trim($email),
                'invitee_subject' => $subject,
                'invitee_message' => $message,
            );
            $this->inviteService->updateOrCreate(['house_id'=>$house_id, 'invitee_to'=>$inviteToInfo->id], $invitees_info);
            $this->inviteHistoryService->create($invitees_info);

            ##check if pass on it property no need to send email for this invitee
            ##But for tracking purpose, we made one entry into database.
            ##Pass it property, don't need to resend invite for same properties
            $isPassIt = $this->houseBuyItService->isPassOnItProperty($house_id, $inviteToInfo->id);

            if ($isPassIt !== true)
            {
                $invitee_email_mail[]    = $email;
                $invitee_error[$inviteToInfo->id][] = "Your invitation has been sent to ".CommonHelper::nameFormat($this->userService->findOneById($inviteToInfo->id));
            }
            else
            {
                $invitee_error[$inviteToInfo->id][] = "User already pass on this property, Invite can not proceed for.";
            }

            # make one entry for STHB, if doesn't exists into table
            $isSTHBExists = $this->wholesaleBuyerNService->isSTHBProperty($house_id, $inviteToInfo->id);
            if (!$isSTHBExists)
            {
                $insert             = [];
                $insert['name']     = CommonHelper::nameFormat($inviteToInfo);
                $insert['email']    = $email;
                $insert['house_id'] = $house_id;
                $insert['user_id']  = $inviteToInfo->id;
                $insert['added_by'] = $this->userService->user_id();
                $this->wholesaleBuyerNService->create($insert);
            }

        }

        $token       = $this->houseTokenService->token($house_id);
        $link        = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);

        $sale_date = CustomHelper::revert_date_format_database(@$property_info->last_sale_details->sale_date);
        //$address = CommonHelper::countyAddressFormat($property_info);
        $address = @$property_info->address . ' '  . @$property_info->state . ' ' . @$property_info->zip;

        # send invite mail now to all users.
        
        $send_invite_email_copy   = [];
        $countyList=["Dallas","Denton","Collin","Travis","Wise","Parker","Kaufman","Rockwall"];
        $txCountyList=["Harris","Montgomery","Fort Bend"];
        
        $txCountyList1=["Dallas", "Collin", "Denton", "Kaufman", "Rockwall","Tarrant","Ellis","Grayson"];
        
        $txCountyList2=["Harris", "Fort Bend", "Galveston", "Bexar", "Brazoria", "Montgomery", "Liberty", "Chambers", "Waller"];
        
        $txCountyList3=["Tarrant"];
        $txCountyList4=["Austin","Bexar"];
        $ncCountyList=["Guilford", "Chatham", "Forsyth", "Davie", "Randolph", "Davidson", "Rockingham",  "Alamance"];
        
        $ncCountyList1=["Guilford","Forsyth", "Davidson"];

        $subUserEmails=$this->userService->getSubtoSetting($property_info->state,$property_info->county);
        if(!empty($subUserEmails)){
            foreach($subUserEmails as $userEmail){
                if(!in_array($userEmail->user->email,$invitee_email_mail)){
                    $invitee_email_mail[]=$userEmail->user->email;
                }
            }
        }
        // if(strtolower($property_info->state)=='ga'){
        //     $invitee_email_mail[] ='montgomery20@att.net';
        // }
        
        // if(strtolower($property_info->state)=='tx'){
        //     $invitee_email_mail[] ='estatesllcamandat@gmail.com';
        //     $invitee_email_mail[] ='montgomery20@att.net';
        //     // if(in_array($property_info->county,$countyList)){
        //     // }
        //     if(in_array($property_info->county,$txCountyList)){
        //         $invitee_email_mail[]="nextlevelproperties93@gmail.com";
        //     }
        //     if(in_array($property_info->county,$txCountyList1)){
        //         //$invitee_email_mail[]="houseofjuda888@gmail.com";
        //         //$invitee_email_mail[] ='jcb8solutions@gmail.com';
        //         //$invitee_email_mail[]="marvinsproperties24@gmail.com";
        //         $invitee_email_mail[] ='j.reidbills@comcast.net';
        //     }
        //     if(in_array($property_info->county,$txCountyList3))
        //     {
        //         //$invitee_email_mail[] ='KinsellaEquityHoldings@gmail.com';
        //         $invitee_email_mail[]="jennifer.andersonbusiness411@gmail.com";
        //     }
        //     if(in_array($property_info->county,$txCountyList2)){
        //         $invitee_email_mail[]="agentbookinfo1@gmail.com";
        //         $invitee_email_mail[]="folorunshojr@gmail.com"; //need to check
        //         $invitee_email_mail[]="darrylw.theestates@gmail.com";
        //         $invitee_email_mail[]="jonesjojns10@gmail.com"; //wholesale buyer
        //     }
        //     if(in_array($property_info->county,$txCountyList4))
        //     {
        //         $invitee_email_mail[]="buttinorealeste@gmail.com";
        //     }
        // }
        // if(strtolower($property_info->state)=='nc'){
        //    // $invitee_email_mail[]="ninalesquire@gmail.com";
        //     $invitee_email_mail[]="Jirehpropertysolutions1@gmail.com";
        //     if(in_array($property_info->county,$ncCountyList)){
        //         $invitee_email_mail[]="derrickellis336@gmail.com";
        //         $invitee_email_mail[]="Jenniferalley32@gmail.com";
        //     }
        //     if(in_array($property_info->county,$ncCountyList1)){
        //         $invitee_email_mail[]="Msflo.ama@gmail.com";
        //     }
        // }
        // if(strtolower($property_info->state)=='sc'){
        //     $invitee_email_mail[]="Jirehpropertysolutions1@gmail.com";
        //     $invitee_email_mail[]="clavierhomes@gmail.com";
        // }
        
        $invitee_email_mail = array_unique($invitee_email_mail);
        
        foreach ($invitee_email_mail as $email)
        {
            $email_info = array(
                'house_id'    => $house_id,
                'to'          => trim($email),
                'subject'     => $property_info->county." ".$subject." ".$sale_date.", ".$address,
                'message'     => $message,
                'link_anchor' => $link,
                "state"       => $property_info->state,
                "county"      => $property_info->county
            );
            $this->mailService->inviteUser($email, $email_info);
        }

        return response()->json(['status' => "success", 'message' => "Your invitation has been sent.", 'messages' => $invitee_error], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($invite_id)
    {

        // check if invite exists or not
        // if belong to same user remove it
        // if admin or invitee is removing it, remove it
        // if user already in buy it, notify user if user want to remove it .
        // if pass it property delete it without any popup alert.


    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function hoaInvite($house_id)
    {
        $request_info = $this->request->all();
        $subject = @$request_info['subject'];
        $message = @$request_info['message'];

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

        if (empty($subject))
        {
            $subject = "Property Invitation.";
        }

        // get list of all emails for this particualar state .
        $infoUsers = HoaEmailsModel::where('state',$property_info->state)->with(['user'=> function($query) {
            $query->select(['id','email','first_name','last_name','username']);
        }])->get();

        if($infoUsers->count() < 1)
        {
            // Send to default emails
            $infoUsers = HoaEmailsModel::where('state','d0')->with(['user'=> function($query) {
                $query->select(['id','email','first_name','last_name','username']);
            }])->get();
        }


        $invitee_email_mail = [];
        $invitee_error      = [];
        foreach($infoUsers as $parent)
        {
            $user = $parent->user;
            if(empty($user))
            {
                continue;
            }

            // storing invitees info
            $email = $user->email;
            $invitee_email_mail[] = $email;
            $invitees_info = array(
                'house_id'        => $house_id,
                'address'         => CommonHelper::addressFormat($property_info),
                'invitee_to'      => $user->id,
                'invitee_from'    => $this->userService->user_id(),
                'invitee_email'   => trim($email),
                'invitee_subject' => $subject,
                'invitee_message' => $message,
            );
            $this->inviteService->updateOrCreate(['house_id'=>$house_id, 'invitee_to'=>$user->id], $invitees_info);
            $this->inviteHistoryService->create($invitees_info);

            ##ToDo: check if pass on it property no need to send email for this invitee
            ##ToDo: But for tracking purpose, we made one entry into database.
            ##ToDo: Pass it property, don't need to resend invite for same properties
            $isPassIt = $this->houseBuyItService->isPassOnItProperty($house_id, $user->id);

            if ($isPassIt !== true)
            {
                $invitee_email_mail[]    = $email;
                $invitee_error[$user->id][] = "Your invitation has been sent.";
            }
            else
            {
                $invitee_error[$user->id][] = "User already pass on this property, Invite can not proceed.";
            }

            # make one entry for STHB, if doesn't exists into table
            $isSTHBExists = $this->wholesaleBuyerNService->isSTHBProperty($house_id, $user->id);
            if (!$isSTHBExists)
            {
                $insert             = [];
                $insert['name']     = CommonHelper::nameFormat($user);
                $insert['email']    = $email;
                $insert['house_id'] = $house_id;
                $insert['user_id']  = $user->id;
                $insert['added_by'] = $this->userService->user_id();
                $this->wholesaleBuyerNService->create($insert);
            }

        }

        $token       = $this->houseTokenService->token($house_id);
        $link        = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);
        $address     = CommonHelper::addressFormat($property_info);

        # send invite mail now to all users.
        foreach ($invitee_email_mail as $email)
        {
            $email_info = array(
                'house_id'    => $house_id,
                'to'          => trim($email),
                'subject'     => $subject . ' ' . $address,
                'message'     => $message,
                'link_anchor' => $link
            );
            $this->mailService->inviteUser($email, $email_info);
        }

        return response()->json(['status' => "success", 'message' => "Your invitation has been sent.", 'messages' => $invitee_error], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function hoaInviteList($house_id)
    {

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

        // get list of all emails for this particualar state .
        $infoUsers = HoaEmailsModel::where('state',$property_info->state)->with(['user'=> function($query) {
            $query->select(['id','first_name','last_name','username']);
        }])->get();

        if($infoUsers->count() < 1)
        {
            // Send to default emails
            $infoUsers = HoaEmailsModel::where('state','d0')->with(['user'=> function($query) {
                $query->select(['id','first_name','last_name','username']);
            }])->get();
        }

        $users = [];
        foreach($infoUsers as $parent)
        {
            $user = $parent->user;
            if(empty($user))
            {
                continue;
            }
            $users[] = $user;
        }

        return response()->json(['status' => "success", 'message' => "..", 'data' => $users], 200);
    }

    function deleteInvite($invite_id){
        try
        {
            $info= $this->inviteService->deleteInvite($invite_id,$this->userService->user_id());
            if ($info == true)
            {
                return response()->json(['message' => __("messages.record_delete"), 'data'=>[]],200);
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

    function deleteAllInvite($invite_id){
        try
        {
            $info= $this->inviteService->deleteAllInvite($invite_id,$this->userService->user_id());
            if ($info == true)
            {
                return response()->json(['message' => __("messages.record_delete"), 'data'=>[]],200);
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

}
