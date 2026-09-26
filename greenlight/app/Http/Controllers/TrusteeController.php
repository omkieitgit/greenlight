<?php
/**
 * Created By Rativardhan Singh Sengar  2/21/19 12:09 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 9:13 PM
 */

namespace App\Http\Controllers;


use App\Http\Validations\TrusteeValidations;
use App\Services\TrusteeExtraService;
use App\Services\TrusteeService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class TrusteeController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $trusteeService;
    private $trusteeExtraService;


    public function __construct(Request $request
        , TrusteeService $trusteeService
        , TrusteeExtraService $trusteeExtraService
    )
    {
        Log::info("TrusteeController: __construct called");
        $this->request             = $request;
        $this->trusteeService      = $trusteeService;
        $this->trusteeExtraService = $trusteeExtraService;

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {
        Log::info("TrusteeController: indexAll called");
        $info  = $this->trusteeService->findAllInformation($house_id);
        $info  = $info ? $info->toArray() : [];
        $extra = $this->trusteeExtraService->findOneById($house_id);
        $extra = $extra ? $extra->toArray() : [];
        $info  = array_merge($info, $extra);

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
    public function updateOrCreate($house_id)
    {
        Log::info("TrusteeController: strategyUpdate called");

        ## check input validation
        Log::info("TrusteeController: strategyUpdate update validation check");
        $rules = TrusteeValidations::updateOrCreateValidation();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $this->trusteeService->updateOrCreate($all);
        $this->trusteeExtraService->updateOrCreate($all);

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

}

