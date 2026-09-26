<?php
/**
 * Created By Rativardhan Singh Sengar  4/14/19 8:49 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/26/19 1:09 AM
 */

namespace App\Services;
use App\Models\InviteModel;
use App\Models\UserInviteSettingsModel;
use Log;
use App\Models\InviteHistoryModel;

class InviteService
{
    private $findOneById;
    private $userService;
    private $propertyService;

    /**
     * InviteService constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService,PropertyService $propertyService) {
        Log::info("InviteService: __construct called");
        $this->userService = $userService;
        $this->propertyService  = $propertyService;
    }


    /**
     * @param $invite_id
     * @return mixed
     */
    public function findOneById($invite_id)
    {
        Log::info("InviteService: findOneById called");

        $this->findOneById =  InviteModel::find($invite_id);
        return $this->findOneById;
    }

    public function create($propertyData){
        Log::info("InviteService: create called");

        //$propertyData['invitee_from'] = $this->userService->user_id();
        return InviteModel::create($propertyData);
    }

    public function updateOrCreate($where , $propertyData){
        Log::info("InviteService: updateOrCreate called");

        if(empty($where))
            return false;

        //        @unset($propertyData['house_id']);
        return InviteModel::updateOrCreate($where, $propertyData);
    }


    public function isInviteePropertyBool($house_id, $user_id){
        Log::info("InviteService: isInviteePropertyBool called");

        if (empty($house_id) || empty($user_id))
            return false;

        $is_exists = InviteModel::where(
            [
                'house_id' => $house_id,
                'invitee_to'  => $user_id,
            ])->exists();

        if($is_exists)
        {
            return true;
        }

        return false;
    }

    public function isTwoYearPropertyAccess($house_id, $user_id){
        $property_info = $this->propertyService->findOneById($house_id);
        $sale_type = @$property_info->last_sale_details->sale_type;
        $sale_date = @$property_info->last_sale_details->sale_date;
        
        $twoYeardate = strtotime($sale_date.' +2 year');
        $currentDate= strtotime("now");

        if($twoYeardate > $currentDate){
            $inviteSettings = UserInviteSettingsModel::
            leftJoin('users', 'user_invite_settings.user_id', '=', 'users.id')
            ->where("users.status","!=",'blocked')
            ->where("users.id","=",$user_id)
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
                ->count();
            
            if($inviteSettings>0)
                return true;
            
        }

        return false;
        
    }



    public function getInviteInfo($fromd,$tod)
    {
    
        $fromDate='2020-'.$fromd;
        $toDate='2020-'.$tod;
        Log::info("InviteService: findOneById called");
        $from=strtotime($fromDate);
        $to =strtotime($toDate);;

    //    $data =  InviteModel::leftjoin('users','users.id','=','invitations_info.invitee_to')
    //             ->where('invitations_info.created_at','>=', $from)
    //             ->where('invitations_info.created_at','<=',$to)
    //             ->orderBy('invitations_info_id','DESC')->get();
        $data =  InviteModel::where('created_at','>=', $from)
                ->where('created_at','<=',$to)
                ->orderBy('invitations_info_id','DESC')->get();
        //dd($data);
        return $data;
    }

    function deleteInvite($invite_id,$user_id){
        return InviteModel::where('invitations_info_id',$invite_id)->update(['is_deleted'=>1,'deleted_by'=>$user_id]);
    }

    function deleteAllInvite($invite_id,$user_id){
        return InviteHistoryModel::where('invitations_info_id',$invite_id)->update(['is_deleted'=>1,'deleted_by'=>$user_id]);
    }
}
