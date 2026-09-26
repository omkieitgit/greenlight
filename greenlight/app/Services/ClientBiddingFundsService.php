<?php

namespace App\Services;

use App\Models\ClientBiddingFundsModel;
use Log;

class ClientBiddingFundsService {
    private $findOneById;
    private $findAllById;


    public function __construct() {
        Log::info("ClientBiddingFundsService: __construct called");
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findAll($house_id, $is_cache = false) {
        Log::info("ClientBiddingFundsService: findAll called");
        if ($is_cache == true) {
            return $this->findAllById;
        }

        $this->findAllById = ClientBiddingFundsModel::where('house_id', $house_id)->get();
        return $this->findAllById;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id, $is_cache = false) {
        Log::info("ClientBiddingFundsService: findOneById called");
        if ($is_cache == true) {
            return $this->findOneById;
        }
        $this->findOneById = ClientBiddingFundsModel::find($id);
        return $this->findOneById;
    }


    public function updateOrCreate( $info){
        Log::info("ClientBiddingFundsService: updateOrCreate called");

        $response =  ClientBiddingFundsModel::updateOrCreate(["id"=>$info['id']],$info);
        if($response->id === 0)
        {
            $response = $this->findOneById($info['id']);
        }
        return $response;
    }

}
