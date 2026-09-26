<?php
/**
 * Created By Rativardhan Singh Sengar  8/31/19 4:33 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 8/27/19 11:39 PM
 */

namespace App\Http\Controllers;

use App\Models\CmaArvNotesModel;
use App\Services\UserService;
use App\Services\WholesaleNotesService;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;


class CmaArvNotesController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $wholesaleNotesService;
    private $userService;

    public function __construct(Request $request, WholesaleNotesService $wholesaleNotesService, UserService $userService) {
        Log::info("CmaArvNotesController: __construct called");
        $this->request               = $request;
        $this->wholesaleNotesService = $wholesaleNotesService;
        $this->userService           = $userService;

    }

    public function index($house_id) {

        Log::info("CmaArvNotesController: index called");
        $info = CmaArvNotesModel::with(['user'=>function ($query) {
            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
        }])->where('house_id','=',$house_id);

        $info = $info ->get();
        return response()->json(['status'  => 'success',
                                 'message' => ".",
                                 'data'    => $info,
                                ], 200);
    }


    public function create($house_id) {

        Log::info("CmaArvNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
                  'notes'     => 'required',];

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info= CmaArvNotesModel::create($all);

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

        Log::info("CmaArvNotesController: destroy called");
        try
        {
            $info = CmaArvNotesModel::find($id);
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

     /**
     * @param $house_id
     * @return JsonResponse
     */

    public function update($id) {

        Log::info("CmaArvNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
                  'notes'     => 'required',];

        $all             = $this->request->all();
        $all['id'] = $id;
        $all['house_id'] =$all['house_id'];
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info = CmaArvNotesModel::find($id);
        $info->notes = $all['notes'] ;
        $info->save();

        return response()->json(['status'  => 'success',
                                'data'=>$info,
                                 'message' => "Notes added successfully."], 200);
    }
}