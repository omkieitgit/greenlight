<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRolesModel;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
Use App\Services\UserService;
Use Log;
use App\Helpers\CommonHelper;
use Validator;
use DB;
class UserController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $userService;
    private $request;

    public function __construct(UserService $userService, Request $request) {
        Log::info("UserController: __construct called");
        $this->userService = $userService;
        $this->request     = $request;

    }


    public function profile(Request $request) {

        $token = $request->input('token');

        //$decoded = JWT::decode($token, env('JWT_SECRET'), array("HS256"));
        //$user = User::find($request->auth->sub);

        // We already have info of user in token verification auth
        if (!$request->auth) {
            return response()->json(['message' => __('error_messages.something_wrong')], 400);
        }
        else {
            $request->auth->load('user_roles');
            return response()->json(["message" => "",
                                     'row'     => $request->auth], 200)->header("token", $token);
        }

    }

    public function changeRole() {
        Log::info("UserController: changeRole called");

        $role_id = $this->request->input('role_id');

        ## Validation rules formation
        $rules = [
            'role_id' => [
                "required",
            ],
        ];

        # if record exists update information
        ## check input validation
        Log::info("UserController: changeRole check validation");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        // Check is role available for user or not
        $role = UserRolesModel::where([
                                  'user_id' => $this->userService->user_id(),
                                  'role_id' => $role_id,
                              ])->with('role')->first();

        if(empty($role)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Invalid role for user."], 200);
        }

        $role_key = @$role->role->role_key;
        $info = [];
        if(!empty($role_key))
        {
            // update current role key in USER table

            // Update User Profile information.
            $update_data               = [];
            $update_data['current_role'] = $role_key;
            $info = $this->userService->update($this->userService->user_id(), $update_data);

        }

        $info->load('user_roles');
        $message=ucwords(str_replace('_',' ',$role_key));
        return response()->json(['status'  => 'success',
                                 "data"    => $info,
                                 'message' => __("messages.change_role",array("user_role" => $message))], 200);


    }


    public function update() {


        ## Validation rules formation
        $rules = ['first_name' => 'required|alpha',
                  'last_name'  => 'required|alpha',
                  'username'   => 'required|unique:users,username,' . $this->userService->user_id(),
                  'email'      => 'required|email|unique:users,email,' . $this->userService->user_id(),
                  //'city'       => 'required',
                  //'state'      => 'required|in:' . implode(",",array_keys(config('constants.states'))),
                  //'mobile'     => 'required'
                ];


        # if record exists update information
        ## check input validation
        Log::info("UserController: update validation check");

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        // Update User Profile information.
        $update_data               = [];
        $update_data['first_name'] = $this->request->input('first_name');
        $update_data['last_name']  = $this->request->input('last_name');
        $update_data['username']   = $this->request->input('username');
        $update_data['email']      = $this->request->input('email');
        $update_data['address']    = $this->request->input('address');
        $update_data['city']       = $this->request->input('city');
        $update_data['state']      = $this->request->input('state');
        $update_data['mobile']     = $this->request->input('mobile');
        $info                      = $this->userService->update($this->userService->user_id(), $update_data);

        # ToDo: Send change username email

        # ToDO: Send change email to both address.
        # ToDo: make user log for record change entries like user name, email.

        return response()->json(['status'  => 'success',
                                 "data"    => ['user' => $info],
                                 'message' => __("messages.record_saved")], 200);

    }


    public function acceptAgree() {
        ## check input validation
        Log::info("UserController: update validation check");

        // Update User Profile information.
        $update_data               = [];

        if(!empty($this->request->input('is_agree')))
        {
            // SEND PDF with email.
            $update_data['is_agree'] = $this->request->input('is_agree');
        }

        if(!empty($this->request->input('is_popup')))
        {
            // SEND PDF with email
            $update_data['is_popup'] = $this->request->input('is_popup');
        }

        $info = $this->userService->update($this->userService->user_id(), $update_data);

        return response()->json(['status'  => 'success',
                                 "data"    => ['user' => $info],
                                 'message' => __("messages.record_saved")], 200);

    }

    public function changePassword() {
        Log::info("UserController: changePassword check");


        ## Validation rules formation
        $rules = [
            'password'   => 'required|min:6',
            'new_password'   => 'required|min:6|different:password',
        ];

        # if record exists update information
        ## check input validation
        Log::info("UserController: update validation check");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        $password = $this->request->input('password');
        if(md5($password) != $this->userService->user()['password']){
            return response()->json(['status'  => 'failed',
                                     'message' => __("error_messages.password_invalid")], 200);
        }

        // Update User Profile information.
        $update_data               = [];
        $update_data['password']   = md5($this->request->input('new_password'));
        $info                      = $this->userService->update($this->userService->user_id(), $update_data);


        return response()->json(['status'  => 'success',
                                 "data"    => ['user' => $info],
                                 'message' => __("messages.password_change")], 200);

    }

    public function wholesaleBuyer(Request $request) {


        $name = $request->input('name');

        $result = $this->userService->findWholesaleBuyerList($name);
        if (empty($result)) {
            return response()->json(['data'    => [],
                                     'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $result], 200);
    }

    public function lender() {

        $result = $this->userService->findFunderLenderList();
        if (empty($result)) {
            return response()->json(['data'    => [],
                                     'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $result], 200);
    }

    public function autopopulateuser() {

        $role = $this->request->input('role');
        $keywords = $this->request->input('keywords');

        $info = User::select([
                DB::raw("CONCAT(first_name,' ',last_name)  AS name")
                ,'users.id'])->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.id')
                    ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
                    ;

        if(!empty($keywords)){
            $info->where(function ($q) use ($keywords) {
                $q->where('users.first_name','LIKE','%'.$keywords.'%' );
                $q->orWhere('users.last_name','LIKE','%'.$keywords.'%' );
                $q->orWhere(DB::raw("CONCAT(`first_name`, '', `last_name`)"),'LIKE','%'.$keywords.'%' );
            });

        }

        if(!empty($role)){
            $info->where('roles.role_key','=', $role );
        }

        $info->where('users.status', '!=', 'blocked');
        $result = $info->groupBy('users.id')->limit(20)->get();

        return response()->json($result, 200);
    }


    public function profileUserList() {

        $keywords = $this->request->input('keywords');

        $info = User::select([
            DB::raw("CONCAT(first_name,' ',last_name)  AS name")
            ,'users.id'])->addSelect(['work_profile_team'])->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.id')
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
        ;

        if(!empty($keywords)){
            $info->where(function ($q) use ($keywords) {
                $q->where('users.first_name','LIKE','%'.$keywords.'%' );
                $q->orWhere('users.last_name','LIKE','%'.$keywords.'%' );
                $q->orWhere(DB::raw("CONCAT(`first_name`, '', `last_name`)"),'LIKE','%'.$keywords.'%' );
            });

        }

        $info->where(function ($q) {
            $q->where('roles.role_key','=', 'first_dtc' );
            $q->orWhere('roles.role_key','=', 'second_dca' );
            $q->orWhere('roles.role_key','=', 'third_dca' );
            $q->orWhere('roles.role_key','=', 'nos_by' );
            $q->orWhere('roles.role_key','=', 'im_by' );
            $q->orWhere('roles.role_key','=', 'trustee_callers' );
            $q->orWhere('roles.role_key','=', 'chief_dca' );
        });

        $info->where('users.status', '!=', 'blocked');
        $result = $info->groupBy('users.id')->limit(100)->get();

        return response()->json($result, 200);
    }

    public function getWorkProfileList()
    {
        ## check input validation
        Log::info("getWorkProfileList: called");
        $data = ['pay_rates'=>config('pay_rates')];
        $data['text'][] = 'For all DTC/DCA';
        $data['text'][] = '49 and below = No internet will be paid';
        $data['text'][] = '50 to 100 = $5';
        $data['text'][] = '101 to 199 = $10';
        $data['text'][] = '200 to 249 = $15';
        $data['text'][] = '250 up = $20';
        $data['text'][] = '';
        $data['text'][] = 'NOS:';
        $data['text'][] = '499 below, none';
        $data['text'][] = '500 to 899, $10';
        $data['text'][] = '900 and above, $20';
        $data['text'][] = 'But those who are on the management, gets $20 internet fee automatically';
        return $data;
    }

    public function updateWorkProfileTeam($uid)
    {
        if(!($this->userService->is_admin()) ){
            return response()->json(['status' => 'failed','message'=>__("messages.not_authorize_section"),'data' => []], 200);
        }

        ## Validation rules formation
        $all = $this->request->all();
        $all['uid'] = $uid;
        $rules = [
            'work_profile_team'      => 'required|in:' . implode(",",array_keys(config('pay_rates'))),
            'uid'     => 'required|numeric|exists:users,id'
        ];

        ## check input validation
        Log::info("updateWorkProfileTeam: update validation check");
        $validator = Validator::make($all, $rules);
        $validator->validate();

        // Update User Profile information.
        $update_data               = [];
        $update_data['work_profile_team'] = $this->request->input('work_profile_team');
        $info                      = $this->userService->update($uid, $update_data);
        $info = $info->only(['id','first_name', 'last_name','work_profile_team']);

        $info['name'] = @$info['first_name'].' '.@$info['last_name'];

        return response()->json(['status'  => 'success',
            "data"    => $info,
            'message' => __("messages.record_saved")], 200);
    }
}
