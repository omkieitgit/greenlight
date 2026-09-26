<?php

namespace App\Http\Controllers;

use Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ConfigController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        Log::info("ConfigController: __construct called");
        $this->request = $request;

    }
    public function propertyConfig()
    {
        Log::info("ConfigController: propertyConfig called");

        # TODO: change config to load dynamically don't need to add into bootstraps/app.php
        return response()->json([
            'row' =>  config('property_information') // config('property_information')
        ], 200);

    }


    public function countyUrl()
    {
        Log::info("PublicController: countyUrl called");
        // Return Role list from table
        return response()->json([
            'row' =>  config('county_url')
        ], 200);

    }

}
