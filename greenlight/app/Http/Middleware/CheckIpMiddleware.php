<?php

namespace App\Http\Middleware;
use App\Models\CommonModel;
use App\Services\CommonService;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckIpMiddleware
{
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

        $userObj = new CommonService("lock_ip");
        $user_id = $userObj->getInfo(array("ip_address" => $request->ip()));

        $date = date_create();
        $currunt_time = date_timestamp_get($date);
        #return $currunt_time;
        if ($user_id and $user_id->lock_release_time > $currunt_time) {

            $token = $request->header('Authorization');
            $data['ip_address'] = $request->ip();
            $data['uri'] = $request->url();
            $data['method'] = $request->method();
            $data['user_id'] = $user_id;
            $data['token'] = $token;

            Log::emergency("Ip has been blocked",$data);

            return response()->json([
                'message' => 'Your IP has been blocked, Please contact system administrator willow@theestates.com'
            ], 400);
        }

        return $next($request);
    }
}
