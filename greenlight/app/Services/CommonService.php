<?php

namespace App\Services;

use App\Models\CommonModel;

class CommonService
{
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct($table) {
        $this->model = new CommonModel($table);
    }

    public function getInfo($where = array()){
        return $this->model->getInfo($where);
    }

    public function saveInfo($data, $where = array()){
    	return $this->model->saveInfo($data, $where);
    }

    public function getAllInfo($where = array(),$pagination = array()){
        return $this->model->getAllInfo($where);
    }

    public function deleteInfo($where = array()){
        return $this->model->deleteInfo($where);
    }
}
