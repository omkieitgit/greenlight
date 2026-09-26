<?php
/**
 * Created By Rativardhan Singh Sengar  6/3/19 10:13 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/1/19 7:17 PM
 */

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\AlarmMeModel;
use App\Models\WholesaleNotesModel;
use Log;
use App\Services\MailService;
use App\Models\PropertyModel;


class AlarmMeService {
    private $findOneById;
    private $userService;
    private $mailService;

    public function __construct(UserService $userService,MailService $mailService) {
        Log::info("AlarmMeService: __construct called");
        $this->userService = $userService;
        $this->mailService  = $mailService;
    }


    public function findOneById($id) {
        Log::info("AlarmMeService: findOneById called");
        $this->findOneById = AlarmMeModel::find($id);
        return $this->findOneById;
    }

    public function exists($where) {
        Log::info("AlarmMeService: findOneById called");
        return  AlarmMeModel::where($where)->exists();
    }


    public function create($info) {
        Log::info("AlarmMeService: create called");
        $info['user_id'] = $this->userService->user_id();
        return AlarmMeModel::create($info);
    }

    public function getAlarmSetting($house_id){
        return AlarmMeModel::where('alarm_me.house_id',$house_id)
             ->select(['alarm_me.house_id','alarm_me.user_id'])
             ->leftJoin('home_information', 'home_information.house_id', '=', 'alarm_me.house_id')
             ->leftJoin('users', 'users.id', '=', 'alarm_me.user_id')
             ->where('users.status','active')
             ->whereNull('home_information.deleted_at')
             ->whereNull('alarm_me.deleted_at')->get();
    }

    function sendEmailAlarmSettingUser($house_id){
        $alarmResult=$this->getAlarmSetting($house_id);
        if(!empty($alarmResult)){
            $this->showAlarmUi($house_id);
            foreach($alarmResult as $alarm){
                $this->mailService->alarmReminder($alarm);
            }
        }
    }

    public function getAlarmMe($userId){

        
        return AlarmMeModel::with([
                'house'=>function($query){
                    $query->select(["address",
                                    "city",
                                    "county",
                                    "state",
                                    "zip",
                                    "total_living_sqft",
                                    "year_built",
                                    "bed",
                                    "bath",
                                    'lot_acreage_sf',
                                    'county_value',
                                    "house_id"
                      ]);
                },
                "house.last_sale_details" => function ($query) {
                    $query->select(['house_id',
                            'sale_id',
                            'sale_date',
                            'sale_time',
                            'case_number',
                            'priceint',
                            'opening_bid',
                            'sale_type']);
                },])
              ->where('alarm_me.user_id',$userId)
             ->where('alarm_me.is_show_alarm',1)
             ->select(['alarm_id','alarm_me.house_id','alarm_me.user_id'])
            ->leftJoin('home_information', 'home_information.house_id', '=', 'alarm_me.house_id')
            ->whereNull('home_information.deleted_at')
            ->whereNull('alarm_me.deleted_at')->get();
    }

    public function updateAlarmMe($userId) {
        Log::info("AlarmMeService: updateAlarmMe called");
        $info['is_show_alarm'] = 0;
        return AlarmMeModel::where('user_id',$userId)->whereNull('deleted_at')->update($info);
    }

    public function showAlarmUi($house_id){
        $info['is_show_alarm'] = 1;
        return AlarmMeModel::where('house_id',$house_id)->whereNull('deleted_at')->update($info);
    }
}
