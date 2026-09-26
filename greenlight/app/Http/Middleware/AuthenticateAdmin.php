<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\LoginController;
use Illuminate\Support\Facades\Session;


class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the authenticated user from the session, this information is stored in seession inside authenticateAdmin() in Admin/LoginController
        $user = Session::get('admin_info');

        // Check if the user is authenticated
        if ($user) {
            // Set the authenticated user in the request
            Auth::setUser($user);
        } else {
            // If user not found, redirect to login or another route
            return redirect()->route('admin.login');
        }

        // Continue processing the request
        return $next($request);

    }
}