<?php
/**
 * Created By Rativardhan Singh Sengar  4/21/19 1:46 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/20/19 12:54 AM
 */

namespace App\Http\Controllers;


use App\Http\Validations\PropertyQueueListValidations;
use App\Services\PropertyQueueListDescriptionsService;
use App\Services\PropertyQueueListService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class PropertyQueueListController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyQueueListService;
    private $userService;


    public function __construct(Request $request
        , PropertyQueueListService $propertyQueueListService
        , UserService $userService
    )
    {
        Log::info("PropertyQueueListController: __construct called");
        $this->request                  = $request;
        $this->propertyQueueListService = $propertyQueueListService;
        $this->userService              = $userService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function myList()
    {
        Log::info("PropertyQueueListController: myList called");

        $data = $this->propertyQueueListService->myList();


        if (empty($data))
        {
            return response()->json(['status' => "success",'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        # create information
        return response()->json(['status' => 'success','data' => $data, 'message' => ""], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        Log::info("PropertyQueueListController: create called");

        ## check input validation
        Log::info("PropertyQueueListController: create validation check");
        $validator = Validator::make($this->request->all(), ['name' => 'required']);
        $validator->validate();

        # create information
        $info = $this->propertyQueueListService->create($this->request->all());
        return response()->json(['status' => 'success','data' => $info, 'message' => __("messages.record_saved")], 200);
    }


    /**
     * @param $list_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($list_id)
    {
        Log::info("PropertyQueueListController: update called");

        # first check record exists or not
        $info = $this->propertyQueueListService->findOneById($list_id);

        if (empty($info))
        {
            return response()->json(['status' => 'failed','message' => __("error_messages.record_not_exists")], 200);
        }

        if ($info->user_id != $this->userService->user_id())
        {
            return response()->json(['status' => 'failed','message' => __("messages.not_authorize")], 200);
        }

        # if record exists update information
        $update = [];
        $update['name'] = $this->request->input('name');
        $u = $info->update($update);

        return response()->json(['status' => 'success','data' => $info,'message' => __("messages.record_saved")], 200);
    }


    /**
     * @param $list_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($list_id)
    {
        Log::info("PropertyQueueListController: delete called");

        try
        {

            $info = $this->propertyQueueListService->findOneById($list_id);

            if (!empty($info))
            {
                if ($info->user_id != $this->userService->user_id())
                {
                    return response()->json(['status' => 'failed','message' => __("messages.not_authorize")], 200);
                }

                $info->delete();
                return response()->json(['status' => 'success','message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['status' => 'failed','message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['status' => 'failed','message' => __("error_messages.something_wrong")], 400);
        }
    }

}