<?php
/**
 * Created By Rativardhan Singh Sengar  2/25/19 11:46 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/25/19 11:37 PM
 */

namespace App\Http\Controllers;


use App\Http\Validations\AmountWiredToCloseValidations;
use App\Models\AmountWiredToCloseModel;
use App\Services\AmountWiredToCloseService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class AmountWiredToCloseController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $AmountWiredToCloseService;


    public function __construct(Request $request
        , AmountWiredToCloseService $AmountWiredToCloseService

    )
    {
        Log::info("AmountWiredToCloseController: __construct called");
        $this->request             = $request;
        $this->AmountWiredToCloseService = $AmountWiredToCloseService;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        Log::info("AmountWiredToCloseController: index called");
        $info = $this->AmountWiredToCloseService->findOneById($id);
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
        Log::info("AmountWiredToCloseController: indexAll called");
        $info = $this->AmountWiredToCloseService->findAllByHouseId($house_id);
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
        Log::info("AmountWiredToCloseController: create called");

        ## check input validation
        Log::info("AmountWiredToCloseController: update validation check");
        $rules = AmountWiredToCloseValidations::createValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $all = $this->request->all();
        $all['added_by'] = 1;
        $info = $this->AmountWiredToCloseService->create($this->request->all());

        return response()->json(['data' => ['id' => $info->amount_wired_to_close_id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        Log::info("AmountWiredToCloseController: update called");

        # first check record exists or not
        $info = $this->AmountWiredToCloseService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("AmountWiredToCloseController: update validation check");
        $rules = AmountWiredToCloseValidations::updateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->AmountWiredToCloseService->update($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        Log::info("AmountWiredToCloseController: emailsAmDelete called");

        try
        {
            $info = AmountWiredToCloseModel::find($id);

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

