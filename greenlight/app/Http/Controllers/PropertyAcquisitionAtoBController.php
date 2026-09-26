<?php

namespace App\Http\Controllers;

use App\Http\Validations\PropertyAcquisitionAtoBValidations;
use App\Services\HomeBuyersAlias2DarrenService;
use App\Services\PropertyAcquisitionAtoBFirstService;
use App\Services\PropertyAcquisitionAtoBSecondService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;


class PropertyAcquisitionAtoBController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyAcquisitionAtoBFirstService;
    private $propertyAcquisitionAtoBSecondService;
    private $homeBuyersAlias2DarrenService;


    public function __construct(Request $request
        , PropertyAcquisitionAtoBFirstService $propertyAcquisitionAtoBFirstService
        , PropertyAcquisitionAtoBSecondService $propertyAcquisitionAtoBSecondService
        , HomeBuyersAlias2DarrenService $homeBuyersAlias2DarrenService

    )
    {
        Log::info("PropertyAcquisitionAtoBController: __construct called");
        $this->request                              = $request;
        $this->propertyAcquisitionAtoBFirstService  = $propertyAcquisitionAtoBFirstService;
        $this->propertyAcquisitionAtoBSecondService = $propertyAcquisitionAtoBSecondService;
        $this->homeBuyersAlias2DarrenService        = $homeBuyersAlias2DarrenService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function index($house_id)
    {
        Log::info("PropertyAcquisitionAtoBController: propertyAcquisitionAtoBIndex called");

        $info_first  = $this->propertyAcquisitionAtoBFirstService->findOneById($house_id);
        $info_second = $this->propertyAcquisitionAtoBSecondService->findOneById($house_id);
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

        ## TODO: homebuyer_info.php file has some field can only be updated by darren, Please add condition for this.
        // (in_array($twot_uid, $darren_acees_user_id))

        Log::info("PropertyAcquisitionAtoBController: propertyAcquisitionAtoBUpdate called");

        ## check input validation
        Log::info("propertyAcquisitionAtoBIndex: update validation check");
        $rules = PropertyAcquisitionAtoBValidations::updateValidation();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        # update information
        $this->propertyAcquisitionAtoBFirstService->updateOrCreate($all);
        $this->propertyAcquisitionAtoBSecondService->updateOrCreate($all);
        $this->homeBuyersAlias2DarrenService->updateOrCreate($all);

        #ToDo: Add codition here only for Admin level access user can update this
        $this->homeBuyersAlias2DarrenService->updateOrCreate($all);
        return response()->json(['message' => __("messages.record_saved")], 200);

    }

}