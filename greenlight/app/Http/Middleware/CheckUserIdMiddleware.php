<?php

namespace App\Http\Middleware;
use App\Models\CommonModel;
use App\Services\CommonService;
use App\Services\UserService;
use App\Models\User;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckUserIdMiddleware
{
    protected  $userService;
    public function __construct(UserService $userService)
    {
        Log::info(get_class($this).": __construct called");

        $this->userService = $userService;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info(get_class($this).": handle called");


        $user = $this->userService->user();
        
        $userObj = new CommonService("lock_user");
        $user_id = $userObj->getInfo(array("user_id" => $user->email));

        $date = date_create();
        $currunt_time = date_timestamp_get($date);
        #return $currunt_time;
        if ($user_id and $user_id->lock_user_release_time > $currunt_time) {

            
            return response()->json([
                'message' => 'Your ID is blocked'
            ], 401);
        }

        return $next($request);
    }
}



/*<!DOCTYPE HTML>
<html>
<head>
<title>Oops Error </title>

<style type="text/css">
body{
    font-family:  cursive;
}
body{
    background:#eaeaea;
}   
.wrap{
    margin:0 auto;
    width:1000px;
}
.logo{
    text-align:center;
    margin-top:200px;
}
.logo img{
    width:350px;
}
.logo p{
    color:#272727;
    font-size:40px;
    margin-top:1px;
}   
.logo p span{
    color:lightgreen;
}   
.sub a{
    color:#fff;
    background:#272727;
    text-decoration:none;
    padding:10px 20px;
    font-size:13px;
    font-family: arial, serif;
    font-weight:bold;
    -webkit-border-radius:.5em;
    -moz-border-radius:.5em;
    -border-radius:.5em;
}   
.footer{
    color:black;
    position:absolute;
    right:10px;
    bottom:10px;
}   
.footer a{
    color:rgb(114, 173, 38);
}   
</style>
</head>


<body>

<!---728x90--->

 <div class="wrap">
    <div class="logo">
            <p>OOPS! - Your IP is Blocked</p>
            
            <p>Please try after some time</p>           
<!---728x90--->

            
    </div>
 </div> 
    
<!---728x90--->

    
    <div class="footer">
     
    </div>
    
</body>*/