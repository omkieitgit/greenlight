<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use App\Services\AdminService;

class DashboardController extends Controller
{
   
    protected $adminService;

    public function __construct(AdminService $adminService)
{
    $this->adminService = $adminService;
}
   
    public function index()
    {
        // Logic to show the dashboard
        $userStatistics = $this->adminService->getUserStatistics();


        $adminUser = Session::get('admin_info');
        View::share('adminUser', $adminUser);
        return view('admin.dashboard',['userStatistics' => $userStatistics]);
    }
}


