<?php

namespace App\Http\Controllers;

use App\Models\UserInviteSettingsModel;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
Use Log;
use Validator;
class UserInviteSettingsController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $userService;
    private $request;

    public function __construct(UserService $userService, Request $request) {
        Log::info("UserInviteSettingsController: __construct called");
        $this->userService = $userService;
        $this->request     = $request;

    }

    public function index()
    {
        Log::info("UserInviteSettingsController: index called");
        $all = $this->request->all();
        $user_id = $this->userService->user_id();
        if($this->userService->is_admin())
        {
            if(!empty(@$all['user_id']))
                $user_id = @$all['user_id'];
        }

        $info = UserInviteSettingsModel::where("user_id",$user_id)->with('user');
        
        if(!empty($all['request_type'])){
            $info=$info->where("request_type",$all['request_type'])->where("is_deleted",0);
        }
        $info=$info->get();
        
        return response()->json(['status'  => 'success',
            "data"    => $info,
            ], 200);
    }

    public function store()
    {
        Log::info("UserInviteSettingsController: store called");

        ## Validation rules formation
        $rules = [
            "request_type"=>[
                'required',
                Rule::in([1,2,3]),
            ],
            'sale_type'              => [
                'array',
                'min:1"',
            ],
            'sale_type.*'              => [
                'required',
                Rule::in(array_keys(config('property_information.sale_type'))),
            ],

            'state'              => [
                'array',
                'min:1"',
            ],
            'state.*'              => [
                'required',
                Rule::in(array_keys(config('constants.states'))),
            ],
            "county" => [
                'nullable',
                'array',

                'min:1"',
            ],
            "county.*" => [
                'max:20',
            ]
        ];

        $all = $this->request->all();
        Log::info("UserInviteSettingsController: store validation check");
        $validator = Validator::make($all, $rules);
        $validator->validate();

        // Delete old settings for this user.
        //        UserInviteSettingsModel::where([
        //            "user_id"=>$this->userService->user_id(),
        //            "request_type"=>$all['request_type'],
        //
        //        ])->delete();
        $insert_data = [];
        $sale_type = $all['sale_type'];
        $state = $all['state'];
        $county = @$all['county'];
        $request_type = $all['request_type'];

        ## ToDO: make a log entry for this
        $user_id = $this->userService->user_id();
        ## Admin can update record of other wholesale buyer.
        if($this->userService->is_admin())
        {
            if(!empty(@$all['user_id']))
            $user_id = @$all['user_id'];
        }

        foreach ($sale_type as $key=>$value)
        {
            $temp = [];
            $temp['user_id'] = $user_id;
            $temp['sale_type'] = $value;
            $temp['state'] = $state[$key];
            $temp['county'] = @$county[$key] ?$county[$key]:null;
            $temp['request_type'] = $request_type;
            $temp['created_at'] = time();
            $temp['updated_at'] = time();
            $insert_data[] =$temp;
        }

        UserInviteSettingsModel::insert($insert_data);

        $user_id = $this->userService->user_id();
        if($this->userService->is_admin())
        {
            if(!empty(@$all['user_id']))
                $user_id = @$all['user_id'];
        }
        $info = UserInviteSettingsModel::where("user_id",$user_id)->with('user');
        if(!empty($request_type) && $request_type==3){
            $info=$info->where("request_type",$all['request_type'])->where("is_deleted",0);
        }
        $info =$info->get();
        return response()->json(['status'  => 'success',
            "data"    => $info,
            'message' => __("messages.invite_settings_saved")], 200);

    }

    public function destroy($id)
    {
        Log::info("CommonNotesController: noteDelete called");
        try {
            $info = UserInviteSettingsModel::find($id);
            if ($info == true) {
                // check user id of notes
                if ($this->userService->user_id() != 501 &&  $info->user_id != $this->userService->user_id()) {
                    return response()->json(['message' => __("messages.not_delete")], 200);
                }
                $info->delete();
                return response()->json(['message' => __("messages.invite_settings_delete")
                ], 200);
            } else {
                return response()->json(['message' => __("error_messages.record_not_exists")], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }
}
