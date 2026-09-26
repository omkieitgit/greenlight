<?php
/**
 * Created By Rativardhan Singh Sengar  5/26/19 10:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/26/19 9:57 PM
 */

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Models\ManagerNotesModel;
use App\Services\MailService;
use App\Services\UserService;
use App\Services\ManagerNotesService;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ManagerNotesController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $mangerNotesService;
    private $userService;

    public function __construct(Request $request, ManagerNotesService $wholesaleNotesService, UserService $userService) {
        Log::info("ManagerNotesController: __construct called");
        $this->request               = $request;
        $this->wholesaleNotesService = $wholesaleNotesService;
        $this->userService           = $userService;

    }

    public function index($type,$house_id) {

        Log::info("ManagerNotesController: index called");

        // if ($this->userService->is_wholesale_buyer()) {
        //     $data = $this->wholesaleNotesService->myList($house_id);
        // }
       
        $data = $this->wholesaleNotesService->list($type,$house_id);
        return response()->json(['status'  => 'success',
                                 'message' => ".",
                                 "data"    => $data], 200);
    }


    public function create($house_id) {

        Log::info("ManagerNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
                  'notes'     => 'required',];

        $all             = $this->request->all();
        $all['house_id'] = $house_id;

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info=$this->wholesaleNotesService->create($all);
        return response()->json(['status'  => 'success',
                                 'message' => "Notes added successfully.",
                                'data'=>$info], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

        Log::info("ManagerNotesController: destroy called");
        try
        {
            $info = ManagerNotesModel::find($id);
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

    function update($id){
        Log::info("ManagerNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
            'notes'     => 'required',
            'note_type'     => 'required|max:255',

            ];
        $all            = $this->request->all();
       // $all['id']      = $id;
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        $validator->validate();

        
        $info = ManagerNotesModel::find($id);
        $info->notes = $all['notes'] ;
        $info->save();

       // $info = ManagerNotesModel::where('id', $id)->update($all);
        return response()->json(['status'  => 'success',
            'data'=>$info,
            'message' => "Notes added successfully."], 200);
    }

}