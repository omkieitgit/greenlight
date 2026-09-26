<?php
/**
 * Created By Rativardhan Singh Sengar  8/27/19 10:14 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 8/27/19 10:13 PM
 */

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Models\BorrowerNotesModel;
use App\Services\UserService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ScraperAWSController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;

    public function __construct(Request $request
        , UserService $userService) {
        Log::info("ScraperAWSController: __construct called");
        $this->request               = $request;
        $this->userService           = $userService;

    }

    public function index() {

        Log::info("ScraperAWSController: index called");


        $endURl = $this->request->input('end_url');
        $scraper_url = $this->request->input('scraper_url');
        $scraper_name = $this->request->input('scraper_name');
        //$ip = $_SERVER['HTTP_CLIENT_IP'] ? $_SERVER['HTTP_CLIENT_IP'] : ($_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);


        $url1 = "http://18.222.41.99/".$endURl; // scraper/harScript
        $data= [];
        $data['scraper_url'] = $scraper_url;
        $data['scraper_name'] = $scraper_name;
        //$data['user_ip']    =$ip;
        $response = Http::asForm()->post($url1,$data);
        //        if($response->ok()) {
        //            //File exists
        //
        //        }
        //        else{
        //
        //        }
        return $response;

    }

}