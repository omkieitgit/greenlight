<?php
/**
 * Created By Rativardhan Singh Sengar  5/26/19 10:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/26/19 9:57 PM
 */

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Models\CommonNotesModel;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;


class CommonNotesController extends Controller {

    /**
     * @var Request
     */
    private $request;
    private $userService;

    /**
     * CommonNotesController constructor.
     * @param Request $request
     * @param UserService $userService
     */
    public function __construct(Request $request
        , UserService $userService

    ) {
        Log::info("CommonNotesController: __construct called");
        $this->request                = $request;
        $this->userService            = $userService;
    }


    /**
     * @param $house_id
     * @return JsonResponse
     */
    public function index($house_id,$note_type="") {

        Log::info("CommonNotesController: index called");
        $info = CommonNotesModel::where('house_id','=',$house_id);

        if($note_type){
          $info->where("note_type",$note_type);
        }

        // ->where("user_id",$this->userService->user_id())
        //;

        $info = $info ->get();
        return response()->json(['status'  => 'success',
            'message' => ".",
            'data'    => $info,
        ], 200);
    }

    /**
     * @param $house_id
     * @return JsonResponse
     */
    public function store($house_id) {

        Log::info("CommonNotesController: create called");
        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
            'notes'     => 'required',
            'note_type'     => 'required|max:255',

            ];
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info = CommonNotesModel::create($all);
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

        Log::info("CommonNotesController: noteDelete called");
        try
        {
            $info = CommonNotesModel::find($id);
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

        Log::info("CommonNotesController: create called");
        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
            'notes'     => 'required',
            'note_type'     => 'required|max:255',

            ];
        $all            = $this->request->all();
        $all['id']      = $id;
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info = CommonNotesModel::find($id);
        $info->notes = $all['notes'] ;
        $info->save();

        return response()->json(['status'  => 'success',
            'data'=>$info,
            'message' => "Notes added successfully."], 200);
    }

}
