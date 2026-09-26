<?php

namespace App\Http\Controllers;

use App\Http\Validations\GeoValidations;
use App\Services\GeoService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
class GeoController extends Controller
{

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;
    private $geoService;



    public function __construct(
        Request $request
        , UserService $userService
        , GeoService $geoService
    ) {
        Log::info("GeoController: __construct called");
        $this->request                = $request;
        $this->userService            = $userService;
        $this->geoService             = $geoService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {
        Log::info("PropertyController: index called");


        $info = $this->geoService->findOneById($house_id);

        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreate($house_id)
    {
        Log::info("GeoController: updateOrCreate called");

        ## check input validation
        Log::info("GeoController: strategyUpdate update validation check");
        $rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        $this->geoService->updateOrCreate($house_id, $all);
        return response()->json(['status' => 'success','message' => __("messages.geo_codes")], 200);
    }
}
