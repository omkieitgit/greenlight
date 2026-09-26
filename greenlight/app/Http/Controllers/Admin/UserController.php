<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AdminService;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{

    protected $adminService;
    public function __construct(AdminService $adminService)
{
    $this->adminService = $adminService;
}


    public function showAllUsers(AdminService $adminService)
    {
        // Logic to list users

     
            // Retrieve all user fields using the AdminService
                   // Logic to show the dashboard
        $adminUser = Session::get('admin_info');
        View::share('adminUser', $adminUser);

            $users = $adminService->getAllUserDetail();
            
    
            // Return or do something with the $users collection
            return view('admin.users.index', ['users' => $users]);
     }

    
    public function showUser(AdminService $adminService,$id)
    {
        $adminUser = Session::get('admin_info');
        View::share('adminUser', $adminUser);
        
        // Logic to show a specific user
        $specificUser = $adminService->getUserByID($id);   
        
        return view('admin.users.show', compact('specificUser'));
    }

    public function updateUserRoleDatabase(AdminService $adminService, Request $request)
    {
        
        // Retrieve the new value from the request
        
        $newSetOfRoles = $request->input('newValue');
        // You can also retrieve other necessary data from the request
        $userId = $request->input('userId');  
        $adminService->updateUserRoles($userId ,$newSetOfRoles) ;


    }

    public function updateUserStatusDatabase(AdminService $adminService, Request $request)
    {
        
        // Retrieve the new value from the request
        
        $newValueOfStatus = $request->input('newValue');
        // You can also retrieve other necessary data from the request
        $userIdForStatus = $request->input('userIdForStatus');  
        $adminService->updateUserStatus($userIdForStatus ,$newValueOfStatus) ;


    }


    // public function deleteRoleFromUser(Request $request, $userId, $roleId)
    // {
    //     // Call the method from AdminService to handle role deletion
    //     $result = $this->adminService->deleteRoleFromUser($userId, $roleId);

    //     // Handle the result and return appropriate response
    //     if ($result) {
    //         return response()->json(['message' => 'Role deleted successfully'], 200);
    //     } else {
    //         return response()->json(['error' => 'Failed to delete role'], 500);
    //     }
    // }

    // public function addRoleToUser(Request $request, $userId)
    // {
    //     $roleId = $request->input('role_id');

    //     // Call the method from AdminService to add role to user
    //     $result = $this->adminService->addRoleToUser($userId, $roleId);

    //     // Handle the result and return appropriate response
    //     if ($result) {
    //         return response()->json(['message' => 'Role added successfully'], 200);
    //     } else {
    //         return response()->json(['error' => 'Failed to add role (role already exists for user)'], 500);
    //     }
    // }


    
}
