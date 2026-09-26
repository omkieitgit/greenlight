<?php
/**
 * Created By Rativardhan Singh Sengar  2/4/19 12:36 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/3/19 11:19 PM
 */

namespace App\Http\Controllers;


use App\Http\Validations\WholeSaleBuyerValidations;
use App\Models\EmailsAmModel;
use App\Models\EmailsCompanyTeamMemberModel;
use App\Models\EmailsFunderLenderModel;
use App\Models\EmailsTimeLeftNoticeModel;
use App\Models\WholesaleBuyerNModel;
use App\Services\AdditionalCostWiredService;
use App\Services\AmountWiredToCloseService;
use App\Services\DepositWiredService;
use App\Services\EmailsAmService;
use App\Services\EmailsCompanyTeamMemberService;
use App\Services\EmailsFunderLenderService;
use App\Services\EmailsTimeLeftNoticeService;
use App\Services\HomeBuyersAlias2DarrenService;
use App\Services\PropertyAcquisitionAtoBFirstService;
use App\Services\PropertyAcquisitionAtoBSecondService;
use App\Services\PropertyAcquisitionBtoCFirstService;
use App\Services\PropertyAcquisitionBtoCSecondService;
use App\Services\TrusteeExtraService;
use App\Services\TrusteeService;
use App\Services\UserService;
use App\Services\WholesaleBuyerNService;
use App\Services\WholesaleBuyerNTotalService;
use App\Services\WholesaleBuyerStrategyExtraService;
use App\Services\WholesaleBuyerStrategyService;
use App\Services\WholesaleBuyerStrategyServiceExtra;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class WholesaleBuyerController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $wholesaleBuyerStrategyService;
    private $wholesaleBuyerStrategyExtraService;
    private $emailsAmService;
    private $emailsCompanyTeamMemberService;
    private $emailsFunderLenderService;
    private $emailsTimeLeftNoticeService;
    private $wholesaleBuyerNService;
    private $wholesaleBuyerNTotalService;
    private $trusteeService;
    private $trusteeExtraService;
    private $depositWiredService;
    private $additionalCostWiredService;
    private $amountWiredToCloseService;
    private $propertyAcquisitionAtoBFirstService;
    private $propertyAcquisitionAtoBSecondService;
    private $homeBuyersAlias2DarrenService;
    private $propertyAcquisitionBtoCFirstService;
    private $propertyAcquisitionBtoCSecondService;
    private $userService;

    public function __construct(Request $request
        , WholesaleBuyerStrategyService $wholesaleBuyerStrategyService
        , WholesaleBuyerStrategyExtraService $wholesaleBuyerStrategyExtraService
        , EmailsAmService $emailsAmService
        , EmailsCompanyTeamMemberService $emailsCompanyTeamMemberService
        , EmailsFunderLenderService $emailsFunderLenderService
        , EmailsTimeLeftNoticeService $emailsTimeLeftNoticeService
        , WholesaleBuyerNService $wholesaleBuyerNService
        , WholesaleBuyerNTotalService $wholesaleBuyerNTotalService
        , TrusteeService $trusteeService
        , TrusteeExtraService $trusteeExtraService
        , DepositWiredService $depositWiredService
        , AdditionalCostWiredService $additionalCostWiredService
        , AmountWiredToCloseService $amountWiredToCloseService
        , PropertyAcquisitionAtoBFirstService $propertyAcquisitionAtoBFirstService
        , PropertyAcquisitionAtoBSecondService $propertyAcquisitionAtoBSecondService
        , HomeBuyersAlias2DarrenService $homeBuyersAlias2DarrenService
        , PropertyAcquisitionBtoCFirstService $propertyAcquisitionBtoCFirstService
        , PropertyAcquisitionBtoCSecondService $propertyAcquisitionBtoCSecondService
        , UserService $userService
    )
    {
        Log::info("WholesaleBuyerController: __construct called");
        $this->request                              = $request;
        $this->wholesaleBuyerStrategyService        = $wholesaleBuyerStrategyService;
        $this->wholesaleBuyerStrategyExtraService   = $wholesaleBuyerStrategyExtraService;
        $this->emailsAmService                      = $emailsAmService;
        $this->emailsCompanyTeamMemberService       = $emailsCompanyTeamMemberService;
        $this->emailsFunderLenderService            = $emailsFunderLenderService;
        $this->emailsTimeLeftNoticeService          = $emailsTimeLeftNoticeService;
        $this->wholesaleBuyerNService               = $wholesaleBuyerNService;
        $this->wholesaleBuyerNTotalService          = $wholesaleBuyerNTotalService;
        $this->trusteeService                       = $trusteeService;
        $this->trusteeExtraService                  = $trusteeExtraService;
        $this->depositWiredService                  = $depositWiredService;
        $this->additionalCostWiredService           = $additionalCostWiredService;
        $this->amountWiredToCloseService            = $amountWiredToCloseService;
        $this->propertyAcquisitionAtoBFirstService  = $propertyAcquisitionAtoBFirstService;
        $this->propertyAcquisitionAtoBSecondService = $propertyAcquisitionAtoBSecondService;
        $this->homeBuyersAlias2DarrenService        = $homeBuyersAlias2DarrenService;
        $this->propertyAcquisitionBtoCFirstService  = $propertyAcquisitionBtoCFirstService;
        $this->propertyAcquisitionBtoCSecondService = $propertyAcquisitionBtoCSecondService;
        $this->userService                          = $userService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {
        Log::info("WholesaleBuyerController: indexAll called");

        $infowbss = $this->wholesaleBuyerStrategyService->findAllInformation($house_id);

        $info['strategy']                   = $infowbss ? $infowbss->toArray() : [];
        $extra                              = $this->wholesaleBuyerStrategyExtraService->findOneById($house_id);
        $extra                              = $extra ? $extra->toArray() : [];
        $info['strategy']                   = array_merge($info['strategy'], $extra);
        $info['emails_am']                  = $this->emailsAmService->findAllByHouseId($house_id);
        $info['emails_company_team_member'] = $this->emailsCompanyTeamMemberService->findAllByHouseId($house_id);
        $info['emails_funder_lender']       = $this->emailsFunderLenderService->findAllByHouseId($house_id);
        $info['emails_time_left_notice']    = $this->emailsTimeLeftNoticeService->findAllByHouseId($house_id);
        $info['sthb']                       = $this->wholesaleBuyerNService->findAllByHouseId($house_id);
        $info['sthb_total']                 = $this->wholesaleBuyerNTotalService->findOneById($house_id);
        $info['trustee']                    = $this->trusteeService->findOneById($house_id);
        $info['trustee']                    = $info['trustee'] ? $info['trustee']->toArray() : [];
        $extra                              = $this->trusteeExtraService->findOneById($house_id);
        $extra                              = $extra ? $extra->toArray() : [];
        $info['trustee']                    = array_merge($info['trustee'], $extra);
        $info['deposit_wired']              = $this->depositWiredService->findAllByHouseId($house_id);
        $info['additional_cost_wired']      = $this->additionalCostWiredService->findAllByHouseId($house_id);
        $info['amount_wired_to_close']      = $this->amountWiredToCloseService->findAllByHouseId($house_id);

        $info_first     = $this->propertyAcquisitionAtoBFirstService->findOneById($house_id);
        $info_second    = $this->propertyAcquisitionAtoBSecondService->findOneById($house_id);
        $arr1           = (array)json_decode($info_first);
        $arr2           = (array)json_decode($info_second);
        $info['a_to_b'] = array_merge($arr1, $arr2);

        $info_first_b_to_c  = $this->propertyAcquisitionBtoCFirstService->findOneById($house_id);
        $info_second_b_to_c = $this->propertyAcquisitionBtoCSecondService->findOneById($house_id);
        $arr1               = (array)json_decode($info_first_b_to_c);
        $arr2               = (array)json_decode($info_second_b_to_c);
        $info['b_to_c']     = array_merge($arr1, $arr2);

        $darren = $this->homeBuyersAlias2DarrenService->findOneById($house_id);
        $darren = (array)json_decode($darren);
        #ToDo: Add codition here only for Admin level access user can update this
        $info['darren'] = $darren;

        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function strategy($house_id)
    {
        Log::info("WholesaleBuyerController: strategy called");

        $info = $this->wholesaleBuyerStrategyService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function strategyUpdate($house_id)
    {
        Log::info("WholesaleBuyerController: strategyUpdate called");

        ## check input validation
        Log::info("WholesaleBuyerController: strategyUpdate update validation check");
        $rules = WholeSaleBuyerValidations::strategyValidation();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $this->wholesaleBuyerStrategyService->updateOrCreate($all);
        $this->wholesaleBuyerStrategyExtraService->updateOrCreate($all);

        return response()->json(['message' => __("messages.record_saved")], 200);
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsAm($id)
    {
        Log::info("WholesaleBuyerController: emailsAm called");

        $info = $this->emailsAmService->findOneById($id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsAmAll($house_id)
    {
        Log::info("WholesaleBuyerController: emailsAmAll called");

        $info = $this->emailsAmService->findAllByHouseId($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsAmCreate()
    {
        Log::info("WholesaleBuyerController: emailsAmCreate called");

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsAmCreateValidation();

        $all = $this->request->all();
        unset($all['user_id']);
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        ## ToDo: Ask willow that, can we allow only register email in AM. so we will send buy it and other emails to only
        ## register emails not all.

        // Check email ID exists in user table or not
        $is_exists = $this->userService->isEmailExists($all['email']);
        if($is_exists == null)
        {
            $all['user_id'] = 0;
        }
        else
        {
            $all['user_id'] = $is_exists->id;
        }

        # update information
        $info = $this->emailsAmService->create($all);

        return response()->json(['data' => ['id' => $info->emails_am_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsAmUpdate($id)
    {
        Log::info("WholesaleBuyerController: emailsAmUpdate called");

        # first check record exists or not
        $info = $this->emailsAmService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsAmUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->emailsAmService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsAmDelete($id)
    {
        Log::info("WholesaleBuyerController: emailsAmDelete called");

        try
        {
            $info = EmailsAmModel::find($id);

            if ($info == true)
            {
                EmailsAmModel::where('emails_am_id',$id)->update(['is_deleted'=>1]);
                
                return response()->json(['message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsCompanyTeamMember($id)
    {
        Log::info("WholesaleBuyerController: emailsCompanyTeamMember called");

        $info = $this->emailsCompanyTeamMemberService->findOneById($id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsCompanyTeamMemberAll($house_id)
    {
        Log::info("WholesaleBuyerController: emailsCompanyTeamMemberAll called");

        $info = $this->emailsCompanyTeamMemberService->findAllByHouseId($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsCompanyTeamMemberCreate()
    {
        Log::info("WholesaleBuyerController: emailsCompanyTeamMemberCreate called");

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsCompanyTeamMemberCreateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->emailsCompanyTeamMemberService->create($this->request->all());

        return response()->json(['data' => ['id' => $info->emails_company_team_member_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsCompanyTeamMemberUpdate($id)
    {
        Log::info("WholesaleBuyerController: emailsCompanyTeamMemberUpdate called");

        # first check record exists or not
        $info = $this->emailsCompanyTeamMemberService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsCompanyTeamMemberUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->emailsCompanyTeamMemberService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsCompanyTeamMemberDelete($id)
    {
        Log::info("WholesaleBuyerController: emailsCompanyTeamMemberDelete called");

        try
        {
            $info = EmailsCompanyTeamMemberModel::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsFunderLender($id)
    {
        Log::info("WholesaleBuyerController: emailsFunderLender called");

        $info = $this->emailsFunderLenderService->findOneById($id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsFunderLenderAll($house_id)
    {
        Log::info("WholesaleBuyerController: emailsFunderLender called");

        $info = $this->emailsFunderLenderService->emailsFunderLenderAll($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonRespo                nse
     */
    public function emailsFunderLenderCreate()
    {
        Log::info("WholesaleBuyerController: emailsFunderLenderCreate called");

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsFunderLenderCreateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->emailsFunderLenderService->create($this->request->all());

        return response()->json(['data' => ['id' => $info->emails_funder_lender_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsFunderLenderUpdate($id)
    {
        Log::info("WholesaleBuyerController: emailsFunderLenderUpdate called");

        # first check record exists or not
        $info = $this->emailsFunderLenderService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsFunderLenderUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->emailsFunderLenderService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsFunderLenderDelete($id)
    {
        Log::info("WholesaleBuyerController: emailsFunderLenderDelete called");

        try
        {
            $info = EmailsFunderLenderModel::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsTimeLeftNotice($id)
    {
        Log::info("WholesaleBuyerController: emailsTimeLeftNotice called");

        $info = $this->emailsTimeLeftNoticeService->findOneById($id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsTimeLeftNoticeAll($house_id)
    {
        Log::info("WholesaleBuyerController: emailsTimeLeftNoticeAll called");

        $info = $this->emailsTimeLeftNoticeService->findAllByHouseId($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsTimeLeftNoticeCreate()
    {
        Log::info("WholesaleBuyerController: emailsTimeLeftNoticeCreate called");

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsTimeLeftNoticeCreateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->emailsTimeLeftNoticeService->create($this->request->all());

        return response()->json(['data' => ['id' => $info->emails_time_left_notice_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsTimeLeftNoticeUpdate($id)
    {
        Log::info("WholesaleBuyerController: emailsTimeLeftNoticeUpdate called");

        # first check record exists or not
        $info = $this->emailsTimeLeftNoticeService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::emailsTimeLeftNoticeUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->emailsTimeLeftNoticeService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailsTimeLeftNoticeDelete($id)
    {
        Log::info("WholesaleBuyerController: emailsTimeLeftNoticeDelete called");

        try
        {
            $info = EmailsTimeLeftNoticeModel::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerN($id)
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerN called");

        $info = $this->wholesaleBuyerNService->findOneById($id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerNAll($house_id)
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerNAll called");

        $info = $this->wholesaleBuyerNService->findAllByHouseId($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerNCreate()
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerNCreate called");

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules     = WholeSaleBuyerValidations::wholesaleBuyerNCreateValidation();
        $all       = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        ## create user, if not exists into database as non_register user.
        $inviteToInfo = $this->userService->isRegisterIfNotExists($all['email']);

        # update information
        $all['user_id'] = $inviteToInfo->id;
        $info           = $this->wholesaleBuyerNService->create($all);

        return response()->json(['data' => ['id' => $info->wholesale_buyer_n_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerNUpdate($id)
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerNUpdate called");

        # first check record exists or not
        $info = $this->wholesaleBuyerNService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("WholesaleBuyerController: update validation check");
        $rules = WholeSaleBuyerValidations::wholesaleBuyerNUpdateValidation();

        $all = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        ## create user, if not exists into database as non_register user.
        $inviteToInfo = $this->userService->isRegisterIfNotExists($all['email']);

        # update information
        $all['user_id'] = $inviteToInfo->id;
        $this->wholesaleBuyerNService->update($id, $all);

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerNDelete($id)
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerNDelete called");

        try
        {
            $info = WholesaleBuyerNModel::find($id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerNTotal($house_id)
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerNTotal called");


        $info = $this->wholesaleBuyerNTotalService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesaleBuyerNTotalUpdate($house_id)
    {
        Log::info("WholesaleBuyerController: wholesaleBuyerNTotalUpdate called");

        ## check input validation
        Log::info("WholesaleBuyerController: wholesaleBuyerNTotalUpdate update validation check");
        $rules = WholeSaleBuyerValidations::wholesaleBuyerNTotalUpdateValidation();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $this->wholesaleBuyerNTotalService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }
}
