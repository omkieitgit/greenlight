<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/14/19 7:28 PM
 */

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\PropertyQueueListHousesModel;
use Log;

class PropertyQueueListHousesService
{
    private $findOneById;
    private $userService;

    /**
     * PropertyQueueListHousesService constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        Log::info("PropertyQueueListHousesService: __construct called");
        $this->userService = $userService;
    }


    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id)
    {
        Log::info("PropertyQueueListHousesService: findOneById called");
        $this->findOneById = PropertyQueueListHousesModel::find($id);
        return $this->findOneById;
    }

    /**
     * @param $where
     * @return mixed
     */
    public function list($where)
    {
        if(empty($where))
        {
            return false;
        }

        Log::info("PropertyQueueListHousesService: myList called");
        return PropertyQueueListHousesModel::
        select([
            'property_queue_list_houses.property_queue_list_houses_id', 'property_queue_list_houses.list_id',
            'property_queue_list_houses.house_id',
        ])
        ->leftJoin('home_information', 'home_information.house_id', '=', 'property_queue_list_houses.house_id')

            ->where($where)
            ->with(
                [
                    "house" => function($query) {
                          $query->select(['house_id','address','city','state','county','zip']);
                     },
                    "house.geo",


                    "house.last_sale_details" => function ($query) {
                        $query->select(['house_id', 'sale_id', 'trustee', 'sale_date', 'sale_time'
                                        , 'case_number', 'priceint', 'opening_bid', 'sale_type'
                                        , 'nos_by','nos_date'
                                       ]);
                    },
                    'house.last_sale_details.last_bidder'      => function ($query) {
                        $query->select(['house_id', 'sale_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                                        , 'name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                                        ,'im_by','im_date'
                                       ]);
                    },

                    "house.last_cma_arv_recommendations"       => function ($query) {
                        $query->select(['house_id', 'recommended_cma_arv', 'general_demand', 'specific_demand']);
                    },
                    "house.first_liens"                         => function ($query) {
                        $query->select(['house_id', 'lien_amount', 'date_recorded','lender', 'lien_foreclosing']);
                    },
                    "house.second_liens"                         => function ($query) {
                        $query->select(['house_id', 'lien_amount', 'date_recorded', 'lender', 'lien_foreclosing']);
                    },
                    "house.third_liens"                         => function ($query) {
                        $query->select(['house_id', 'lien_amount', 'date_recorded','lender',  'lien_foreclosing']);
                    },
                    "house.front_picture"                      => function ($query) {
                        $query->select(['house_id', 'org_name', 'store_name']);
                    },
                ]
            )
            ->whereNull('home_information.deleted_at')
            ->get();
    }

    /**
     * @param $property_queue_list_houses_ids_arr
     * @param $info
     * @return mixed
     */
    public function moveToList($property_queue_list_houses_ids_arr, $info)
    {

        return PropertyQueueListHousesModel::
        where('user_id',$this->userService->user_id())
            ->whereIn('property_queue_list_houses_id',$property_queue_list_houses_ids_arr)->update($info);

    }



    public function updateOrCreate($where , $propertyData){
        Log::info("PropertyQueueListHousesService: updateOrCreate called");

        if(empty($where))
            return false;

        $propertyData['user_id'] = $this->userService->user_id();
        return PropertyQueueListHousesModel::updateOrCreate($where, $propertyData);
    }

}
