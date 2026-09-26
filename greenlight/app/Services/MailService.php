<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\EmailSettingsModel;
use Log;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\PDF;

class MailService  {

    public static $footer;
    public        $userService;
    public        $propertyService;
    public        $emailsAmService;
    public        $houseBuyItService;
    public        $houseTokenService;
    private       $disk;
    private       $pdf;
    private       $urlPdfCsvExcelService;

    public function __construct(UserService $userService,
                                PropertyService $propertyService,
                                EmailsAmService $emailsAmService,
                                HouseBuyItService $houseBuyItService,
                                HouseTokenService $houseTokenService,
                                UrlPdfCsvExcelService $urlPdfCsvExcelService,
                                PDF $pdf

    ) {
        Log::info("MailService: __construct called");
        $this->userService       = $userService;
        $this->propertyService   = $propertyService;
        $this->emailsAmService   = $emailsAmService;
        $this->houseBuyItService = $houseBuyItService;
        $this->houseTokenService = $houseTokenService;
        $this->urlPdfCsvExcelService = $urlPdfCsvExcelService;
        $this->pdf               = $pdf;

          ## ToDo: make a queue of all emails for faster response..
    }


    protected static function toModify($email) {

        if (strtolower(app()->env) == "production") {
            return $email;
        }
        // in Non production case no need to send any email to any user
        $start = explode('@',$email);
        return "rati+".$start[0]."@theGreenlight Property Finder.com";
    }

    protected static function toBcc($message) {
        //$message->bcc('vikas.gupta026@gmail.com');
        // return $email;
        if (strtolower(app()->env) == "production") {
            //$message->bcc('vikas.gupta026@gmail.com');
            //$message->bcc('willow.storm@greenlight-community.com');
            //$message->bcc('david.ginn@greenlight-community.com');
        }
    }

    protected static function signature() {
        Log::info("MailService: signature called");
        $footer                = array();
        $footer['TEAM_NAME']   = env("TEAM_NAME");
        $footer['TEAM_DOMAIN'] = env("TEAM_DOMAIN");
        self::$footer          = $footer;
    }

    protected static function subjectEnvironmentModify($subject) {

        Log::info("MailService: subjectEnvironmentModify called");
        if (strtolower(app()->env) != "production") {
            $subject = "--" . strtoupper(strtolower(app()->env)) . "--" . $subject;
            return $subject;
        }
        else if (strtolower(app()->env) == "production") {
            $subject = "" . $subject;
            return $subject;
        }

        return $subject;
    }

