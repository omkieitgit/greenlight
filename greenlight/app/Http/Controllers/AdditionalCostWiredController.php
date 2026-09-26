<?php
/**
 * Created By Rativardhan Singh Sengar  2/25/19 11:31 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/25/19 9:16 PM
 */

namespace App\Http\Controllers;


use App\Http\Validations\AdditionalCostWiredValidations;
use App\Models\AdditionalCostWiredModel;
use App\Services\AdditionalCostWiredService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class AdditionalCostWiredController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $AdditionalCostWiredService;


    public function __construct(Request $request
        , AdditionalCostWiredService $AdditionalCostWiredService

    )
    {
        Log::info("AdditionalCostWiredController: __construct called");
        $this->request             = $request;
        $this->AdditionalCostWiredService = $AdditionalCostWiredService;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        Log::info("AdditionalCostWiredController: index called");
        $info = $this->AdditionalCostWiredService->findOneById($id);
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
    public function all($house_id)
    {
        Log::info("AdditionalCostWiredController: indexAll called");
        $info = $this->AdditionalCostWiredService->findAllByHouseId($house_id);
        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        Log::info("AdditionalCostWiredController: create called");

        ## check input validation
        Log::info("AdditionalCostWiredController: update validation check");
        $rules = AdditionalCostWiredValidations::createValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $all = $this->request->all();
        $all['added_by'] = 1;
        $info = $this->AdditionalCostWiredService->create($this->request->all());

        return response()->json(['data' => ['id' => $info->additional_cost_wired_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        Log::info("AdditionalCostWiredController: update called");

        # first check record exists or not
        $info = $this->AdditionalCostWiredService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("AdditionalCostWiredController: update validation check");
        $rules = AdditionalCostWiredValidations::updateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->AdditionalCostWiredService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        Log::info("AdditionalCostWiredController: emailsAmDelete called");

        try
        {
            $info = AdditionalCostWiredModel::find($id);

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

}

