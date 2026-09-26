<?php

namespace App\Http\Middleware;
use App\Services\ApiLogService;
use Closure;
use Illuminate\Support\Facades\Log;

class AfterMiddleware
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

        $response = $next($request);

        $data['response_code'] = $response->getStatusCode();
        $apiLogId = $request['api_log_id'];
        $data['rtime'] = $this->microtime_float() - $request['api_exec_start'];
        $aplLogObj = new ApiLogService();
        //$aplLogObj->updateApiLog($data,$apiLogId);
        #$response->throwResponse();
        #$response->withException(Exception $e);

        return $response;
    }

    function microtime_float()
    {
        Log::info(get_class($this).": microtime_float called");


        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function terminate($request, $response)
    {
        // Store the session data...
    }
}