    public static function forgotPasswordMail($user, $token) {
        if (empty($user)) return false;

        Log::info("MailService: forgotPasswordMail called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.forgot_subject");

        // Variable initialise
        $data               = array();
        $data['user']       = $user;
        $data['footer']     = self::$footer;
        $data['subject']    = self::subjectEnvironmentModify($subject);
        $data['reset_link'] = env("APP_FRONTEND") . 'reset_password/' . $token;

        $result = Mail::send('emails.forgot_password', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['user']->email))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: forgotPasswordMail Could not send message to" . $user->email);
        }

        if (empty($result)) {
           // Log::error("MailService: forgotPasswordMail Email could not be sent." . $user->email);
        }
        return false;
    }

    public static function resetPasswordMail($user_id) {
        if (empty($user_id)) return false;

        Log::info("MailService: resetPasswordMail called");

        // Signature modify
        self::signature();
        $user    = \App\Models\User::find($user_id);
        $subject = __("emails_messages.reset_subject");

        // Variable initialise
        $data            = array();
        $data['user']    = $user;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        $result = Mail::send('emails.reset_password', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['user']->email))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: resetPasswordMail Could not send message to" . $user->email);
        }

        if (empty($result)) {
            //Log::error("MailService: resetPasswordMail Email could not be sent." . $user->email);
        }
        return false;
    }

    public static function contactUsMail($info) {

        Log::info("MailService: contactUsMail called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();
        $subject = __("emails_messages.contact_us", array("name" => $info['your_name']));

        // Variable initialise
        $data            = array();
        $data['info']    = $info;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        # ToDo: convert all mail into mailable
        //        $now = Carbon::now()->addMinutes(1);
        //        $welcome  = new WelcomeMail('emails.contact_us', $data);
        //        $welcome->build();
        //        Mail::to('rativardhan@gmail.com')
        //            ->send($now, $welcome);

        $result = Mail::send('emails.contact_us', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(env("CONTACT_US_EMAIL_TO", "rativardhan@gmail.com"))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: contactUsMail Could not send message to");
        }


        if (empty($result)) {
            //Log::error("MailService: contactUsMail Email could not be sent.");
        }
        return false;
    }

    public static function sendMail($to, $to_name, $subject, $view, $data) {
        $signature = '<b>Confidentiality Notice:</b><br>
                        <i> This email message, including any attachments, is for the sole use of the intended recipient(s) and may contain confidential and privileged information. Any unauthorized use, disclosure or distribution is prohibited. If you are not the intended recipient, please contact the sender by reply email and destroy all copies of the original message. We will try to respond within 30 minutes.<i>';

        /*
        * TEST ENV
        * Desc - Overrieds actula params and send mail to test accounts, also share the cc email details
        */

        #$signature = \Lang::get('messages.signature');
        $contact_info = array();
        if (strtolower(app()->env) != "production") {

            $subject        = "--TEST--Greenlight Property Finder:" . $subject;
            $contact_info[] = "To: $to";
            $to             = "testing_estate_mail@gmail.com";
            if (array_key_exists('cc', $data)) {
                $contact_info[] = "CC: {$data['cc']}";
                $data['cc']     = '';
            }
        }


        /*
        * PRODUCTION ENV
        * Desc - Overrieds actula params and send mail to test accounts, also share the cc email details
        */
        $data["to"]           = $to;
        $data["to_name"]      = $to_name;
        $data['subject']      = $subject;
        $data['contact_info'] = $contact_info;
        if (!array_key_exists('cc', $data)) {
            $data['cc'] = '';
        }
        $data['signature'] = $signature;


        /*
        * SEND MAIL
        * Desc - This will send mail to users with logging of invalid details and attachments
        */
        Mail::send($view, $data, function ($message) use ($data) {
            self::toBcc($message);
            if (array_key_exists('from', $data)) {
                $message->from($data['from'], $data['from_name']);
            }

            //create invalid email log file
            $logPath         = public_path() . "/../storage/logs/invalid_email_log";
            $invalidEmailLog = new Logger("Invalid Email Logs");
            $invalidEmailLog->pushHandler(new StreamHandler($logPath, Logger::WARNING));

            $email_recipient  = $data["to"];
            $email_recipients = explode(",", $email_recipient);
            foreach ($email_recipients as $email) {
                $email   = trim($email);
                $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
                if ($isValid) {
                    $message->to(self::toModify($email));
                }
                else {
                    $invalidEmailLog->addWarning("invalid email: $email");
                    continue;
                }
            }

            $email_recipient = $data["cc"];
            if ($email_recipient != '') {
                $email_recipients = explode(",", $email_recipient);
                foreach ($email_recipients as $email) {
                    $email   = trim($email);
                    $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
                    if ($isValid) {
                        $message->cc($email);
                    }
                    else {
                        $invalidEmailLog->addWarning("invalid email: $email");
                        continue;
                    }
                }
            }

            $message->subject($data['subject']);
            if (array_key_exists('attachments', $data)) {
                $attachments = $data['attachments'];
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }
        });

    }


    public function welcomeMail($user_id) {
        if (empty($user_id)) return false;

        Log::info("MailService: welcomeMail called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.welcome_subject") .' '. env("FROM_NAME");
        $user    = \App\Models\User::where('id','=',$user_id)->with('user_roles')->get()->first();

        // Variable initialise
        $data            = array();
        $data['user']    = $user;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['name'] = CommonHelper::nameFormat($user);
        $data['is_buyer'] = $this->userService->isBuyerFromAllRoles($user);
        $data['username'] = $user->username;
        $data['email'] = $user->email;

        $result = Mail::send('emails.welcome', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['user']->email))->subject($data['subject']);
            self::toBcc($message);

        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $user->email);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $user->email);
        }
        return false;
    }

    public function buyerWelcomeMailAfterPayment($user_id) {

        if (empty($user_id)) return false;

        Log::info("MailService: welcomeMail called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.welcome_subject") .' '. env("FROM_NAME");
        $user    = \App\Models\User::where('id','=',$user_id)->with('user_roles')->get()->first();

        // Variable initialise
        $data            = array();
        $data['user']    = $user;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['name'] = CommonHelper::nameFormat($user);


        $result = Mail::send('emails.welcome_paid_buyer', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['user']->email))->subject($data['subject']);
            self::toBcc($message);

        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $user->email);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $user->email);
        }
        return false;
    }

    public function sendBuyItEmailToAM($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendBuyItEmailToAM called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        # Tonya Getting TX buy it email, We are adding hardcoded condition to remove her form email list
        $tx_skip_email = [];
        $tx_skip_email[] = 'homeproblemssolved@gmail.com';
        //$tx_skip_email[] = 'tonya@theestates.com';
        // if (strtolower($addressInfo->state) == 'tx') {
        //     $tx_skip_email[] = 'tonya@theestates.com';
        // }

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $subject = __("emails_messages.buy_it_request", array("name"    => $name,
                                                              "address" => $address,
                                                              "saleType"=> $info['sale_type'],
                                                              "saleDate"=> $info['sale_date'],
                                                              "position" =>$info['position'],
                                                              "today_date"=>date('m/d/Y')));
        
        // Variable initialise
        $info['first_name'] = $name;
        $data               = array();
        $data['info']       = $info;
        $data['footer']     = self::$footer;
        $data['subject']    = self::subjectEnvironmentModify($subject);

        $email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);

        foreach ($email_list as $key => $value) {
            if (empty($value['email'])) continue;

            $email         = $value['email'];
            $data['email'] = $email;
            if (in_array(strtolower($value['email']), $tx_skip_email)) continue;

            $result = Mail::send('emails.buyit', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        $temp_search_email   = array();
        //$temp_search_email[] = array('email' => 'david.ginn@greenlight-community.com');
        //$temp_search_email[] = array('email' => 'willow.storm@greenlight-community.com');
        //$temp_search_email[] = array('email' => 'tonya@theestates.com');//homeproblemsolved
        // ToDO:

        $subUserEmails=$this->userService->getSubtoSetting($addressInfo->state,$addressInfo->county);
        if(!empty($subUserEmails)){
            foreach($subUserEmails as $userEmail){
                if(!in_array($userEmail->user->email,$temp_search_email)){
                    $temp_search_email[]=array('email' => $userEmail->user->email);
                }
            }
        }
        
        if (strtolower($addressInfo->state) == 'nc' || strtolower($addressInfo->state) == 'sc') {
            $temp_search_email[] = array('email' => 'michelle@theestates.com');
            //$temp_search_email[] = array('email' => 'samr@theestates.com');
            $temp_search_email[] = array('email' => 'joshua@theestates.com');
            $temp_search_email[] = array('email' => 'ninalesquire@gmail.com');
            
        }
        if(strtolower($addressInfo->state) == 'sc'){
            $temp_search_email[] = array('email' => 'jane@theestates.com');
        }
        if (strtolower($addressInfo->state) == 'nc'){
            $temp_search_email[] = array('email' => 'rob@theestates.com');
        }
        if (strtolower($addressInfo->state) == 'fl') {
            $temp_search_email[] = array('email' => 'joshua@theestates.com');
        }
        if (strtolower($addressInfo->state) == 'ga') {
            $temp_search_email[] = array('email' => 'ryan@theestates.com');
            $temp_search_email[] = array('email' => 'smith@theestates.com');
        }

        $countyList=['Harris','Fort Bend','Galveston', 'Montgomery','Brazoria','Chambers','Liberty', 'Bexar'];
        $countyList1=["Dallas","‎Collin","‎Denton","‎Rockwall‎","Grayson","Hood","Hunt","Tarrant"];
        if(strtolower($addressInfo->state)=='tx'){
            $temp_search_email[] = array('email' => 'karen@theestates.com');
            $temp_search_email[] = array('email' => 'parker@theestates.com');
            $temp_search_email[] = array('email' => 'hank@theestates.com');
            $temp_search_email[] = array('email' => 'seth@theestates.com');
            $temp_search_email[] = array('email' => 'smith@theestates.com');
            if(in_array($addressInfo->county,$countyList)){
                $temp_search_email[] = array('email' => 'estatesllcamandat@gmail.com');
            }else if(in_array($addressInfo->county,$countyList1)){
                $temp_search_email[] = array('email' => 'j.reidbills@comcast.net');
            }
        }

        foreach ($temp_search_email as $key => $value) {

            if (in_array(strtolower($value['email']), $tx_skip_email)) continue;

            $email         = $value['email'];
            $data['email'] = $email;
            $result = Mail::send('emails.buyit', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendBuyItEmailToAM Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendBuyItEmailToAM Email could not be sent.");
        }
        return false;

    }
    public function sendBuyItEmailToUser($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendBuyItEmailToUser called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        
        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $subject = __("emails_messages.buy_it_request", array("name"    => $name,
                                                              "address" => $address,
                                                              "saleType"=> $info['sale_type'],
                                                              "saleDate"=> $info['sale_date'],
                                                              "position" =>$info['position'],
                                                              "today_date"=>date('m/d/Y')));
          // $subject = __("emails_messages.buy_it_request_to_user", array(
        //     "address" => $address));

        // Variable initialise
        $info['first_name'] = $name;
        $data               = array();
        $data['info']       = $info;
        $data['footer']     = self::$footer;
        $data['subject']    = self::subjectEnvironmentModify($subject);

        $email         = $user->email;
        $data['email'] = $email;
        $data['user_type'] = 'you';
        $result = Mail::send('emails.buyit', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendBuyItEmailToUser Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendBuyItEmailToUser Email could not be sent.");
        }

        return false;

    }

    public function tempEmailsendBuyItEmailToUser($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendBuyItEmailToUser called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $user        = $this->userService->findOneById($info['user_id']);
        $addressInfo = $this->propertyService->findOneById($info['house_id'], false);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $subject = __("emails_messages.buy_it_request_to_user", array(
            "address" => $address));

        echo '<p>Sending emails to '.$name.' : email: '.$user->email.'<p>';;

        // Variable initialise
        $info['first_name'] = $name;
        $data               = array();
        $data['info']       = $info;
        $data['footer']     = self::$footer;
        $data['subject']    = self::subjectEnvironmentModify($subject);

        $email         = $user->email;
        $data['email'] = $email;
        $data['user_type'] = 'you';
        $result = Mail::send('emails.buyit', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendBuyItEmailToUser Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendBuyItEmailToUser Email could not be sent.");
        }

        return false;

    }


    function sendPassItEmailToAM($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendPassItEmailToAM called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $subject = __("emails_messages.pass_it_request", array("name"    => $name,
                                                               "address" => $address));

        // Variable initialise
        $info['first_name'] = $name;
        $data               = array();
        $data['info']       = $info;
        $data['footer']     = self::$footer;
        $data['subject']    = self::subjectEnvironmentModify($subject);

        $email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);

        foreach ($email_list as $key => $value) {
            if (
                strtolower($value['email']) == 'homeproblemssolved@gmail.com' || 
                //strtolower($value['email']) == 'sharon@theestates.com' || 
                strtolower($value['email']) == 'lynn@theestates.com'  
                //strtolower($value['email']) == 'tonya@theestates.com'

            ) {
                continue;
            }
            if (empty($value['email'])) continue;

            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.passit', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendPassItEmailToAM Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendPassItEmailToAM Email could not be sent.");
        }
        return false;

    }
    function sendPassItEmailTouser($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendPassItEmailTouser called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $subject = __("emails_messages.pass_it_request_to_user", array("name"    => $name,
            "address" => $address));

        // Variable initialise
        $info['first_name'] = $name;
        $data               = array();
        $data['info']       = $info;
        $data['footer']     = self::$footer;
        $data['subject']    = self::subjectEnvironmentModify($subject);

        $email         = $user->email;
        $data['email'] = $email;

        $result = Mail::send('emails.passit_to_user', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendPassItEmailTouser Could not send message to");
        }
        return false;
    }

    function sendBuyItPositionUpdate($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendBuyItPositionUpdate called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);


        $address = CommonHelper::addressFormat($addressInfo);
        $subject = __("emails_messages.buy_it_position_update", array("address" => $address));

        // Variable initialise
        $data            = array();
        $data['info']    = $info;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        $user_list = $this->houseBuyItService->getDownBuyitUser($info['house_id'], $info['position']);

        $continue_array = [];
        foreach ($user_list as $key => $value) {
            if (in_array($value['email'], $continue_array)) continue;
            $email                = $value['email'];
            $continue_array[]     = $email;
            $data['email']        = $email;
            $data['info']['name'] = CommonHelper::nameFormat($value);

            $result = Mail::send('emails.buyit_position_update', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendBuyItPositionUpdate Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendBuyItPositionUpdate Email could not be sent.");
        }
        return false;
    }

    function inviteUser($email, $info) {

        if (empty($info['house_id']) || empty($email)) return false;

        Log::info("MailService: inviteUser called");
        if (empty($info)) return false;

        // Signature modify
        self::signature();

        $subject = @$info['subject'];

        // Variable initialise
        $data            = array();
        $data['info']    = $info;
        $data['footer']  = self::$footer;
        $data['email']   = $email;

        $data['subject'] = self::subjectEnvironmentModify($subject);
        
        $result = Mail::send('emails.invite_users', $data, function ($message) use ($data) {
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: inviteUser Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: inviteUser Email could not be sent.");
        }
        return false;
    }

    function sendLenderItEmailToAmAndInsurance($info) {


        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendLenderItEmailToAmAndInsurance called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $mapType = CommonHelper::mapType($addressInfo);
        $subject = __("emails_messages.get_a_lender_email_to_am", array("name"    => $name,
                                                                        "address" => $address));

        // Variable initialise
        $data                = array();
        $info['map_type']    = $mapType;
        $info['first_name']  = $name;
        $info['email']       = $user->email;
        $info['address']     = $address;
        $info['address_url'] = $this->houseTokenService->address_url($info['house_id'], $addressInfo);
        $data['info']        = $info;
        $data['footer']      = self::$footer;
        $data['subject']     = self::subjectEnvironmentModify($subject);

        // Send email to Email_settings
        $county              = $addressInfo->county;
        $emailSettings       = EmailSettingsModel::where('email_type', 'get_a_lender')->where(function ($query) use ($county) {
            $query->where('county', 'all');
            if (!empty($county)) $query->orWhere('county', $county);
        });
        $emailSettingsEmails = $emailSettings->get();

        foreach ($emailSettingsEmails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.lender_it', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        $get_a_lender   = [];
        //$get_a_lender[] = array('email' => 'david.ginn@greenlight-community.com');
        //$get_a_lender[] = array('email' => 'willow.storm@greenlight-community.com');

        foreach ($get_a_lender as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.lender_it', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        /*$email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);
        foreach ($email_list as $key => $value)
        {
          if(empty($value['email']))
                continue;

            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.lender_it', $data, function ($message) use ($data)
            {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to($data['email'])->subject($data['subject']);
        self::toBcc($message);
            });

        }*/

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendLenderItEmailToAmAndInsurance Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendLenderItEmailToAmAndInsurance Email could not be sent.");
        }
        return false;
    }

    function sendDepositMail($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendDepositMail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $mapType = CommonHelper::mapType($addressInfo);
        $subject = __("emails_messages.deposit", array("name"    => $name,
                                                       "address" => $address));

        // Variable initialise
        $data                  = array();
        $info['map_type']      = $mapType;
        $info['first_name']    = $name;
        $info['email']         = $user->email;
        $info['address']       = $address;
        $link                  = $this->houseTokenService->address_url($info['house_id'], $addressInfo);
        $info['address_url']   = $link;
        $info['fund_deal_url'] = $link . "/fundDeal";
        $data['info']          = $info;
        $data['footer']        = self::$footer;
        $data['subject']       = self::subjectEnvironmentModify($subject);

        // Send email to Email_settings
        $county              = $addressInfo->county;
        $emailSettings       = EmailSettingsModel::where('email_type', 'get_deposit')->where(function ($query) use ($county) {
            $query->where('county', 'all');
            if (!empty($county)) $query->orWhere('county', $county);
        });
        $emailSettingsEmails = $emailSettings->get();

        foreach ($emailSettingsEmails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.deposit', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        $get_deposit   = [];
        //$get_deposit[] = array('email' => 'david.ginn@greenlight-community.com');
        //$get_deposit[] = array('email' => 'willow.storm@greenlight-community.com');
        $get_deposit[] = array('email' => $this->userService->user()->email);

        foreach ($get_deposit as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.deposit', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        /*$email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);
        foreach ($email_list as $key => $value)
        {
        if(empty($value['email']))
                continue;


            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.lender_it', $data, function ($message) use ($data)
            {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to($data['email'])->subject($data['subject']);
        self::toBcc($message);
            });

        }*/

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendDepositMail Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendDepositMail Email could not be sent.");
        }
        return false;
    }

    function sendRenovationMail($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendRenovationMail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $mapType = CommonHelper::mapType($addressInfo);
        $subject = __("emails_messages.renovation_bid", array("name"    => $name,
                                                              "address" => $address));

        // Variable initialise
        $data                = array();
        $info['map_type']    = $mapType;
        $info['first_name']  = $name;
        $info['email']       = $user->email;
        $info['address']     = $address;
        $link                = $this->houseTokenService->address_url($info['house_id'], $addressInfo);
        $info['address_url'] = $link;
        $data['info']        = $info;
        $data['footer']      = self::$footer;
        $data['subject']     = self::subjectEnvironmentModify($subject);

        // Send email to Email_settings
        Log::info("MailService: Email_settings start");
        $county              = $addressInfo->county;
        $emailSettings       = EmailSettingsModel::where('email_type', 'renovation_contractor')->where(function ($query) use ($county) {
            $query->where('county', 'all');
            if (!empty($county)) $query->orWhere('county', $county);
        });
        $emailSettingsEmails = $emailSettings->get();

        foreach ($emailSettingsEmails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.renovation_bid', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        Log::info("MailService: temp emails start");
        $temp_emails   = [];
        //$temp_emails[] = array('email' => 'david.ginn@greenlight-community.com');
        //$temp_emails[] = array('email' => 'willow.storm@greenlight-community.com');
        // $temp_emails[] = array('email' => $this->userService->user()->email);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.renovation_bid', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        Log::info("MailService: emailsAmService start");
        $email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);
        foreach ($email_list as $key => $value) {
            if (empty($value['email'])) continue;

            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.renovation_bid', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendRenovationMail Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendRenovationMail Email could not be sent.");
        }
        return false;
    }

    function sendTitleSearchMail($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendTitleSearchMail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $mapType = CommonHelper::mapType($addressInfo);
        $subject = __("emails_messages.title_search", array("name"    => $name,
                                                            "address" => $address));

        // Variable initialise
        $data                = array();
        $info['map_type']    = $mapType;
        $info['first_name']  = $name;
        $info['email']       = $user->email;
        $info['address']     = $address;
        $link                = $this->houseTokenService->address_url($info['house_id'], $addressInfo);
        $info['address_url'] = $link;
        $data['info']        = $info;
        $data['footer']      = self::$footer;
        $data['subject']     = self::subjectEnvironmentModify($subject);

        // Send email to Email_settings
        Log::info("MailService: Email_settings start");
        $county              = $addressInfo->county;
        $emailSettings       = EmailSettingsModel::where('email_type', 'title_search')->where(function ($query) use ($county) {
            $query->where('county', 'all');
            if (!empty($county)) $query->orWhere('county', $county);
        });
        $emailSettingsEmails = $emailSettings->get();

        foreach ($emailSettingsEmails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.title_search', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        Log::info("MailService: temp emails start");
        $temp_emails   = [];
        //$temp_emails[] = array('email' => 'david.ginn@greenlight-community.com');
        //$temp_emails[] = array('email' => 'willow.storm@greenlight-community.com');
        // $temp_emails[] = array('email' => $this->userService->user()->email);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.title_search', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        Log::info("MailService: emailsAmService start");
        $email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);
        foreach ($email_list as $key => $value) {
            if (empty($value['email'])) continue;

            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.title_search', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendTitleSearchMail Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendTitleSearchMail Email could not be sent.");
        }
        return false;
    }

    function sendInsuranceQuoteMail($info) {

        if (empty($info['house_id'])) return false;

        Log::info("MailService: sendInsuranceQuoteMail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        $user        = $this->userService->user();
        $addressInfo = $this->propertyService->findOneById($info['house_id'], true);

        $name    = CommonHelper::nameFormat($user);
        $address = CommonHelper::addressFormat($addressInfo);
        $mapType = CommonHelper::mapType($addressInfo);
        $subject = __("emails_messages.insurance_quote", array("name"    => $name,
                                                               "address" => $address));

        // Variable initialise
        $data                = array();
        $info['map_type']    = $mapType;
        $info['first_name']  = $name;
        $info['email']       = $user->email;
        $info['address']     = $address;
        $link                = $this->houseTokenService->address_url($info['house_id'], $addressInfo);
        $info['address_url'] = $link;
        $data['info']        = $info;
        $data['footer']      = self::$footer;
        $data['subject']     = self::subjectEnvironmentModify($subject);

        // Send email to Email_settings
        Log::info("MailService: Email_settings start");
        $county              = $addressInfo->county;
        $emailSettings       = EmailSettingsModel::where('email_type', 'get_insurance_quote')->where(function ($query) use ($county) {
            $query->where('county', 'all');
            if (!empty($county)) $query->orWhere('county', $county);
        });
        $emailSettingsEmails = $emailSettings->get();

        foreach ($emailSettingsEmails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.insurance_quote', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        Log::info("MailService: temp emails start");
        $temp_emails   = [];
        //$temp_emails[] = array('email' => 'david.ginn@greenlight-community.com');
        //$temp_emails[] = array('email' => 'willow.storm@greenlight-community.com');
        // $temp_emails[] = array('email' => $this->userService->user()->email);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.insurance_quote', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        Log::info("MailService: emailsAmService start");
        $email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);
        foreach ($email_list as $key => $value) {
            if (empty($value['email'])) continue;

            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.insurance_quote', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });

        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendInsuranceQuoteMail Could not send message to");
        }

        if (empty($result)) {
           // Log::error("MailService: sendInsuranceQuoteMail Email could not be sent.");
        }
        return false;
    }

    function sendPictureMail($info) {


        Log::info("MailService: sendPictureMail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        $user    = $this->userService->user();
        $name    = CommonHelper::nameFormat($user);
        $subject = __("emails_messages.get_a_picture", array("name" => $name));

        // Variable initialise
        $data               = array();
        $info['first_name'] = $name;
        $info['email']      = $user->email;

        $data['info']    = $info;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        // Send email to Email_settings
        /*Log::info("MailService: Email_settings start");
        $county              = $addressInfo->county;
        $emailSettings       = EmailSettingsModel::where('email_type', 'get_a_picture')->where(function ($query) use ($county) {
            $query->where('county', 'all');
            if (!empty($county)) $query->orWhere('county', $county);
        });
        $emailSettingsEmails = $emailSettings->get();

        foreach ($emailSettingsEmails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.insurance_quote', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to($data['email'])->subject($data['subject']);
        self::toBcc($message);
            });

        }
        */

        Log::info("MailService: temp emails start");
        $temp_emails   = [];
        //$temp_emails[] = array('email' => 'david.ginn@greenlight-community.com');
        //$temp_emails[] = array('email' => 'willow.storm@greenlight-community.com');
        // $temp_emails[] = array('email' => $this->userService->user()->email);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.get_a_picture', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        /*Log::info("MailService: emailsAmService start");
        $email_list = $this->emailsAmService->findAllByHouseId($info['house_id']);
        foreach ($email_list as $key => $value)
        {
            if(empty($value['email']))
                continue;

            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.get_a_picture', $data, function ($message) use ($data)
            {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
        self::toBcc($message);
            });

        }
        */

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendPictureMail Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendPictureMail Email could not be sent.");
        }
        return false;
    }

    function sendHouseLinkEmail($info) {

        Log::info("MailService: sendHouseLinkEmail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();


        $houseAll = $this->propertyService->findMany(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $tr_html = '';
        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);

            $tr_html .= '<tr>' . '<td >' . ' <a  target="_blank" href="' . $address_url . '">' . $address_url . '</a>' . ' </td>' . '</tr>';
        }

        $subject = __("emails_messages.email_houses_link");

        // Variable initialise
        $data            = array();
        $data['tr_html'] = $tr_html;
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        Log::info("MailService: Email list");
        $temp_emails   = [];
        $temp_emails[] = array('email' => $info['to']);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.email_house_link', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                self::toBcc($message);
            });
        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendHouseLinkEmail Could not send message to");
        }

        if (empty($result)) {
           // Log::error("MailService: sendHouseLinkEmail Email could not be sent.");
        }
        return false;
    }

    function sendHouse40DetailsEmail($info) {

        Log::info("MailService: sendHouse40DetailsEmail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        // Common Code start
        $pdf_binary = $this->urlPdfCsvExcelService->house40DetailsPdf($info, true);
        $store_file_name = "ES_" . time() . $this->userService->user_id() . '.' . '.pdf';
        //          $store_file_name = "ES_" .$this->userService->user_id() . '.' . '.pdf';
        //         $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        //        echo $html;die;
        //         die;
        // Common Code End

        $subject = $info['subject'];
        if(empty($subject))
        $subject = __("emails_messages.email_house_pdf");

        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['bin_pdf'] = $pdf_binary;
        $data['file_name'] = $store_file_name;

        Log::info("MailService: Email list");
        $temp_emails   = [];
        $temp_emails[] = array('email' => $info['to']);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.email_house_pdf', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                $message->attachData($data['bin_pdf'], $data['file_name'], []);
                self::toBcc($message);
            });
        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendHouse40DetailsEmail Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendHouse40DetailsEmail Email could not be sent.");
        }
        return false;
    }

    function sendHouseAcquistionInfoEmail($info) {

        Log::info("MailService: sendHouse40DetailsEmail called");
        if (empty($info)) return false;
        // Signature modify
        self::signature();

        // Common Code start
        $pdf_binary = $this->urlPdfCsvExcelService->houseAcquistionInfoPdf($info, true);
        $store_file_name = "ES_" . time() . $this->userService->user_id() . '.' . '.pdf';

        $subject = $info['subject'];
        if(empty($subject))
            $subject = __("emails_messages.email_acquistion_info_pdf");

        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['bin_pdf'] = $pdf_binary;
        $data['file_name'] = $store_file_name;

        Log::info("MailService: Email list");
        $temp_emails   = [];
        $temp_emails[] = array('email' => $info['to']);

        foreach ($temp_emails as $key => $value) {
            $email         = $value['email'];
            $data['email'] = $email;

            $result = Mail::send('emails.email_acquistion_info_pdf', $data, function ($message) use ($data) {
                $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
                $message->to(self::toModify($data['email']))->subject($data['subject']);
                $message->attachData($data['bin_pdf'], $data['file_name'], []);
                self::toBcc($message);
            });
        }

        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: sendHouse40DetailsEmail Could not send message to");
        }

        if (empty($result)) {
            //Log::error("MailService: sendHouse40DetailsEmail Email could not be sent.");
        }
        return false;
    }


    // Modify below code...

    function emailVerification($user_id) {
        if (empty($user_id)) return false;

        Log::info("MailService: email verification called");

        // Signature modify
        self::signature();

        $subject            = __("emails_messages.email_verification");
        $user               = \App\Models\User::find($user_id);
        $email_verification = \App\Models\EmailVerificationsModel::where('user_id', $user_id)->first();

        // Variable initialise
        $data                      = array();
        $data['user']              = $user;
        $data['footer']            = self::$footer;
        $data['subject']           = self::subjectEnvironmentModify($subject);
        $data['verification_code'] = '<p>' . $user->first_name . ' has registered on ' . env("FROM_NAME") . '</p>
                
                <p><strong>To activate his/her Greenlight Property Finder Account simply click the button below:</strong></p>
                    <a href="' . env("APP_FRONTEND") . 'aev/' . $email_verification->vcode . '" style="border:1px solid #477589; background:#018cca; padding:5px 10px; margin-right:10px; color:#fff; font-size:14px; font-weight:600; text-decoration:none;">Activate</a>';


        $result = Mail::send('emails.emailverification', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            //$message->to($user->email)->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $user->email);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $user->email);
        }
        return false;
    }

    function changeEmailVerificationNotification($user_id) {
        if (empty($user_id)) return false;

        Log::info("MailService: email verification called");

        // Signature modify
        self::signature();

        $subject            = __("emails_messages.email_verification");
        $user               = \App\Models\User::find($user_id);
        $email_verification = \App\Models\EmailVerificationsModel::where('user_id', $user_id)->first();


        // Variable initialise

        $data                 = array();
        $data['user']         = $user;
        $data['footer']       = self::$footer;
        $data['subject']      = self::subjectEnvironmentModify($subject);
        $data['notification'] = '<span style="font-size: 14px;">Dear ' . $user->first_name . ' ' . $user->last_name . ',</span>
                                    <p><strong>Your email has been changed recently to ' . $user->email . '.</strong></p>';


        $result = Mail::send('emails.welcome', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            // $message->to($user->email)->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $user->email);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $user->email);
        }
        return false;
    }

    function changeEmailVerification($user_id) {
        if (empty($user_id)) return false;

        Log::info("MailService: email verification called");

        // Signature modify
        self::signature();

        $subject            = __("emails_messages.email_verification");
        $user               = \App\Models\User::find($user_id);
        $email_verification = \App\Models\EmailVerificationsModel::where('user_id', $user_id)->first();


        // Variable initialise

        $data                      = array();
        $data['user']              = $user;
        $data['footer']            = self::$footer;
        $data['subject']           = self::subjectEnvironmentModify($subject);
        $data['verification_code'] = '<p><strong>You modify your email if recently, To activate your ' . env("FROM_NAME") . ' Account simply click the button below:</strong></p>
            <a href="' . env("APP_FRONTEND") . 'aev/' . $email_verification->vcode . '" style="border:1px solid #477589; background:#018cca; padding:5px 10px; margin-right:10px; color:#fff; font-size:14px; font-weight:600; text-decoration:none;">Activate</a>';


        $result = Mail::send('emails.emailverification', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            //$message->to($user->email)->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $user->email);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $user->email);
        }
        return false;
    }

    function propertyClosedAlerts($email, $info) {
        if (empty($info['house_id']) || empty($email)) return false;

        Log::info("MailService: Property Closed Alert called");

        // Signature modify
        self::signature();
        $subject = __("emails_messages.property_closed_alerts");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<p><strong>Hello Sale Buyer,</strong></p>
                <p><strong>The property located at ' . $info['address'] . ' ' . $info['city'] . ' ' . $info['county'] . ' ' . $info['state'] . ' has been closed. </strong></p>
        
                <p><a href="' . env("FROM_NAME") . "home/showdetails/" . $info['house_id'] . '" target="_blank">' . env("FROM_NAME") . "home/showdetails/" . $info['house_id'] . '</a></p>';


        $result = Mail::send('emails.propertyclosedalert', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function homeBuyerAlert($email, $info) {
        if (empty($info['house_id']) || empty($email)) return false;


        Log::info("MailService: Home Buyer Alert called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.homebuyer_alert");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<p><strong>Hello Sale Buyer,</strong></p>
        <p><strong>The property located at ' . $info['address'] . ' ' . $info['city'] . ' ' . $info['county'] . ' ' . $info['state'] . ' Needs to be sold ASAP. You have ' . $info['date_diff'] . ' days left to meet or exceed your goal. Please take a look at what you are doing on this property (<a href="' . env("FROM_NAME") . 'home/showdetails/' . $info['house_id'] . '" target="_blank">Link Here</a>) , and evaluate what it will take, to get it sold. If you need to make changes to get it sold then please do so.</strong></p>';


        $result = Mail::send('emails.homebuyeralert', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function homeBuyerCreatedMail($email, $info) {
        if (empty($info['house_id']) || empty($email)) return false;

        Log::info("MailService: Home Buyer Create Mail called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.homebuyer_alert");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">' . $info['pos_message'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: ' . $info['first_name'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; ">
                <span style="font-size: 14px;">Requested Deposit Amount: ' . $info['deposit_amount'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Renovation Costs: ' . $info['renovation_amount'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Max Purchase Price: ' . $info['purchase_amount'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Total Funding Request: ' . $info['total_amount'] . '</span></h2>';


        $result = Mail::send('emails.homebuyeralert', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function agreementPopUpAddendum($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: email verification called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.agreement_pop_up_addendum");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">' . $info['top_message'] . '</span></h2>

        <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>';


        $result = Mail::send('emails.agreement_pop_up_addendum', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function areaInvite($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: Area Invite called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.area_invite");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<p><strong>Hello,</strong></p>

                        <p>' . $info["message"] . '</p>

                        <p>Below is the property link</p>

                        <p><a href="' . $info["link"] . '" target="_blank">' . $info["link"] . '</a></p>';


        $result = Mail::send('emails.area_invite', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function alarmReminder($info) {
        if (empty($info)) return false;


        Log::info("MailService: Alarm Reminder called");
        $user        = $this->userService->findOneById($info->user_id);
        $addressInfo = $this->propertyService->getLastSaleDatePropertyDetail($info->house_id);

        $address = CommonHelper::addressFormat($addressInfo);
        // Signature modify
        self::signature();

        $subject = __("emails_messages.alarm_reminder").' - '.$addressInfo->county.' '.date('m/d/Y',strtotime($addressInfo->last_sale_details->sale_date)).' - '. $address ;


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $user->email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $info['address_url'] = $this->houseTokenService->address_url($info->house_id, $addressInfo);
        $info['top_message']='';
        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">' . $info['top_message'] . '</span></h2>

            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>';

        $result = Mail::send('emails.alarm_reminder', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function depositFunder($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: Deposit Funder called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.deposit_funder");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;"> ' . $info['pos_message'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">' . $info['top_message'] . '</span></h2>

            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: ' . $info['first_name'] . '</span></h2>';


        $result = Mail::send('emails.deposit_funder', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function dpbCreatedEmail($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: dpbCreatedEmail called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.dpb_created_email");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        $data['content'] = '<p>Hello Distressed Property buyer,</p>
            <p><strong>Your The Estates LLC login details are as follows</strong></p>
            
            <p><strong>Email</strong> : <?php echo $info["to"];?></p>
            
            <p><strong>Password</strong> : <?php echo $info["password"];?></p>';


        $result = Mail::send('emails.dpb_created_email', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
           // Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function errorTemplate($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: errorTemplate called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.error_template");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);

        $data['content'] = '<p><strong>Hello ,</strong></p>

            <p>Following error are we getting on ES:</p>
            <p>' . $info['message'] . '</p>';


        $result = Mail::send('emails.error_template', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function fundDeal($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: FunDeal called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.fund_deal");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: ' . $info['first_name'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; ">
                <span style="font-size: 14px;">Requested Deposit Amount: ' . $info['deposit_amount'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Renovation Costs: ' . $info['renovation_amount'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Max Purchase Price: ' . $info['purchase_amount'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Total Funding Request: ' . $info['total_amount'] . '</span></h2>';


        $result = Mail::send('emails.fund_deal', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function fundedEmailWb($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: fundedEmailWb called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.funded_email_wb");


        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        //        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">{pos_message}</span></h2>
        //            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: '.$info['address_url'].'</span></h2>
        //            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; ">
        //                <span style="font-size: 14px;">Requested Deposit Amount: '.$info['deposit_amount'].'</span></h2>
        //            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Renovation Costs: '.$info['renovation_amount'].'</span></h2>
        //            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Max Purchase Price: '.$info['purchase_amount'].'</span></h2>
        //            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Total Funding Request: '.$info['total_amount'].'</span></h2>';


        $result = Mail::send('emails.funded_email_wb', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function getPicture($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: getPicture called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.funded_email_wb");

        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: ' . $info['first_name'] . '</span></h2>';


        $result = Mail::send('emails.funded_email_wb', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function insuranceQuote($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: getPicture called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.insurance_quote");

        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Property Address: <a target="_blank" style="text-decoration: none;" href=' . $info['map_type'] . '>{address}</a></span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">First Name: ' . $info['first_name'] . '</span></h2>
             <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Email: ' . $info['email'] . '</span></h2>';


        $result = Mail::send('emails.insurance_quote', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function propertyPassonByAm($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: getPicture called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.property_passon_by_am");

        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<p><strong>Hello ' . $info['name'] . ',</strong></p>

            <p>Your Buy It Request on this property has been removed by your Acquisition Manager as per your request. If you think, this is wrong then please contact your Acquisition Manager directly.
                </p>
            <p>Thank you! Have a good day!</p>

            <p>Below is the property link</p>
            <h2 style="text-align: left; color: rgb(51, 51, 51); font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; "><span style="font-size: 14px;">Link: ' . $info['address_url'] . '</span></h2>';


        $result = Mail::send('emails.property_passon_by_am', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            //Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function reAgreeEmail($email, $info) {
        if (empty($info)) return false;


        Log::info("MailService: getPicture called");

        // Signature modify
        self::signature();

        $subject = __("emails_messages.reagree_email");

        // Variable initialise
        $data            = array();
        $data['footer']  = self::$footer;
        $data['email']   = $email;
        $data['subject'] = self::subjectEnvironmentModify($subject);
        $data['content'] = '<p><strong>Hello ' . $info['name'] . ',</strong></p>

            <p>Thank you for approving the New User License Agreement. Attached is your approved copy for your records.</p>';


        $result = Mail::send('emails.reagree_email', $data, function ($message) use ($data) {

            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to(self::toModify($data['email']))->subject($data['subject']);
            self::toBcc($message);
        });


        // Laravel tells us exactly what email addresses failed, let's send back the first
        $fail = Mail::failures();
        if (!empty($fail)) {
            Log::error("MailService: Could not send message to" . $data['email']);
        }

        if (empty($result)) {
            ///Log::error("MailService: Email could not be sent." . $data['email']);
        }
        return false;
    }

    function sendW9Email($data){

        self::signature();
        $data['footer']  = self::$footer;
        
        Mail::send('emails.w9', $data, function ($message)  use ($data){
            $message->attach($data['outputPath']);
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to($data['email'])->subject($data['subject']);
        });
    }

    function sendMcdEmailToUser($data){
        self::signature();
        $data['footer']  = self::$footer;
        
        Mail::send('emails.mcd_email', $data, function ($message)  use ($data){
            $message->from(env("FROM_EMAIL"), env("FROM_NAME"));
            $message->to($data['email'])->subject($data['subject']);
        });
    }
}
