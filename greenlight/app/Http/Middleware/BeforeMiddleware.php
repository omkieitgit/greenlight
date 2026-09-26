<?php

namespace App\Http\Middleware;
use App\Services\ApiLogService;

use Closure;
use Illuminate\Support\Facades\Log;

class BeforeMiddleware
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
        Log::info("URI: ".$request->url());
        $jsonInfo   = $request->all();

        if(!empty($jsonInfo['password']))
        {
            $jsonInfo['password'] = str_repeat('*',strlen($jsonInfo['password']));
        }
        if(!empty($jsonInfo['new_password']))
        {
            $jsonInfo['new_password'] = str_repeat('*',strlen($jsonInfo['new_password']));
        }
        if(!empty($jsonInfo['master_password']))
        {
            $jsonInfo['master_password'] = str_repeat('*',strlen($jsonInfo['master_password']));
        }

        $methodParam = json_encode($jsonInfo);
        $data['ip_address'] = $request->ip();
        $data['uri'] = $request->url();
        $data['method'] = $request->method();
        $data['authorized'] = 0;

        $data['params'] = $methodParam;
        $aplLogObj = new ApiLogService();

      //  $request['api_log_id'] = $aplLogObj->saveApiLog($data);
        $request['api_exec_start'] = $this->microtime_float();
        return  $next($request);


        #print_r($request->query());
        #print_r($request->segments());        
        #echo $request->fullUrlWithQuery();        
        #print_r($request->all());        
        #$data['ip'] = $request->fullUrl();
        #$data['ip'] = $request->path();
        #$data['ip'] = $request->root();

    }

    function microtime_float()
    {
        Log::info(get_class($this).": microtime_float called");

        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}
