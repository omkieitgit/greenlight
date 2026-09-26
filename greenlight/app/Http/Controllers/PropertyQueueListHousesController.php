<?php
/**
 * Created By Rativardhan Singh Sengar  4/21/19 1:46 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/20/19 12:54 AM
 */

namespace App\Http\Controllers;


use App\Http\Validations\PropertyQueueListHousesValidations;
use App\Models\PropertyQueueListHousesModel;
use App\Services\PropertyQueueListHousesService;
use App\Services\PropertyQueueListService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class PropertyQueueListHousesController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyQueueListService;
    private $propertyQueueListHousesService;
    private $userService;


    public function __construct(Request $request
        , PropertyQueueListHousesService $propertyQueueListHousesService
        , PropertyQueueListService $propertyQueueListService
        , UserService $userService
    )
    {
        Log::info("PropertyQueueListHousesController: __construct called");
        $this->request                        = $request;
        $this->propertyQueueListService = $propertyQueueListService;
        $this->propertyQueueListHousesService = $propertyQueueListHousesService;
        $this->userService                    = $userService;
    }


    /**
     * @param $list_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreate($list_id = NULL)
    {
        Log::info("PropertyQueueListHousesController: updateOrCreate called");

        # first check record exists or not
        if(!empty($list_id))
        {
            $info = $this->propertyQueueListService->findOneById($list_id);

            if (empty($info))
            {
                return response()->json(['status' => 'failed','message' => __("error_messages.record_not_exists")], 200);
            }

            if ($info->user_id != $this->userService->user_id())
            {
                return response()->json(['status' => 'failed','message' => __("messages.not_authorize")], 200);
            }
        }


        ## check input validation
        Log::info("PropertyQueueListHousesController: create validation check");
        $rules = PropertyQueueListHousesValidations::updateOrCreate();
        $all = $this->request->all();
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $where = [];
        $where['list_id'] = $list_id;
        $where['house_id'] = $all['house_id'];
        $info = $this->propertyQueueListHousesService->updateOrCreate($where, $all);
        return response()->json(['status' => 'success','data' => $info, 'message' => __("messages.record_saved")], 200);
    }



    public function moveToList()
    {

        $property_queue_list_houses_ids = $this->request->input('property_queue_list_houses_ids');
        $list_id = $this->request->input('list_id');

        $info = $this->propertyQueueListService->findOneById($list_id);
        if (empty($info))
        {
            return response()->json(['status' => 'failed','message' => __("error_messages.record_not_exists")], 200);
        }

        if ($info->user_id != $this->userService->user_id())
        {
            return response()->json(['status' => 'failed','message' => __("messages.not_authorize")], 200);
        }

        $property_queue_list_houses_ids_arr = explode(",",$property_queue_list_houses_ids);

        ## update list_id into those record if they belong to this users.
        $info = [];
        $info['list_id'] =$list_id;
        $is_update = $this->propertyQueueListHousesService->moveToList($property_queue_list_houses_ids_arr, $info);
        return response()->json(['status' => 'success','data' => ['total_update' =>$is_update ], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $property_queue_list_houses_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($property_queue_list_houses_id)
    {
        Log::info("AmountWiredToCloseController: emailsAmDelete called");

        try
        {

            $info = $this->propertyQueueListHousesService->findOneById($property_queue_list_houses_id);

            if (!empty($info))
            {
                if ($info->user_id != $this->userService->user_id())
                {
                    return response()->json(['status' => 'failed', 'message' => __("messages.not_authorize")], 200);
                }

                $info->delete();
                return response()->json(['status' => 'success', 'message' => __("messages.record_delete")
                                         , 'data' => []], 200);
            }
            else
            {
                return response()->json(['status' => 'failed', 'message' => __("error_messages.record_not_exists"), 'data' => []], 200);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['status' => 'failed', 'message' => __("error_messages.something_wrong")], 400);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info("PropertyQueueListHousesController: queueList called");


        ## If user added any property into empty list.
        $where = [
            'user_id'=>$this->userService->user_id(),
            'list_id'=> NULL,
            ];

        $data = $this->propertyQueueListHousesService->list($where);


        if (empty($data))
        {
            return response()->json(['status' => "success", 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        # create information
        return response()->json(['status' => 'success', 'data' => $data, 'message' => ""], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function list($list_id)
    {
        Log::info("PropertyQueueListHousesController: queueList called");


        ## If user added any property into empty list.
        $where = [
            'user_id'=>$this->userService->user_id(),
            'list_id'=> $list_id, ## doesn't matter if he pass something else, record will not come . Combination is unique
        ];

        $data = $this->propertyQueueListHousesService->list($where);


        if (empty($data))
        {
            return response()->json(['status' => "success", 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        # create information
        return response()->json(['status' => 'success', 'data' => $data, 'message' => ""], 200);
    }

}