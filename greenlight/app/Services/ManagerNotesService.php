<?php
/**
 * Created By Rativardhan Singh Sengar  06/01/19 11:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 06/01/19 11:24 PM
 */

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\ManagerNotesModel;
use Log;

class ManagerNotesService {
    private $findOneById;
    private $userService;


    public function __construct(UserService $userService) {
        Log::info("ManagerNotesService: __construct called");
        $this->userService = $userService;
    }


    public function findOneById($id) {
        Log::info("ManagerNotesService: findOneById called");
        $this->findOneById = ManagerNotesModel::find($id);
        return $this->findOneById;
    }

    public function myList($house_id) {
        Log::info("ManagerNotesService: myList called");
        return ManagerNotesModel::where(['user_id'  => $this->userService->user_id(),
                                           "house_id" => $house_id])->with(
            [
                'user' => function ($query) {
                    $query->select(["users.id",
                                    "users.email",
                                    "users.first_name",
                                    "users.last_name"]);
                }
            ]
        )->get(['id',
                                                                           'house_id',
                                                                           'user_id',
                                                                           'note_type',
                                                                           'notes',
                                                                           'created_at',
                                                                           'updated_at']);
    }

    public function list($type,$house_id) {
        Log::info("ManagerNotesService: myList called");
        return ManagerNotesModel::where(["house_id"=>$house_id])
                                ->where('note_type', $type)->with(
        [
            'user' => function ($query) {
                $query->select(["users.id",
                                "users.email",
                                "users.first_name",
                                "users.last_name"]);
            }
        ]
        )->get();
    }

    public function create($info) {
        Log::info("ManagerNotesService: create called");
        $info['user_id'] = $this->userService->user_id();
        return ManagerNotesModel::create($info);
    }

}
