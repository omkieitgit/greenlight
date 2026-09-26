<?php
/**
 * Created By Rativardhan Singh Sengar  8/27/19 10:14 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 8/27/19 10:13 PM
 */

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Models\BorrowerNotesModel;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class BorrowerNotesController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;

    public function __construct(Request $request
        , UserService $userService) {
        Log::info("BorrowerNotesController: __construct called");
        $this->request               = $request;
        $this->userService           = $userService;

    }

    public function index($house_id) {

        Log::info("BorrowerNotesController: index called");
        $info = BorrowerNotesModel::with(['user'=>function ($query) {
            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
        }])->where('house_id','=',$house_id);


        $info = $info ->get();
        return response()->json(['status'  => 'success',
                                 'message' => ".",
                                 'data'    => $info,
                                ], 200);
    }


    public function create($house_id) {

        Log::info("BorrowerNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
                  'notes'     => 'required',];

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info=BorrowerNotesModel::create($all);

        return response()->json(['status'  => 'success',
                                'data'=>$info,
                                'message' => "Notes added successfully."], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Log::info("BorrowerNotesController: destroy called");
        try
        {
            $info = BorrowerNotesModel::find($id);
            if ($info != null)
            {
                // check user id of notes
                if($info->user_id != $this->userService->user_id())
                {
                    return response()->json(['status'  => 'failed','message' => __("messages.not_delete")], 200);
                }
                $info->delete();
                return response()->json(['status'  => 'success','message' => __("messages.record_delete")
                ], 200);
            }
            else
            {
                return response()->json(['status'  => 'failed','message' => __("error_messages.record_not_exists")], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status'  => 'failed','message' => __("error_messages.something_wrong")], 400);
        }
    }

    public function update($id) {

        Log::info("BorrowerNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
                  'notes'     => 'required',];

        $all             = $this->request->all();
        $all['id'] = $id;
        $all['house_id'] =$all['house_id'];
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info = BorrowerNotesModel::find($id);
        $info->notes = $all['notes'] ;
        $info->save();

        return response()->json(['status'  => 'success',
                                'data'=>$info,
                                 'message' => "Notes added successfully."], 200);
    }
}