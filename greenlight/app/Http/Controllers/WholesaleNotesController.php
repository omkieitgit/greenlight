<?php
/**
 * Created By Rativardhan Singh Sengar  5/26/19 10:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/26/19 9:57 PM
 */

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Services\MailService;
use App\Services\UserService;
use App\Services\WholesaleNotesService;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class WholesaleNotesController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $wholesaleNotesService;
    private $userService;

    public function __construct(Request $request, WholesaleNotesService $wholesaleNotesService, UserService $userService) {
        Log::info("WholesaleNotesController: __construct called");
        $this->request               = $request;
        $this->wholesaleNotesService = $wholesaleNotesService;
        $this->userService           = $userService;

    }

    public function index($house_id) {

        Log::info("WholesaleNotesController: index called");

        if ($this->userService->is_wholesale_buyer()) {
            $data = $this->wholesaleNotesService->myList($house_id);
        }
        else {
            $data = $this->wholesaleNotesService->list($house_id);
        }

        return response()->json(['status'  => 'success',
                                 'message' => ".",
                                 "data"    => $data], 200);
    }


    public function create($house_id) {

        Log::info("WholesaleNotesController: create called");

        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
                  'notes'     => 'required',];

        $all             = $this->request->all();
        $all['house_id'] = $house_id;

        $validator = Validator::make($all, $rules);
        $validator->validate();

        $info=$this->wholesaleNotesService->create($all);

        return response()->json(['status'  => 'success',
                                  'data'    =>$info,
                                 'message' => "Notes added successfully."], 200);
    }
}