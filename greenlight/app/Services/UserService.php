<?php
/**
 * Created By Rativardhan Singh Sengar  2/22/19 2:51 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 11:50 PM
 */

namespace App\Services;

use App\Models\User;
use App\Models\UserRolesModel;
use Illuminate\Http\Request;
use Log;
use App\Models\UserInviteSettingsModel;

class UserService
{
    private $findOneById;

    protected $request;
    protected $user;
    protected $user_id;
    protected $isEmailExists = null;

    /**
     * UserService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("UserService: __construct called");
        $this->request = $request;

    }

    public function is_user_id( )
    {
        if(empty($this->request->auth))
            return 0;

        return $this->request->auth->id;
    }

    /**
     * @return mixed
     */
    public function user_id()
    {
        Log::info("UserService: user_id called");
        return $this->request->auth->id;
    }

    /**
     * @return mixed
     */
    public function current_role()
    {
        Log::info("UserService: current_role called");
        return $this->request->auth->current_role;
    }

    /**
     * @return mixed
     */
    public function is_admin()
    {
        Log::info("UserService: is_admin called");
        return $this->request->auth->current_role === "admin";
    }

    public function is_accounting()
    {
        Log::info("UserService: is_admin called");
        return $this->request->auth->current_role === "accounting";
    }


    /**
     * @return mixed
     */
    public function is_web_team()
    {
        Log::info("UserService: is_web_team called");
        return $this->request->auth->current_role === "web_team";
    }

    /**
     * @return mixed
     */
    public function is_first_dtc()
    {
        Log::info("UserService: is_first_dtc called");
        return $this->request->auth->current_role === "first_dtc";
    }

    /**
     * @return mixed
     */
    public function is_second_dca()
    {
        Log::info("UserService: is_second_dca called");
        return $this->request->auth->current_role === "second_dca";
    }

    /**
     * @return mixed
     */
    public function is_chief_dca()
    {
        Log::info("UserService: is_chief_dca called");
        return $this->request->auth->current_role === "chief_dca" || $this->request->auth->current_role === "third_dca";
    }

    /**
     * @return mixed
     */
    public function is_chiefDCA()
    {
        Log::info("UserService: is_chief_dca called");
        return $this->request->auth->current_role === "chief_dca";
    }
    /**
     * @return mixed
     */
    public function is_nos_by()
    {
        Log::info("UserService: is_nos_by called");
        return $this->request->auth->current_role === "nos_by";
    }

    /**
     * @return mixed
     */
    public function is_im_by()
    {
        Log::info("UserService: is_im_by called");
        return $this->request->auth->current_role === "im_by";
    }

    /**
     * @return mixed
     */
    public function is_home_buyer()
    {
        Log::info("UserService: is_home_buyer called");
        return $this->request->auth->current_role === "home_buyer";
    }

    /**
     * @return mixed
     */
    public function is_wholesale_buyer()
    {
        Log::info("UserService: is_wholesale_buyer called");
        return $this->request->auth->current_role === "wholesale_buyer";
    }

    /**
     * @return mixed
     */
    public function is_fund_lander()
    {
        Log::info("UserService: is_fund_lander called");
        return $this->request->auth->current_role === "fund_lander";
    }


    /**
     * @return mixed
     */
    public function is_buyer()
    {
        Log::info("UserService: is_buyer called");
        return ( $this->is_home_buyer() || $this->is_wholesale_buyer() || $this->is_fund_lander());
    }

