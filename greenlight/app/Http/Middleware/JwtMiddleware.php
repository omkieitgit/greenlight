<?php

namespace App\Http\Middleware;
use App\Helpers\CommonHelper;
use App\Models\CommonModel;
use App\Services\ApiLogService;
use App\Services\UserService;
use Closure;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Log;
use Firebase\JWT\Key;

class JwtMiddleware
{
    protected  $userService;
    public function __construct(UserService $userService)
    {
        Log::info(get_class($this).": __construct called");

        $this->userService = $userService;
    }


    public function handle($request, Closure $next, $guard = null)
    {
        Log::info(get_class($this).": handle called");
        try {
            Log::info(get_class($this).": try ");

            $token = $request->header('Authorization');
            Log::info(get_class($this).": Authorization ");
            if(empty($token))
            {
                Log::info(get_class($this).": empty token ");
                $token = $request->get('token');
            }

            Log::info(get_class($this).": !token if  ");

            if(!$token) {
                Log::info(get_class($this).": Unauthorized response if token not there");

                // Unauthorized response if token not there
                return response()->json([
                    'message' => 'Token not provided.'
                ], 400);
            }
            Log::info(get_class($this).": JWT::decode");
            //$credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            
        } catch(ExpiredException $e) {
            Log::info(get_class($this).": ExpiredException ");
           
            return response()->json([
                'message' => 'Provided token is expired.'
            ], 440);
        }

        catch(Exception $e) {
            Log::info(get_class($this).": Exception ");
            Log::info($e);
            return response()->json([
                'message' => 'Your session has been expired, Please login again.'
            ], 401);
        }

        Log::info(get_class($this).": findJwtOneById ");
        $user = $this->userService->findJwtOneById($credentials->sub);

        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;


        // Update API authorize user_id
        $apiLogId = $request['api_log_id'];
        $data['authorized'] = $credentials->sub;
        $data['api_key'] = $token;
        $aplLogObj = new ApiLogService();
        //$aplLogObj->updateApiLog($data,$apiLogId);

        // To access user id in model too
        # To Do: later change it
        //CommonHelper::setCommonKey('user_id',$user->user_id);

        Log::info(get_class($this).": handle end");

        return $next($request);
    }
}
