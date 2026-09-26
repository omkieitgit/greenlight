<?php
/**
 * Created By Rativardhan Singh Sengar  3/7/19 10:30 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/7/19 10:28 PM
 */

namespace App\Http\Controllers;

use App\Http\Validations\PropertyAcquisitionBtoCValidations;
use App\Services\HomeBuyersAlias2DarrenService;
use App\Services\PropertyAcquisitionBtoCFirstService;
use App\Services\PropertyAcquisitionBtoCSecondService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use App\Services\PropertyService;
use Illuminate\Http\Request;


class PropertyAcquisitionBtoCController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $PropertyAcquisitionBtoCFirstService;
    private $PropertyAcquisitionBtoCSecondService;
    private $homeBuyersAlias2DarrenService;

    /**
     * PropertyAcquisitionBtoCController constructor.
     * @param Request $request
     * @param PropertyAcquisitionBtoCFirstService $PropertyAcquisitionBtoCFirstService
     * @param PropertyAcquisitionBtoCSecondService $PropertyAcquisitionBtoCSecondService
     * @param HomeBuyersAlias2DarrenService $homeBuyersAlias2DarrenService
     */
    public function __construct(Request $request
        , PropertyAcquisitionBtoCFirstService $PropertyAcquisitionBtoCFirstService
        , PropertyAcquisitionBtoCSecondService $PropertyAcquisitionBtoCSecondService
        , HomeBuyersAlias2DarrenService $homeBuyersAlias2DarrenService

    )
    {
        Log::info("PropertyAcquisitionBtoCController: __construct called");
        $this->request                              = $request;
        $this->PropertyAcquisitionBtoCFirstService  = $PropertyAcquisitionBtoCFirstService;
        $this->PropertyAcquisitionBtoCSecondService = $PropertyAcquisitionBtoCSecondService;
        $this->homeBuyersAlias2DarrenService        = $homeBuyersAlias2DarrenService;

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function index($house_id)
    {
        Log::info("PropertyAcquisitionBtoCController: PropertyAcquisitionBtoCIndex called");

        $info_first  = $this->PropertyAcquisitionBtoCFirstService->findOneById($house_id);
        $info_second = $this->PropertyAcquisitionBtoCSecondService->findOneById($house_id);
        $darren      = $this->homeBuyersAlias2DarrenService->findOneById($house_id);
        $arr1        = (array)json_decode($info_first);
        $arr2        = (array)json_decode($info_second);
        $darren      = (array)json_decode($darren);
        $info        = array_merge($arr1, $arr2);
        $info        = array_merge($info, $darren);

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
    public function updateOrCreate($house_id)
    {

        Log::info("PropertyAcquisitionBtoCController: PropertyAcquisitionBtoCUpdate called");

        ## check input validation
        Log::info("PropertyAcquisitionBtoCIndex: update validation check");
        $rules = PropertyAcquisitionBtoCValidations::updateValidation();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        # update information
        $this->PropertyAcquisitionBtoCFirstService->updateOrCreate($all);
        $this->PropertyAcquisitionBtoCSecondService->updateOrCreate($all['irsTaxForm']);

        #ToDo: Add codition here only for Admin level access user can update this
        $this->homeBuyersAlias2DarrenService->updateOrCreate($all);
        return response()->json(['message' => __("messages.record_saved")], 200);

    }

}