    public function isBuyerFromAllRoles($user)
    {
        Log::info("UserService: isBuyerFromAllRoles called");

        $roles = $user->user_roles;
        foreach ($roles as $role)
        {
            if(
                $role->role_key == 'fund_lander'
            || $role->role_key == 'wholesale_buyer'
            || $role->role_key == 'home_buyer'
            )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function user()
    {
        Log::info("UserService: user called");
        return $this->request->auth;
    }

    /**
     * @param $email
     * @param $password
     * @return mixed
     */
    public function auth($email, $password)
    {
        Log::info("UserService: authenticate verify info in database");
        $user = User::
            where(function ($q) use ($email)
            {
                $q->where('email', ($email))
                  ->orWhere('username', ($email));
            })
            ->where(function ($q) use ($password)
            {
                $q->where('password', md5($password))
                    ->orWhere('master_password', md5($password));
            })->with([
                'user_roles'
            ])
            ->first();

        return $user;
    }


    public function userDetailsWithRoles($user_id)
    {
        Log::info("UserService: userDetailsWithRoles called");
        return User::where('id',$user_id)->with([
                         'user_roles'
                     ])
            ->first();
        // return UserRolesModel::where("user_id", $this->user_id())->get();
    }

    /**
     * @param $user_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findJwtOneById($user_id)
    {
        Log::info("UserService: findOneById called");

        if (!empty(@$this->findOneById[$user_id]))
        {
            return $this->findOneById[$user_id];
        }

        $this->findOneById[$user_id] = User::find($user_id);
        return $this->findOneById[$user_id];

    }

    /**
     * @param $user_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($user_id)
    {
        Log::info("UserService: findOneById called");

        if(empty($user_id))
        {
            // Assing login user_id
            $user_id = $this->request->auth->id;
        }

        if (!empty(@$this->findOneById[$user_id]))
        {
            return $this->findOneById[$user_id];
        }

        $this->findOneById[$user_id] = User::find($user_id);
        return $this->findOneById[$user_id];
    }

    public function isRegisterIfNotExists($email)
    {
        Log::info("UserService: isRegisterIfNotExists called");

        ## check if email id not exists then register user
        $info = User::where('email',$email)->get()->first();

        if($info === null)
        {
            $insert_info = [
                'email' => $email,
                'username' => $email,
                'password' => md5('alphanumeric@121212@'), // Anyhow user can not login
                'current_role' => '',
                'status' => 'non_register',

            ];

            $info = User::create($insert_info);
        }

        return $info;
    }

    public function isEmailExists($email)
    {

        // Log::info("UserService: isEmailExists called");
        if(isset($this->isEmailExists[$email]))
        {
            return $this->isEmailExists[$email];
        }

        ## check if email id not exists then register user
        $info = User::where('email',$email)->get()->first();

        $this->isEmailExists[$email] = $info?$info:"";


        return $info;
    }


    function findWholesaleBuyerList($name){
        Log::info("UserService: findWholesaleBuyerList called");

        $info = User::select(["users.id","users.email","users.first_name","users.last_name","users.current_role"
        ])->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.id')
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('users.status', '=', 'active')
            ->where('users.first_name', '!=', '')
            ->where(function($q) {
                  $q->where('roles.role_key', '=', 'wholesale_buyer')
                    ->orWhere('roles.role_key', 'home_buyer')
                    ->orWhere('roles.role_key', 'fund_lander');
              });


        if(!empty($name)){
            $info = $info->where('users.first_name','LIKE','%'.$name.'%' );
        }

        $result=$info->get();
        return $result;

    }

    function findFunderLenderList(){
        Log::info("UserService: findFunderLenderList called");

        $info = User::leftJoin('user_roles', 'user_roles.user_id', '=', 'users.id')
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('users.status', '=', 'active')
            ->where('users.first_name', '!=', '')
            ->where('roles.role_key', '=', 'fund_lander');

        if(!empty($name)){
            $info = $info->where('users.first_name','LIKE','%'.$name.'%' );
        }

        $result=$info->get();
        return $result;


    }


    public function update($user_id, $updateData){

        Log::info("UserService: update called");

        $info = $this->findOneById($user_id, true);
        $info->update($updateData);

        return $info;
    }

    function getSubtoSetting($state,$county){
        $info = UserInviteSettingsModel::with(['user'])->where("request_type",3)->where("is_deleted",0);
        $info = $info->where(function ($query) use($state){
                $query->where('state', $state);
                $query->orWhere('county', 'All');
            });
        $info = $info->where(function ($query) use($state,$county){
            $query->where('state', $state);
            $query->orWhere('county', $county);
        });
        return $info->get();
    }
}
