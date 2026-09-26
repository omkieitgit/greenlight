<?php

namespace App\Services;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\ApiLogModel;
class ApiLogService
{
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct() {
        $this->model = new ApiLogModel();     
    }

    public function saveApiLog($data){

//        $data['created_at'] = time();
//        $data['updated_at'] = time();

        $insert = ApiLogModel::create($data);
        return $insert->id;

    }

    public function updateApiLog($data,$apiLogId){
        ApiLogModel::updateOrCreate(["id"=>$apiLogId],$data);
    }
    
}
