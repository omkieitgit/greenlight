<?php
/**
 * Created By Rativardhan Singh Sengar  2/21/19 12:21 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/21/19 12:13 AM
 */

namespace App\Http\Controllers;


use App\Http\Validations\DepositWiredValidations;
use App\Models\DepositWiredModel;
use App\Services\DepositWiredService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class DepositWiredController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $depositWiredService;


    public function __construct(Request $request
        , DepositWiredService $depositWiredService

    )
    {
        Log::info("DepositWiredController: __construct called");
        $this->request             = $request;
        $this->depositWiredService = $depositWiredService;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        Log::info("DepositWiredController: index called");
        $info = $this->depositWiredService->findOneById($id);
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
        Log::info("DepositWiredController: indexAll called");
        $info = $this->depositWiredService->findAllByHouseId($house_id);
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
        Log::info("DepositWiredController: create called");

        ## check input validation
        Log::info("DepositWiredController: update validation check");
        $rules = DepositWiredValidations::createValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $all = $this->request->all();
        $all['added_by'] = 1;
        $info = $this->depositWiredService->create($this->request->all());

        return response()->json(['data' => ['id' => $info->deposit_wired_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        Log::info("DepositWiredController: update called");

        # first check record exists or not
        $info = $this->depositWiredService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("DepositWiredController: update validation check");
        $rules = DepositWiredValidations::updateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->depositWiredService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        Log::info("DepositWiredController: emailsAmDelete called");

        try
        {
            $info = DepositWiredModel::find($id);

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

