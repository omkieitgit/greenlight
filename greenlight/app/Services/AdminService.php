<?php
/**
 * Created By Rativardhan Singh Sengar  2/22/19 2:51 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 11:50 PM
 */

namespace App\Services;

use App\Models\User;
use App\Models\UserRolesModel;
use App\Models\RolesModel;
use Illuminate\Http\Request;
use Log;
use App\Models\UserInviteSettingsModel;

class AdminService
{

    private $findOneById;

    protected $request;
    protected $user;
    protected $user_id;
  
    //protected $isEmailExists = null;

    /**
     * AdminService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("AdminService: __construct called");
        $this->request = $request;
        

    }

 


    public function authAdmin($email, $password)
     {
         Log::info("AdminService: authenticate verify info in database");
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

    

     public function getAllUserDetail()
     {
         // Retrieve all users and their fields from the users table
         // return User::all();

         $users = User::with(['roles' => function ($query) {
            $query->select('role_string'); // Select only the role_string column from the roles table
        }])->get();
       

        return $users;
     }

             /**
     * @param $user_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getUserByID($user_id)
    {
        Log::info("AdminService: getUserByID called");
        $user = User::with(['roles' => function ($query) {
            $query->select('role_id'); // Select only the role_string column from the roles table
        }])
        ->where('id', $user_id) // Filter users by ID
        //->get();
        ->first();

         return $user;

         
    }

    public function updateUserRoles($userId ,$newSetOfRoles) 
    {
        Log::info("AdminService: updateUserRoles called");

        // Delete existing records for the given user ID
        UserRolesModel::where('user_id', $userId)->delete();

        if (!empty($newSetOfRoles)) {
            Log::info("newSetOfRoles is not empty");

           // UserRolesModel::where('user_id', $userId)->delete();
            // Insert new records for each role ID in the newValue array
            $insertData = [];
            foreach ($newSetOfRoles as $roleId) {
                // Generate a unique ID for each new entry
               // $uniqueId = uniqid();
            
                $insertData[] = [
                   // 'id' => $uniqueId,
                    'user_id' => $userId,
                    'role_id' => $roleId
                ];
            }
            
            try {
                UserRolesModel::insert($insertData);
            } 
            catch (\Exception $e) {
                // Handle the exception (e.g., log the error, return a response, etc.)
                // Example:
                \Log::error('Error inserting user roles: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to insert user roles'], 500);
            }
            
        } else {

            Log::info("newSetOfRoles is empty");
            
        }

        
    }


    public function updateUserStatus($userIdForStatus ,$newStatus) 
    {
        
        Log::info("AdminService: updateUserStatus called");
        Log::info('The value of the variables in updateUserStatus are: ' . $userIdForStatus . ',' . $newStatus );
        
        $enumValues = ['active', 'pending', 'blocked', 'non_register'];
       

        User::where('id', $userIdForStatus)->update(['status' => $enumValues[$newStatus]]);
              
    }


     public function getUserStatistics()
     {
         $statistics = [];
 
         // Total number of users
         $statistics['total_users'] = User::count();
         
         $statistics['total_active_users']          = User::where('status', 'active')->count();
         $statistics['total_pending_users']         = User::where('status', 'pending')->count();
         $statistics['total_blocked_users']         = User::where('status', 'blocked')->count();
         $statistics['total_admin_users']           = User::where('current_role', 'admin')->count();
         $statistics['total_web_team_users']        = User::where('current_role', 'web_team')->count();
         $statistics['total_wholesale_buyer_users'] = User::where('current_role', 'wholesale_buyer')->count();
         $statistics['total_chief_dca_users']       = User::where('current_role', 'chief_dca')->count();
         $statistics['total_am_users']              = User::where('current_role', 'am')->count();
         $statistics['total_first_dtc_users']       = User::where('current_role', 'first_dtc')->count();
         $statistics['total_second_dca_users']      = User::where('current_role', 'second_dca')->count();
         $statistics['total_third_dca_users']       = User::where('current_role', 'third_dca')->count();
         $statistics['total_im_by_users']           = User::where('current_role', 'im_by')->count();
         $statistics['total_trustee_caller_users']  = User::where('current_role', 'trustee_caller')->count();
         $statistics['total_accounting_users']      = User::where('current_role', 'accounting')->count();
         $statistics['total_home_buyer_users']      = User::where('current_role', 'home_buyer')->count();
         $statistics['total_wholesale_buyer_users'] = User::where('current_role', 'wholesale_buyer')->count();
         $statistics['total_fund_lander_users']     = User::where('current_role', 'fund_lander')->count();
         $statistics['total_auction_by_users']      = User::where('current_role', 'auction_by')->count();
         $statistics['total_im_checked_by_users']   = User::where('current_role', 'im_checked_by')->count();
         $statistics['total_sub_to_users']          = User::where('current_role', 'sub_to')->count();
         $statistics['total_acquisition_manager_users']          = User::where('current_role', 'acquisition_manager')->count();
         $statistics['total_nos_by_users']          = User::where('current_role', 'nos_by')->count();
         // Add more statistics as needed...
 
         return $statistics;
     }



}