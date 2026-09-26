<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CorsMiddleware
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

        if(strtolower(app()->env) == "production")
        {
            $response = $next($request);
            return $response;
        }
        else{

            $headers = [
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age'           => '86400',
                'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
            ];

            if ($request->isMethod('OPTIONS'))
            {
                return response()->json('{"method":"OPTIONS"}', 200, $headers);
            }

            $response = $next($request);
            foreach($headers as $key => $value)
            {
                $response->headers->set($key, $value);
            }

//            $response->headers->set('Access-Control-Allow-Origin', '*');
//            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
//            $response->headers->set('Access-Control-Allow-Credentials', 'true');
//            $response->headers->set('Access-Control-Max-Age', '86400');
//            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');


            Log::info(get_class($this).": handle end");
            return $response;
        }

    }
}