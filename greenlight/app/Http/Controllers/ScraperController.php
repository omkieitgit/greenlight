<?php
/**
 * Created By Rativardhan Singh Sengar  5/26/19 10:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/26/19 9:57 PM
 */

namespace App\Http\Controllers;

use App\Services\MailService;
use App\Services\PropertyService;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ScraperController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $mailService;
    private $propertyService;

    public function __construct(
        Request $request
        , MailService $mailService
        , PropertyService $propertyService

    ) {
        Log::info("ScraperController: __construct called");
        $this->request                = $request;
        $this->mailService            = $mailService;
        $this->propertyService        = $propertyService;
    }

    public function brockAndScottScraper()
    {
        Log::info("brockAndScottScraper: called");

        $key = $this->request->input('key');
        if ($key != "ratiscraper_12") {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 400);
        }


        $response = Http::get('http://3.21.205.201/scraper/scraperjson/brockandscott');

        if($response->ok())
        {
            $json_response =  $response->json();

            if(!empty($json_response))
            {
                foreach ($json_response as $key=>$value)
                {
                    // check if address already exists in records or not
                    $address = $value['address'];

                    // remove county , city , state from address
                    $address = Str::of($address)->replaceLast($value['county'], '');
                    $address = Str::of($address)->replaceLast($value['city'], '');
                    $address = Str::of($address)->replaceLast(' '.$value['state'], '');



                    print_r($value);die;
                }
            }
            else{
                // Error log , something went wrong
            }

        }
        else
        {
            // Error log , something went wrong
        }

        //        var_dump($response->ok());
        //        var_dump($response->successful());
        //       // var_dump($response->failed());
        //        var_dump($response->serverError());
        //        var_dump($response->clientError());
        //        var_dump($response->status());
        //        var_dump($response->json());
        //        var_dump($response->body());
        //        echo '<pre>';
        //        var_dump($response);
        //die;
    }




}