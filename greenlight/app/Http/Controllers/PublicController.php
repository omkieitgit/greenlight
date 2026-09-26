<?php

namespace App\Http\Controllers;


use App\ContactUs;
use App\Exceptions\CustomException;
use App\Models\HouseBuyItModel;
use App\Models\HouseTokenModel;
use App\Models\PropertyModel;
use App\Models\User;
use App\Models\UserPaymentLog;
use App\Models\UserPlanModel;
use App\Models\UsersPlansModel;
use App\Services\HouseBuyItService;
use App\Services\HouseTokenService;
use App\Services\InviteService;
use App\Services\PropertyService;
use Dompdf\Exception;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use App\Helpers\CustomHelper;
use App\Services\CommonService;
use App\Services\MailService;
use Illuminate\Http\Request;

use DB;
class PublicController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $mailService;

    private $inviteService;
    private $propertyService;
    private $houseTokenService;
    private $houseBuyItService;

    public function __construct(Request $request,
    MailService $mailService,


    InviteService $inviteService,
    PropertyService $propertyService,
    HouseTokenService $houseTokenService
        , HouseBuyItService $houseBuyItService
    )
    {
        Log::info("AuthController: __construct called");
        $this->request = $request;
        $this->mailService = $mailService;


        $this->inviteService = $inviteService;
        $this->propertyService = $propertyService;
        $this->houseTokenService = $houseTokenService;
        $this->houseBuyItService = $houseBuyItService;

    }
    public function sendBuyIt()
    {
        die;
        Log::info("PublicController: sendBuyIt called");

        $key = $this->request->input('key');

        if($key != 'rati12')
        {
            return response()->json([
                'message' => 'wrong call!!'
            ], 400);
        }


        // fetch all list of june 1 st
        $infos = HouseBuyItModel::where('created_at','>',strtotime('june 1 2020'))
            ->where('created_at','<',strtotime('june 21 2020'))
            ->get();

        echo  'total records .'.$infos->count();

        foreach($infos as $key=>$value)
        {

            $house_id = @$value['house_id'];
            $position_info = $this->houseBuyItService->getBuyItNextPosition($house_id, $value['user_id']);
            $pos_message   = $position_info['pos_message'];
            $top_message   = $position_info['top_message'];
            # first check record exists or not
            $property_info = $this->propertyService->findOneById($house_id);


            $notes = @$value['notes'];

            $question                               = array();
            $question['did_you_buy']                = $did_you_buy = @$value['did_you_buy'];
            $question['did_you_picture']            = $did_you_picture = @$value['did_you_picture'];
            $question['please_submit']              = $please_submit = @$value['please_submit'];
            $question['do_money_finance']           = $do_money_finance = @$value['do_money_finance'];
            $question['buyit_repair_cost']          = $buyit_repair_cost = @$value['buyit_repair_cost'];
            $question['buyit_estimation_arv_value'] = $buyit_estimation_arv_value = @$value['buyit_estimation_arv_value'];
            $question['highest_offer_bid']          = $highest_offer_bid = @$value['highest_offer_bid'];
            $question['buyitnotes']                 = $notes;

            $token = $this->houseTokenService->token($house_id);
            $link                = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);

            $info                = ($question);
            $info['notes']       = $notes;
            $info['pos_message'] = $pos_message;
            $info['top_message'] = $top_message;

            $info['address_url'] = $link;
            $info['address']     = trim($info['address_url']);
            $info['house_id']    = $house_id;
            $info['user_id']    = @$value['user_id'];

            // send one email to requesting person
            $this->mailService->tempEmailsendBuyItEmailToUser($info);

        }

        // Return Role list from table


    }


    public function roles()
    {
        Log::info("PublicController: roles called");
        // Return Role list from table
        return response()->json([
            'row' => config('constants.roles_es')
        ], 200);

    }

    public function buyerConfigInfo()
    {
        Log::info("PublicController: buyerConfigInfo called");
        // Return Role list from table
        return response()->json([
            'row' => array(
                "role"               => config('constants.roles_buyer'),
                "plan"               => config('constants.booster_plan'),
                "first_county_price" => config('constants.first_county_price'),
                "per_county_price"   => config('constants.per_county_price')
            )
        ], 200);

    }

    public function memberRegisterConfigInfo()
    {
        Log::info("PublicController: memberRegisterConfigInfo called");
        // Return Role list from table
        return response()->json([
            'row' => array(
                "roles_es" => config('constants.roles_es'),
                "states"   => config('constants.states'),
            )
        ], 200);

    }

    public function register()
    {
        // TO DO: implement common exception handle for all controller
        Log::info("PublicController: register called");

        $roles = config('constants.roles_es');

        ## Validation rules formation
        $rules = [
            'first_name' => 'required|alpha',
            'last_name'  => 'required|alpha',
            'username'   => 'required|unique:users',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'address'    => 'required',
            'city'       => 'required',
            'state'      => 'required|in:' . implode( ",",array_keys(config('constants.states'))),
            'mobile'     => 'required|min:11|numeric',
            'role'       => 'required|in:' . implode( ",",array_keys($roles)),
        ];

        ## check input validation
        Log::info("PublicController: validation check");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        ## Input variables
        $insert                 = array();
        $insert['first_name']   = $this->request->input('first_name');
        $insert['last_name']    = $this->request->input('last_name');
        $insert['username']     = $this->request->input('username');
        $insert['email']        = $this->request->input('email');
        $insert['password']     = md5($this->request->input('password'));
        $insert['address']      = $this->request->input('address');
        $insert['city']         = $this->request->input('city');
        $insert['state']        = $this->request->input('state');
        $insert['mobile']       = $this->request->input('mobile');
        $insert['current_role'] = $this->request->input('role');
        $insert['status']       = "pending";
        $insert['is_popup']       = '1';
        $insert['created_at']       = time();
        $insert['updated_at']       = time();

        $userObj = new CommonService("users");
        $user_id = $userObj->saveInfo($insert);

        #ToDo: email verification is missing

        if (empty($user_id))
        {
            Log::error("PublicController: Something went wrong");
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
        else
        {

            Log::info("PublicController: User register successfully");

            // Get role id of role
            $roleObj = new CommonService("roles");
            $role    = $roleObj->getInfo(array("role_key" => $this->request->input('role')));

            if (!empty($role))
            {
                Log::info("PublicController: !empty role " . $this->request->input('role'));
                // Find role id from
                $insert            = array();
                $insert['role_id'] = $role->id;
                $insert['user_id'] = $user_id;

                $userRoleObj = new CommonService("user_roles");
                $userRoleObj->saveInfo($insert);
            }

            #Send Welcome Email for normal User.
            //$this->mailService->welcomeMail($user_id);
            return redirect()->back()->withSuccess(__("messages.buyer_register"));

            //return response()->json(['message' => __("messages.user_register")], 201);
        }
    }

    public function buyerRegister()
    {
        // TO DO: implement common exception handle for all controller
        Log::info("PublicController: buyerRegister called");

        $roles = config('constants.roles_buyer');

        ## Validation rules formation
        $rules = [
            'first_name'  => 'required|alpha',
            'last_name'   => 'required|alpha',
            'username'    => 'required|unique:users',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:6',
            //'address'     => 'required',
            //'city'        => 'required',
            'state'       => 'required|in:' . implode(",",array_keys(config('constants.states')) ),
            //'mobile'      => 'required|min:11|numeric',
            ## We are considering wholesale_buyer default 'role'          => 'required|in:'.implode(array_keys($roles),","),
            //'plan_region' => 'required_without:plan_county|in:' . implode( ",",array_keys(config('constants.booster_plan'))),
            //'plan_county' => 'required_without:plan_region', ## Foramt state::county|||state::county||
        ];

        ## check input validation
        Log::info("PublicController: buyerRegister validation check");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        ## Input variables
        $insert                 = array();
        $insert['first_name']   = $this->request->input('first_name');
        $insert['last_name']    = $this->request->input('last_name');
        $insert['username']     = $this->request->input('username');
        $insert['email']        = $this->request->input('email');
        $insert['password']     = md5($this->request->input('password'));
        //$insert['address']      = $this->request->input('address');
        //$insert['city']         = $this->request->input('city');
        $insert['state']        = $this->request->input('state');
        //$insert['mobile']       = $this->request->input('mobile');
        $insert['current_role'] = "wholesale_buyer";
        $insert['status']       = "pending";
        $insert['is_popup']       = '1';
        $insert['created_at']       = time();
        $insert['updated_at']       = time();

        $userObj = new CommonService("users");
        $user_id = $userObj->saveInfo($insert);

        #ToDo: email verification is missing
        if (empty($user_id))
        {
            Log::error("PublicController:buyerRegister Something went wrong");
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
        else
        {

            Log::info("PublicController:buyerRegister User register successfully");

            ## Get role id of role
            $roleObj = new CommonService("roles");
            $role    = $roleObj->getInfo(array("role_key" => "wholesale_buyer"));

            if (!empty($role))
            {
                Log::info("PublicController:buyerRegister !empty role " . $this->request->input('role'));
                // Find role id from
                $insert            = array();
                $insert['role_id'] = $role->id;
                $insert['user_id'] = $user_id;

                $userRoleObj = new CommonService("user_roles");
                $userRoleObj->saveInfo($insert);
            }

            ## save users plan into users_plan table
            ## First county price
            $per_county_price = config("constants.first_county_price");;
            $usersPlan         = new CommonService("users_plans");
            $insert            = array();
            $insert['user_id'] = $user_id;

            $plan_region = $this->request->input('booster_plan');
            if (!empty($plan_region))
            {
                $plan_price            = array("west_nc" => 300, "full_nc" => 500);
                $insert['plan_region'] = $plan_region;
                $insert['plan_state']  = "";
                $insert['plan_county'] = "";
                $insert['plan_price']  = $plan_price[$plan_region];
                $usersPlan->saveInfo($insert);
            }

            ## First county price
            $per_county_price = config("constants.first_county_price");

            $t_county          = $this->request->input('plan_county');
            $plan_state_county = explode("|||", $t_county);

            foreach ($plan_state_county as $key => $value)
            {
                $t_array               = explode("::", $value);
                $insert['plan_region'] = "";
                $insert['plan_state']  = $t_array[0];
                $insert['plan_county'] = @$t_array[1];
                $insert['plan_price']  = $per_county_price;
                $usersPlan->saveInfo($insert);

                ## after second county price will be 50
                $per_county_price = config("constants.per_county_price");
            }

            #Send Welcome Email for normal User.
            $this->mailService->welcomeMail($user_id);

            return response()->json(['message' => __("messages.buyer_register"), "row" => array("user_id" => $user_id)], 200);
        }
    }

    public function forgotPassword()
    {
        // TO DO: implement common exception handle for all controller
        Log::info("PublicController: forgotPassword called");

        ## Validation rules formation
        $rules = [
            'email' => 'required|email',
        ];
        ## check input validation
        Log::info("PublicController: forgotPassword validation check email");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        $user = \App\Models\User::where("email", $this->request->input("email"))->get()->first();

        if ($user)
        {
            Log::error("PublicController: user info found ");
            ## create token
            $token = md5(date("Y-m-d H:i:s") . '' . $user->email);
            // Find role id from
            $insert               = array();
            $insert['token']      = $token;
            $insert['user_id']    = $user->id;
            $insert['created_at'] = time();

            $password_code = new CommonService("password_reset_code");
            $password_code->saveInfo($insert);

            MailService::forgotPasswordMail($user, $token);
            return response()->json(['message' => __("messages.check_mail")], 200);
        }
        else
        {
            Log::info("PublicController: forgotPassword email not exists");
            return response()->json(['message' => __("error_messages.email_not_exists")], 400);
        }

    }

    public function resetPassword()
    {
        // TO DO: implement common exception handle for all controller
        Log::info("PublicController: resetPassword called");

        ## Validation rules formation
        $rules = [
            'token'    => 'required',
            'password' => 'required|min:6',
        ];

        ## check input validation
        Log::info("PublicController: resetPassword validation check token");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        // Get role id of role
        $passwordCode = new CommonService("password_reset_code");
        $tokenInfo    = $passwordCode->getInfo(array("token" => $this->request->input('token')));

        if ($tokenInfo)
        {
            ## Token is old then 24 hours.
            if ((time() - $tokenInfo->created_at) / (60 * 60 * 24) > 1)
            {
                ## remove this link, because it is not valid
                $passwordCode->deleteInfo(array("id" => $tokenInfo->id));

                Log::info("PublicController: resetPassword token expired ");
                return response()->json(['message' => __("error_messages.token_expire")], 400);
            }
            else
            {
                Log::info("PublicController: resetPassword reset password of user ");
                $update             = array();
                $update['password'] = md5($this->request->input('password'));

                $userObj = new CommonService("users");
                $userObj->saveInfo($update, array("id" => $tokenInfo->user_id));

                MailService::resetPasswordMail($tokenInfo->user_id);

                ## remove all token of this user from table
                $passwordCode->deleteInfo(array("user_id" => $tokenInfo->user_id));

                return response()->json(['message' => __("messages.password_change")], 200);
            }
        }
        else
        {
            Log::info("PublicController: resetPassword token is invalid");
            return response()->json(['message' => __("error_messages.token_invalid")], 400);
        }

    }

    public function contactUs()
    {
        // TO DO: implement common exception handle for all controller
        Log::info("PublicController: contactUs called");

        ## Validation rules formation
        $rules = [
            'your_name' => 'required',
          //  'company'   => 'required',
            'phone'     => 'required',
            'email'     => 'required|email',
            'message'   => 'required',
        ];

        ## check input validation
        Log::info("PublicController: contactUs validation check token");
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        ## Insert contact us Log
        $info       = $this->request->all();
        $info['company']=empty($info['company'])?'':$info['company'];

        $info['ip'] = $this->request->ip();

        #ToDO: trace user id if he is login

        $contact_us = ContactUs::create($info);

        ## Send contact us Mail to administrator
        MailService::contactUsMail($this->request->all());
        return response()->json(['message' => __("messages.contact_us")], 200);
    }

    public function braintreeToken($user_id)
    {
        if(empty($user_id))
            $user_id = $this->request->input('user_id');

        if(empty($user_id))
        {
            return response()->json(['status' => 'failed', "message"=>"Invalid User.", 'data' => []], 200);

        }

        $user = User::find($user_id);

        if(empty($user))
        {
            return response()->json(['status' => 'failed', "message"=>"Invalid User.", 'data' => []], 200);
        }

        $errorCode = false;
        $isBrainTreeCustomerExists = false;

        if (!empty($user_id)) {
            try {
                $isBrainTreeCustomerExists = \Braintree\Customer::find($user_id);
            }
            catch (\Braintree\Exception\NotFound $ex) {
                $errorCode = ($ex->getCode());
            }
            catch (Exception $ex) {
                $errorCode = ($ex->getCode());
            }
        }

        // if customer not exists then create new one
        if (empty($isBrainTreeCustomerExists) || ($errorCode === 0)) {
            $result = \Braintree\Customer::create(array(
                'id' => $user_id,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'company' => 'Not Available',
                'email' => $user->email,
                'phone' => $user->mobile,
            ));

            if ($result->success != 1) {
                // ToDo: send one email to IT Team to track issue.
                return response()->json(['status' => 'failed', "message"=>"Something went wrong, Please try after sometime.", 'data' => []], 200);
            }

        }

        $info = [];
        $info['token'] = \Braintree\ClientToken::generate(
            ["customerId" => $user_id]
        );

        // get total amount of county and plan
        $total_price = UsersPlansModel::where('user_id',$user_id)->sum('plan_price');

        if($total_price < 0)
        {
            // ToDo: send one email to IT Team to track issue.
            Log::alert('Total price is less ', [$total_price]);
            throw new CustomException('Total price is less.',503);
            // return response()->json(['status' => 'failed', "message"=>"Something went wrong, Please contact support team.", 'data' => []], 200);

        }
        $info['total'] = $total_price;



        return response()->json(['status'=>'success','data' => $info], 200);

    }

    public function braintreeSubscription()
    {
        $user_id = $this->request->input('user_id');

        if(empty($user_id))
        {
            return response()->json(['status' => 'failed', "message"=>"Invalid User.", 'data' => []], 200);

        }

        $user = User::find($user_id);

        if(empty($user))
        {
            return response()->json(['status' => 'failed', "message"=>"Invalid User.", 'data' => []], 200);
        }

        $nounce = $this->request->input('nonce');

        if(empty($nounce))
        {
            return response()->json(['status' => 'failed', "message"=>"Invalid payment process.", 'data' => []], 200);
        }
        // get total amount of county and plan
        $total_price = UsersPlansModel::where('user_id',$user_id)->sum('plan_price');

        if($total_price < 0)
        {
            Log::alert('Total price is less ', [$total_price]);
            throw new CustomException('Total price is less.',503);
        }

        $plan_id = env('PLAN_ID');
        try{
            $result = \Braintree\Subscription::create([
                'paymentMethodNonce' => $nounce,
                'planId' => $plan_id,
                'price' => $total_price
            ]);
        }
        catch(Exception $ex){
            Log::emergency("-----------------------".date("Y-m-d H:i:s")."------------------------------");
            $temp = print_r($ex, true);
            Log::emergency("-----------------------".$temp."------------------------------");

            $error_message = $ex->getMessage();
            $error_coode = ($ex->getCode());
            $insert_data = array();
            $insert_data['error_code'] = $error_coode.'|||'.$error_message;
            $insert_data['user_id'] = $user_id;
            $insert_data['response'] = '';
            $insert_data['price'] = $total_price;
            $insert_data['status'] = 'failed';
            UserPaymentLog::create($insert_data);

            Log::emergency('Total price is less '.$ex->getMessage());
            throw new CustomException('Total price is less.',503);
            return response()->json(['status' => 'failed', "message"=>"Something went wrong, Please try again.", 'data' => []], 200);
        }

        if ($result->success) {
            # payment history
            $insert_data = array();
            $insert_data['error_code'] = '';
            $insert_data['user_id'] = $user_id;
            $insert_data['response'] = base64_encode(json_encode($result));
            $insert_data['price'] = $total_price;
            $insert_data['status'] = 'success';
            UserPaymentLog::create($insert_data);

            // update user info for wholesale yes and is payed yes
            $update_data = [];
            $update_data['is_payed'] = '1';
            //$update_data['status'] = 'active';
            $user->update($update_data);

            # send success email to buyer
            $this->mailService->buyerWelcomeMailAfterPayment($user_id);

            return response()->json(['status' => 'success', "message"=>"Your payment has been done successfully. Please continue to login.", 'data' => []], 200);

        } else if ($result->transaction) {

            $code = '';
            foreach($result->errors->deepAll() AS $error) {
                Log::emergency("-----------------------".date("Y-m-d H:i:s")."------------------------------");
                $code = "".$error->attribute . ": " . $error->code . " " . $error->message;
                Log::emergency($code );
            }

            $insert_data = array();
            $insert_data['error_code'] = $code;
            $insert_data['user_id'] = $user_id;
            $insert_data['response'] = base64_encode(json_encode($result));
            $insert_data['price'] = $total_price;
            $insert_data['status'] = 'failed';
            UserPaymentLog::create($insert_data);

            return response()->json(['status' => 'failed', "message"=>"Something went wrong, Please try again.", 'data' => []], 200);

        } else {

            foreach($result->errors->deepAll() AS $error) {
                Log::emergency("-----------------------".date("Y-m-d H:i:s")."------------------------------");
                $code = "".$error->attribute . ": " . $error->code . " " . $error->message;
                Log::emergency( $code);
            }

            #Payment Log
            $insert_data = array();
            $insert_data['error_code'] = $code;
            $insert_data['user_id'] = $user_id;
            $insert_data['response'] = base64_encode(json_encode($result));
            $insert_data['price'] = $total_price;
            $insert_data['status'] = 'failed';
            UserPaymentLog::create($insert_data);
            return response()->json(['status' => 'failed', "message"=>"Something went wrong, Please try again.", 'data' => []], 200);

        }

        return response()->json(['status' => 'failed', "message"=>"Something went wrong, Please try again.", 'data' => []], 200);
    }


    public function test()
    {

        //send email to client
        $to                   = "rativardhan@gmail.com";
        $data['cc']           = "rativardhan+1@gmail.com";
        $subject              = "New property available for Sale in NY";
        $data['main_message'] = array("");


        $this->mailService->welcomeMail(1935);
        $this->mailService->buyerWelcomeMailAfterPayment(1935);
        //$this->mailService->welcomeMail(10);
        return response()->json(['message' => "Testing"], 201);
    }

    public function test_attach()
    {

        //send email to client
        $to                   = "rativardhan@gmail.com";
        $data['cc']           = "rativardhan+1@gmail.com";
        $subject              = "New property available for Sale in NY";
        $data['main_message'] = array("");
        $fileDir              = storage_path() . "/aggreement_doc";
        //        $attachments[] = "$fileDir/agreement1.pdf";
        //        $attachments[] = "$fileDir/agreement2.doc";
        //        $attachments[] = "$fileDir/agreement3.xls";
        //        $data['attachments'] = $attachments;
        MailService::sendMail($to, "Rativardhan", $subject, "emails.send-client-mail", $data);
    }

    public function test12()
    {

        $isDone = [];

        $result = DB::connection('olddb')
            ->table('user_payment_log')
            ->select([
                '*',
            ])
            //->where('user_id','=','1606')
            ->where('user_payment_log.payment_type','=', 'wholesale')
            ->orderBy('date_added','asc')
            ->get();
        $outputs = [];
        foreach($result as $value)
        {

            $output = [];
            $output['user_id'] = $value->user_id;
            $output['date_added'] = $value->date_added;
            $base64 = json_decode(base64_decode($value->response),0);


//            echo '<pre>';
//            var_dump(@$base64->_attributes->transaction->_attributes->customerDetails);
//            die;

            $cd = @$base64->_attributes->transaction->_attributes->customerDetails->_attributes;
            if(empty($cd))
            {
                $cd = @$base64->subscription->_attributes->transaction[0]->_attributes->customer;
            }

            if(empty($cd))
            {
                $cd = @$base64->subscription->_attributes->transactions[0]->_attributes->customer;
            }

            $output['subscription_id'] = @$base64->subscription->_attributes->transactions[0]->_attributes->id;
            $output['amount'] = @$base64->subscription->_attributes->transactions[0]->_attributes->amount;
            $output['first_name'] = @$cd->firstName;
            $output['last_name'] = @$cd->lastName;
            $output['email'] = @$cd->email;
            $output['braintree_customer_id'] = @$cd->id;


            $output['json_all'] = base64_decode($value->response);
            $outputs[] = $output;

            if( $value->user_id == 670)
            {
               // var_dump();
                //break;
            }

        }
        echo '<table border="1">';
        echo '<tr>';
        foreach($outputs as $value)
        {
            foreach($value as $tk => $tv)
            {
                // var_dump($tvalue);
                echo '<td>'.$tk.'</td>';
            }
            break;
        }
        echo '</tr>';

        foreach($outputs as $value)
        {
            echo '<tr>';
            foreach($value as $tvalue)
            {
               // var_dump($tvalue);
               echo '<td>'.$tvalue.'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        die;
        //dd($outputs);

    }

    function getInviteEmail($from,$to){
        $records=$this->inviteService->getInviteInfo($from,$to);
        //dd($data);
        $estateOldUrl='https://ng.estatestracking.com/';
        foreach($records as $data){

        
            $house_id=$data->house_id;
            $property_info = $this->propertyService->findOneById($house_id);

            $token       = $this->houseTokenService->token($house_id);
            $link        = $estateOldUrl . 'quickview/' . $token . '/' . CommonHelper::url_slug($property_info);

            $sale_date = CustomHelper::revert_date_format_database(@$property_info->last_sale_details->sale_date);
            $address = CommonHelper::countyAddressFormat($property_info);
            $subject = "Property Invitation.";
            # send invite mail now to all users.
            // $invitee_email_mail = array_unique($invitee_email_mail);
            // foreach ($invitee_email_mail as $email)
            // {
            
            // }
            
            $email_info = array(
                'house_id'    => $house_id,
                'to'    => $data->invitee_email,//trim($email),
                'subject'     => $data->invitee_subject.' '.$sale_date.' '.$address,
                'message'     => $data->invitee_message,
                'link_anchor' => $link,
                //'date'=>date('D, d M Y H:i:s',$data->created_at)
                'date'=>date('D, d M Y',$data->created_at)

            );
           // dd($email_info);
            $footer                = array();
            $footer['TEAM_NAME']   = env("TEAM_NAME");
            $footer['TEAM_DOMAIN'] = $estateOldUrl;//env("TEAM_DOMAIN");

            echo view('emails.demo_invite_users', ['info'=>$email_info,'footer'=>$footer]);
            
        }
            // 'emails.buyit', $data
    }
}
