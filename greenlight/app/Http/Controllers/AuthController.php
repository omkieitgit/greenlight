<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\LoginHistoryModel;
use App\Services\UserService;
use DB;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use CommonHelper;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request
        , UserService $userService
    )
    {
        Log::info("AuthController: __construct called");
        $this->request     = $request;
        $this->userService = $userService;
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function authenticate(User $user)
    {

        // TO DO: implement common exception handle for all controller
        Log::info("AuthController: authenticate called");

        ## Input variables
        $email    = $this->request->input('email');
        $password = $this->request->input('password');

        ## Validation rules formation
        $rules   = [
            'email'    => 'required',
            'password' => 'required'
        ];
        $message = [
            'email.required'    => __("error_messages.empty_email"),
            'password.required' => __("error_messages.empty_password")
        ];

        ## check input validation
        Log::info("AuthController: authenticate validation check");
        $validator = Validator::make($this->request->all(), $rules, $message);
        $validator->validate();

        ## check Email and Password exists in system or not
        Log::info("AuthController: authenticate verify info in database");
        $user = $this->userService->auth($email, $password);

        if (!$user)
        {
            Log::info("authController: authenticate verify database failed");
            // ToDo: later change into 401, frontend not able to handle now.
            return back()->withErrors(['error' => __('error_messages.invalid_cred')]);

        }
        else
        {
            // Check if user is pending or blocked
            if ($user->status == "pending")
            {
                if($user->is_payed == 0  && $this->userService->isBuyerFromAllRoles($user))
                {
                    // return response()->json(
                    //     ['message' => __('error_messages.account_pending'),
                    //         "data" => $user
                    //     ], 402);
                    return back()->withErrors([ 'error' => __('error_messages.account_pending')]);

                }
                return back()->withErrors([ 'error' => __('error_messages.account_pending')]);

                // return response()->json(
                //     ['message' => __('error_messages.account_pending'),
                //     ], 400);
            }
            else if ($user->status == "blocked")
            {
                return back()->withErrors([ 'error' => __('error_messages.account_blocked')]);

                //return response()->json(['message' => __('error_messages.account_blocked')], 400);
            }

            // LoginHistoryModel::insert(
            //     ['user_id'      => $user->id
            //      , 'ip_address' => $this->request->ip()
            //      , 'browser'    => $this->request->userAgent()
            //      , 'login_time' => time()
            //     ]
            // );

            #Update user last login
            $user->last_login = time();
            $user->save();
           // \Session::put('token',$this->jwt($user));
           // \Session::put('data',$user);
            \Cookie::queue('token',$this->jwt($user), 120);
            
            // if ($this->request->userdata('referal_url') != '')
            // {
            //     $referal_url = $this->session->userdata('referal_url');
            //     $this->session->set_userdata('referal_url', '');
            //     redirect($referal_url);
            // }
            $co_ref_url = @$_COOKIE['referal_url'];
            if(!empty($co_ref_url)){
                $referal_url =$co_ref_url;
            }else{
                $referal_url = \Session::get('referer_url')??'/home';
            }
            return redirect()->intended($referal_url);
            # Get User role information
            //return response()->json(['token' => $this->jwt($user), "data" => $user], 200)->header("token", $this->jwt($user));
        }

    }


    


    /**
     * Create a new token.
     *
     * @param  \App\Models\User $user
     * @return string
     */
    protected function jwt(User $user)
    {
        Log::info("AuthController: jwt called");

        $payload = [
            'iss' => env('APP_URL'), // Issuer of the token
            'aud' => env('APP_URL'), // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'id'  => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 9 * 60 * 60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'), "HS256");
    }

    function getToken(Request $request){

        // $token=\Crypt::decrypt(\Cookie::get('token'), false);
        // $user=\Crypt::decrypt(\Cookie::get('data'), false);
        // return response()->json(['token' =>$token, "data" => $user], 200)->header("token",$token);

        Log::info(get_class($this).": handle called");
        try {
            Log::info(get_class($this).": try ");
            $token="";
            if(!empty(\Cookie::get('token'))){
                $token=\Crypt::decrypt(\Cookie::get('token'), false);
                if(!empty($token)){
                    $token= explode('|',$token)[1];
                }
                Log::info(get_class($this).": Authorization ");
                if(empty($token))
                {
                    Log::info(get_class($this).": empty token ");
                    $token = $request->get('token');
                }
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
        $user = $this->userService->userDetailsWithRoles($credentials->sub);
        return response()->json(['token' =>$token, "data" => $user,'property_config'=>config('property_information')], 200)->header("token",$token);
    }

    function register(){
        $data["roles_es"] = config('constants.roles_es');
        $data["states"]  = config('constants.states');
        return view('consumer.auth.register',['data'=>$data]);
    }

    function logout(){
        \Session::flush();
        \Cookie::forget('token');
        \Cookie::queue(\Cookie::forget('token',null,'/'));
        \Auth::logout();
    }
}
