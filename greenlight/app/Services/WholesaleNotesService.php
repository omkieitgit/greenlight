<?php
/**
 * Created By Rativardhan Singh Sengar  06/01/19 11:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 06/01/19 11:24 PM
 */

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\WholesaleNotesModel;
use Log;

class WholesaleNotesService {
    private $findOneById;
    private $userService;


    public function __construct(UserService $userService) {
        Log::info("WholesaleNotesService: __construct called");
        $this->userService = $userService;
    }


    public function findOneById($id) {
        Log::info("WholesaleNotesService: findOneById called");
        $this->findOneById = WholesaleNotesModel::find($id);
        return $this->findOneById;
    }

    public function myList($house_id) {
        Log::info("WholesaleNotesService: myList called");
        return WholesaleNotesModel::where(['user_id'  => $this->userService->user_id(),
                                           "house_id" => $house_id])
                                  ->with(['user' => function ($query) {
                                      $query->select(["users.id",
                                                      "users.email",
                                                      "users.first_name",
                                                      "users.last_name"]);
                                  }])->get(['wholesale_notes_id',
                                            'house_id',
                                            'user_id',
                                            'notes',
                                            'created_at',
                                            'updated_at']);
    }

    public function list($house_id) {
        Log::info("WholesaleNotesService: myList called");
        return WholesaleNotesModel::where('house_id', $house_id)->with(['user' => function ($query) {
            $query->select(["users.id",
                            "users.email",
                            "users.first_name",
                            "users.last_name"]);
        }])->get(['wholesale_notes_id',
                  'house_id',
                  'user_id',
                  'notes',
                  'created_at',
                  'updated_at']);
    }

    public function create($info) {
        Log::info("WholesaleNotesService: create called");
        $info['user_id'] = $this->userService->user_id();

        $info['notes'] = $info['notes'];
        return WholesaleNotesModel::create($info);
    }

}
