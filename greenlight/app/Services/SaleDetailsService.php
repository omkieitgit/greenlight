<?php

namespace App\Services;

use App\Models\SaleDetailsModel;
use Log;

class SaleDetailsService {
    private $findOneById;
    private $findAllById;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct() {
        Log::info("SaleDetailsService: __construct called");
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findSaleDetailsInformation($house_id, $is_cache = false) {
        Log::info("SaleDetailsService: findAll called");
        if ($is_cache == true) {
            return $this->findAllById;
        }

        // Left Join Null issue of sale_id , creating problems.
        $this->findAllById = SaleDetailsModel
            ::select(['sale_details.*'
                      ,
                      'sale_details_descriptions.*'
                      ,
                      "sale_details.sale_id"
                     ])->with(
                [
                    'document_sale'
                    ,
                    'document_sale.user'           => function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    }
                    ,
                    'bidders'
                    ,
                    'bidders.document_bidder'
                    ,
                    'bidders.document_bidder.user' => function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    }

                    ,
                    'bidders.im'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'bidders.nos'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'bidders.im_checked'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'bidders.auction'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    }
                    ,
                    'nos'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },

                    'im'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'trustee_callers'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'auction'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'redemption'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'excess_fund'=> function ($query) {
                        $query->select(['id',
                                        'first_name',
                                        'last_name',
                                        'username']);
                    },
                    'sale_trustee_notes'
                ]
            )->leftJoin('sale_details_descriptions', function ($join) {
                $join->on('sale_details_descriptions.sale_id', '=', 'sale_details.sale_id');
            })->where('house_id', $house_id)->get();

        return $this->findAllById;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($sale_id, $is_cache = false) {
        Log::info("SaleDetailsService: findOneById called");
        if ($is_cache == true) {
            return $this->findOneById;
        }

        $this->findOneById = SaleDetailsModel::find($sale_id);
        return $this->findOneById;
    }

    public function create($propertyData) {
        Log::info("SaleDetailsService: create called");
        return SaleDetailsModel::create($propertyData);
    }

    public function update($id, $updateData) {
        Log::info("SaleDetailsService: update called");

        unset($updateData['house_id']);

        $info = $this->findOneById($id, true);
        return $info->update($updateData);

    }

}
