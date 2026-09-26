<?php

namespace App\Services;

use App\Models\PropertyModel;
use App\Models\SaleDetailsModel;
use App\Models\AssessmentModel;

use Log;
use Illuminate\Support\Facades\DB;
class PropertyService {
    private $findOneById;
    private $findAllById;


    public function __construct() {
        Log::info("PropertyService: __construct called");
    }

    /**
     * @param $id
     * @param bool $is_cache
     * @return mixed
     */
    public function findPropertyInformation($id, $is_cache = false) {
        Log::info("PropertyService: findAll called");
        if ($is_cache == true) {
            return $this->findAllById;
        }

        $this->findAllById = PropertyModel::with(['assessment',
                                                  'local_real_estate_details',
                                                  //                'property_descriptions',
                                                  'price_history',
                                                  'schools_and_neighborhood',
                                                  'property_descriptions',
                                                  'document_property',
                                                  'last_cma_arv_recommendations',
                                                  'document_property.user',])->where('home_information.house_id', $id)->first();
        return $this->findAllById;
    }

    /**
     * Find property record
     * @param $id
     * @return mixed
     */
    public function findOneById($id, $is_cache = false) {

        Log::info("PropertyService: findOneById called");
//        if ($is_cache == true && !empty($this->findOneById[$id] )) {
//            return $this->findOneById[$id] ;
//        }

        return PropertyModel::find($id);

        // testing required
        //return $this->findOneById;
    }


    public function findMany($house_ids) {
        Log::info("PropertyService: findAllById called");
        return PropertyModel::findMany($house_ids);
    }
    public function findHoaDetails($house_ids) {
        Log::info("PropertyService: findHoaDetails called");

        $info = PropertyModel::select(["home_information.house_id"
            ## Needed this field, Important this line
        ]);

        //        $info->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id');
        //        $info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id');
        //        $info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id');
        //        $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        //        $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');

        $info->whereIn("home_information.house_id", $house_ids);
        $info->select(["home_information.address",
                        "home_information.city",
                        "home_information.county",
                        "home_information.state",
                        "home_information.zip",
                        "home_information.year_built",
                        'home_information.total_living_sqft',
            //            "home_information.total_living_sqft",
            //            "home_information.year_built",
            //            "home_information.bed",
            //            "home_information.bath",
            //            'home_information.lot_acreage_sf',
            //            'home_information.parcel_id1',
            //            'home_information.parcel_id2',
            //            'home_information.subdivision',
            //            
            //            'home_information.county_value',
            //            "property_descriptions.*",
            //            "local_real_estate.zestimate",
            "home_information.house_id"
            ## Needed this field, Important this line
        ]);
        $info->with([
            "last_owner_info" =>   function ($query) {
                $query->select(['house_id', 'full_name','full_address']);
            },
            "hoa_liens"  => function ($query) {
                $query->select(['house_id', 'hoa_name','hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_foreclosing'
                    ,'no_str','redemption_expires','tax_code','affidavit_date'
                ]);
            },
            // "tax_liens"=> function ($query) {$query->select(
            //     ['house_id','redemption_expires','tax_code']);
            // },
            "last_cma_arv_recommendations" => function ($query) {
                $query->select(['house_id',
                    'recommended_cma_arv']);
            },
            "first_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'est_equity',
                    'no_str_no_appt','amortization_loan_estimate_balance'
                ]);
            },
            "second_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount','est_equity',
                    'no_str_no_appt','amortization_loan_estimate_balance'
                ]);
            },
            "third_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount','est_equity',
                    'no_str_no_appt','amortization_loan_estimate_balance'
                ]);
            },
            "last_sale_details"=> function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'trustee',
                    'trustee_phone',
                    'trustee_file_no',
                    'sale_date',
                    'sale_time',
                    'case_number',
                    'priceint',
                    'opening_bid',
                    'sale_type',
                    'nos_by',
                    'nos_date']);
            },


          ]);
        $info->orderBy('home_information.county');


        return $info->get();
    }


    public function find40Details($house_ids) {
        Log::info("PropertyService: find40Details called");


        $info = PropertyModel::select(["home_information.house_id"
                                       ## Needed this field, Important this line
                                      ]);
        $info->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id');
        $info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id');
        $info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id');
        $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');

        $info->whereIn("home_information.house_id", $house_ids);
        $info->select(["home_information.address",
                       "home_information.city",
                       "home_information.county",
                       "home_information.state",
                       "home_information.zip",
                       "home_information.total_living_sqft",
                       "home_information.year_built",
                       "home_information.bed",
                       "home_information.bath",
                       'home_information.lot_acreage_sf',
                       'home_information.parcel_id1',
                       'home_information.parcel_id2',
                       'home_information.subdivision',
                       'home_information.total_living_sqft',
                       'home_information.county_value',
                       "property_descriptions.*",
                       "local_real_estate.zestimate",
                       "home_information.house_id"
                       ## Needed this field, Important this line
                      ]);
        $info->with(["geo",
                     "last_sale_details"            => function ($query) {
                         $query->select(['house_id',
                                         'sale_id',
                                         'trustee',
                                         'trustee_phone',
                                         'trustee_file_no',
                                         'sale_date',
                                         'sale_time',
                                         'case_number',
                                         'priceint',
                                         'opening_bid',
                                         'sale_type',
                                         'nos_by',
                                         'nos_date']);
                     },
                     "last_sale_details.nos"        => function ($query) {
                         $query->select(["users.id",
                                         "users.email",
                                         "users.first_name",
                                         "users.last_name"]);
                     },
                     "last_cma_arv_recommendations" => function ($query) {
                         $query->select(['house_id',
                                         'recommended_cma_arv',
                                         'rents_zestimate',
                                         'general_demand',
                                         'specific_demand']);
                     },

                     "first_dtc"                        => function ($query) {
                         $query->select(['house_id',
                                         'info_added_by',
                                         'user_id',
                                         'date']);
                     },
                     "first_dtc.user"                   => function ($query) {
                         $query->select(["users.id",
                                         "users.email",
                                         "users.first_name",
                                         "users.last_name"]);
                     },
                     "first_liens"                      => function ($query) {
                         $query->select(['house_id',
                                         'lien_amount',
                                         'date_recorded',
                                         'lien_foreclosing']);
                     },
                     'last_sale_details.last_bidder'    => function ($query) {
                         $query->select(['house_id',
                                         'sale_id',
                                         'min_amt_nxt_ub',
                                         'last_date_to_upset_bid',
                                         'name_upset_bidder as wining_bidder',
                                         'amount_of_bid as winning_bid',
                                         'im_by',
                                         'im_date']);
                     },
                     'last_sale_details.last_bidder.im' => function ($query) {
                         $query->select(["users.id",
                                         "users.email",
                                         "users.first_name",
                                         "users.last_name"]);
                     },
                     "wholesale_buyer_strategy"         => function ($query) {
                         $query->select(['house_id',
                                         'est_close_date_a_to_b']);
                     },

                     "front_picture" => function ($query) {
                         $query->select(['house_id',
                                         'org_name',
                                         'store_name']);
                     }]);

        return $info->get();
    }

    public function findAcquistionDetails($house_ids) {
        Log::info("PropertyService: findAcquistionDetails called");


        $info = PropertyModel::select(["home_information.house_id"
                                       ## Needed this field, Important this line
                                      ]);
        $info->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id');
        $info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id');
        $info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id');
        $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');

        $info->whereIn("home_information.house_id", $house_ids);
        $info->select(["home_information.address",
                       "home_information.city",
                       "home_information.county",
                       "home_information.state",
                       "home_information.zip",
                       "home_information.total_living_sqft",
                       "home_information.year_built",
                       "home_information.bed",
                       "home_information.bath",
                       'home_information.lot_acreage_sf',
                       'home_information.parcel_id1',
                       'home_information.parcel_id2',
                       'home_information.subdivision',
                       'home_information.total_living_sqft',
                       'home_information.county_value',
                       "property_descriptions.*",
                       "local_real_estate.zestimate",
                       "home_information.house_id"
                       ## Needed this field, Important this line
                      ]);
        $info->with(["geo",
                     "last_sale_details"            => function ($query) {
                         $query->select(['house_id',
                                         'sale_id',
                                         'trustee',
                                         'trustee_phone',
                                         'trustee_file_no',
                                         'sale_date',
                                         'sale_time',
                                         'case_number',
                                         'priceint',
                                         'opening_bid',
                                         'sale_type',
                                         'nos_by',
                                         'nos_date']);
                     },
                     "last_sale_details.nos"        => function ($query) {
                         $query->select(["users.id",
                                         "users.email",
                                         "users.first_name",
                                         "users.last_name"]);
                     },
                     "last_cma_arv_recommendations" => function ($query) {
                         $query->select(['house_id',
                                         'recommended_cma_arv',
                                         'rents_zestimate',
                                         'general_demand',
                                         'specific_demand']);
                     },

                     "first_dtc"                        => function ($query) {
                         $query->select(['house_id',
                                         'info_added_by',
                                         'user_id',
                                         'date']);
                     },
                     "first_dtc.user"                   => function ($query) {
                         $query->select(["users.id",
                                         "users.email",
                                         "users.first_name",
                                         "users.last_name"]);
                     },
                     "first_liens"                      => function ($query) {
                         $query->select(['house_id',
                                         'lender',
                                         'lien_amount',
                                         'date_recorded',
                                         'lien_foreclosing']);
                     },
                     "second_liens"                     => function ($query) {
                         $query->select(['house_id',
                                         'lender',
                                         'lien_amount',
                                         'date_recorded',
                                         'lien_foreclosing']);
                     },
                     "third_liens"                      => function ($query) {
                         $query->select(['house_id',
                                         'lender',
                                         'lien_amount',
                                         'date_recorded',
                                         'lien_foreclosing']);
                     },
                     "other_liens"                      => function ($query) {
                         $query->select(['house_id',
                                         'lender',
                                         'lien_amount',
                                         'date_recorded',
                                         // 'lien_foreclosing'
                                        ]);
                     },
                     'last_sale_details.last_bidder'    => function ($query) {
                         $query->select(['house_id',
                                         'sale_id',
                                         'min_amt_nxt_ub',
                                         'last_date_to_upset_bid',
                                         'name_upset_bidder as wining_bidder',
                                         'amount_of_bid as winning_bid',
                                         'deposit_upset',
                                         'im_by',
                                         'im_date']);
                     },
                     'last_sale_details.last_bidder.im' => function ($query) {
                         $query->select(["users.id",
                                         "users.email",
                                         "users.first_name",
                                         "users.last_name"]);
                     },
                     "wholesale_buyer_strategy"         => function ($query) {
                         $query->select(['house_id',
                                         'est_prp_rate_of_return',
                                         'act_prp_rate_of_return',
                                         'est_days_on_market',
                                         'est_close_date_a_to_b']);
                     },
                     "last_borrower_info"               => function ($query) {
                         $query->select(['id',
                                         'house_id',
                                         'full_name']);
                     },

                     "front_picture" => function ($query) {
                         $query->select(['house_id',
                                         'org_name',
                                         'store_name']);
                     }]);

        return $info->get();
    }

    public function findTexasAuctionDetails($house_ids) {
        Log::info("PropertyService: findTexasAuctionDetails called");


        $info = PropertyModel::select(["home_information.house_id"
            ## Needed this field, Important this line
        ]);

        $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        $info->whereIn("home_information.house_id", $house_ids);
        $info->select(["home_information.address",
            "home_information.city",
            "home_information.county",
            "home_information.state",
            "home_information.zip",
            "home_information.total_living_sqft",
            "home_information.year_built",
            "home_information.bed",
            "home_information.bath",
            'home_information.lot_acreage_sf',
            'home_information.parcel_id1',
            'home_information.parcel_id2',
            'home_information.subdivision',
            'home_information.county_value',
            "property_descriptions.*",
            "home_information.house_id",
            "home_information.property_type",
            "home_information.specific_property_type",
            "home_information.heating"

            ## Needed this field, Important this line
        ]);
        $info->with([
            "last_sale_details"            => function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'trustee',
                    'trustee_phone',
                    'trustee_file_no',
                    'sale_date',
                    'sale_time',
                    'sale_place',
                    'case_number',
                    'priceint',
                    'opening_bid',
                    'sale_type',
                    'nos_by',
                    'nos_date']);
            },
            'last_sale_details.last_bidder'    => function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'min_amt_nxt_ub',
                    'last_date_to_upset_bid',
                    'name_upset_bidder as wining_bidder',
                    'amount_of_bid as winning_bid',
                    'im_by',
                    'im_date']);
            },
            "last_cma_arv_recommendations" => function ($query) {
                $query->select(['house_id',
                    'recommended_cma_arv',
                    'rents_zestimate',
                    'general_demand',
                    'specific_demand']);
            },
            "first_liens"                      => function ($query) {
                $query->select(['house_id',
                    'lender',
                    'lien_amount',
                    'loan_type',
                    'date_recorded',
                    'lien_foreclosing','no_str_no_appt']);
            },
            "second_liens"                     => function ($query) {
                $query->select(['house_id',
                    'lender',
                    'lien_amount',
                    'loan_type',
                    'date_recorded',
                    'lien_foreclosing','no_str_no_appt']);
            },
            "third_liens"                      => function ($query) {
                $query->select(['house_id',
                    'lender',
                    'lien_amount',
                    'loan_type',
                    'date_recorded',
                    'lien_foreclosing','no_str_no_appt']);
            },
            "hoa_liens"                      => function ($query) {
                $query->select(['house_id','hoa_name',
                    'hoa_lien_amount',
                    'date_of_hoa_lien','no_str','total_debt'
                    ]);
            },

            "last_owner_info"               => function ($query) {
                $query->select(['id',
                    'house_id',
                    'full_name','full_address']);
            },
            
            "front_picture" => function ($query) {
                $query->select(['house_id',
                    'org_name',
                    'store_name']);
            }]);
            
        $info->orderBy('home_information.county');

        return $info->get();
    }


    public function findWholetailDetails($house_ids) {
        Log::info("PropertyService: findWholetailDetails called");


        $info = PropertyModel::select(["home_information.house_id"
                                       ## Needed this field, Important this line
                                      ]);
        $info->whereIn("home_information.house_id", $house_ids);
        $info->select(["home_information.address",
                       "home_information.city",
                       "home_information.county",
                       "home_information.state",
                       "home_information.zip",
                       "home_information.total_living_sqft",
                       "home_information.total_sqft",
                       "home_information.year_built",
                       "home_information.bed",
                       "home_information.bath",
                       'home_information.lot_acreage_sf',
                       'home_information.parcel_id1',
                       'home_information.parcel_id2',
                       'home_information.subdivision',
                       'home_information.county_value',
                       'home_information.main_floor_area',
                       'home_information.second_floor_area',
                       'home_information.third_floor_area',
                       'home_information.basement_area',
                       'home_information.garages',
                       "home_information.house_id"
                       ## Needed this field, Important this line
                      ]);
        $info->with([
                        "property_descriptions",
                        "assessment",
                        "first_liens"   => function ($query) {
                            $query->select([
                                'mortgage_id',
                                'house_id',
                                'lien_amount',
                                'date_recorded',
                            ]);
                        },
                        "second_liens"=> function ($query) {
                            $query->select([
                                               'mortgage_id',
                                               'house_id',
                                               'lien_amount',
                                               'date_recorded',
                                           ]);
                        },
                        "third_liens"=> function ($query) {
                            $query->select([
                                               'mortgage_id',
                                               'house_id',
                                               'lien_amount',
                                               'date_recorded',
                                           ]);
                        },
                        //"other_liens",
                        "local_real_estate_details"          => function ($query) {
                            $query->select(['house_id',
                                            'zestimate',
                                            'zillow_url',
                                            'realtor_url',
                                           ]);
                        },
                        "last_cma_arv_recommendations"       => function ($query) {
                            $query->select(['house_id',
                                            'recommended_cma_arv',
                                            'rents_zestimate',
                                            'general_demand',
                                            'specific_demand']);
                        },
                        "property_acquisition_a_to_b_second" => function ($query) {
                            $query->select(['house_id',
                                            'house_construction_est',
                                           ]);
                        },
                        "last_owner_info"                    => function ($query) {
                            $query->select(['id',
                                            'house_id',
                                            'full_name',
                                            'phone',
                                            'phone2'
                                           ]);
                        },

                        "front_picture" => function ($query) {
                            $query->select(['house_id',
                                            'org_name',
                                            'store_name']);
                        }]);

        return $info->get();
    }

    public function createProperty($propertyData) {
        Log::info("PropertyService: createProperty called");
        $property = PropertyModel::create($propertyData);
        return $property->house_id;

    }

    public function updateProperty($id, $updateData) {
        Log::info("PropertyService: updateProperty called");

        $info = $this->findOneById($id, true);

        return $info->update($updateData);

    }


    public function quickViewExport($house_ids) {
        Log::info("PropertyService: find40Details called");
        
        $info=PropertyModel::
                select(["home_information.house_id",'zpid','address','city','county','state','zip','total_living_sqft'
                    , 'year_built','bed','bath','lot_acreage_sf','county_value',
                    'property_type','specific_property_type','parcel_id1'
                    ])->
                with(
                    ["last_sale_details"=> function($query) {
                        $query->select(['house_id','sale_id','sale_date','case_number','opening_bid',
                                        'sale_type','sale_status','sale_time','sale_place','priceint',
                                        'trustee'])->whereNotNull('sale_date')->orderBy('sale_id','desc');
                        },

                  "first_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']);},
                  "second_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']);},
                  "third_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']);},
                  "hoa_liens"=> function ($query) {$query->select(['house_id','hoa_lien_amount','date_of_hoa_lien']);},
                  "other_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']);},

                  "front_picture"=> function($query) {
                      $query->select(['house_id','org_name','store_name']);
                  },
                  "last_owner_info" =>   function ($query) {
                      $query->select(['house_id','full_name','full_address','phone']);
                  },

                  "last_cma_arv_recommendations"=> function($query) {
                      $query->select(['house_id','recommended_cma_arv','rents_zestimate']);
                  },

              ]);
              // "first_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']); },

        $info->whereIn("home_information.house_id", $house_ids);


        return $info->get();
    }

    public function redemptionExport($house_ids) {
        Log::info("PropertyService: redemptionExport called");

        $info=PropertyModel::select('home_information.house_id','zpid','address','city','county','state','zip', "property_descriptions.legal_description")
                ->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id')
                ->with(
                    ["last_sale_details"=> function($query) {
                        $query->select(['house_id','sale_id','sale_date','sale_type','sale_status','case_number','opening_bid','trustee'])
                        ->whereNotNull('sale_date')->orderBy('sale_id','desc');
                        },
                    "first_liens"  => function ($query) {
                        $query->select(['house_id',
                            'lien_amount',
                            'date_recorded']);
                    },
                    "second_liens" => function ($query) {
                        $query->select(['house_id',
                            'lien_amount',
                            'date_recorded']);
                    },
                    "third_liens" => function ($query) {
                        $query->select(['house_id',
                            'lien_amount',
                            'date_recorded']);
                    },
                  "hoa_liens"=> function ($query) {$query->select(
                    ['house_id',
                    'hoa_name',
                    'hoa_lien_amount',
                    'date_of_hoa_lien',
                    'trdeed_date',
                    'winning_bidder',
                    'winning_bid',
                    'redemption_expires',
                    'tax_code',
                    'affidavit_date']);
                  },
                  "last_cma_arv_recommendations" => function ($query) {
                    $query->select(['house_id','recommended_cma_arv']);
                  },
                  "tax_liens"=> function ($query) {$query->select(
                    ['house_id','trdeed_date','winning_bidder','winning_bid','redemption_expires','tax_code']);
                  },

                  "front_picture"=> function($query) {
                      $query->select(['house_id','org_name','store_name']);
                  },
                  "last_owner_info" =>   function ($query) {
                      $query->select(['house_id','full_name','full_address','phone']);
                  }

              ]);
              // "first_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']); },

        $info->whereIn("home_information.house_id", $house_ids);


        return $info->get();
    }

    
    public function bidderExportByName($request){
        $bidderName=$request['search_fields']['bidder_name'];
        $info=PropertyModel::select('home_information.house_id','zpid','home_information.address','home_information.city','home_information.county','home_information.state','home_information.zip');
              $info->leftJoin('sale_details','sale_details.house_id','=','home_information.house_id');  
              $info->leftJoin('sale_bidder','sale_bidder.sale_id','=','sale_details.sale_id');  
              $info->with(
                [
                    'last_sale_details2.bidders'      => function ($query) use($bidderName){
                        $query->select(['house_id',
                        'sale_id',
                        'bidder_id',
                        'address',
                        'name_upset_bidder',
                        'amount_of_bid',
                        'bid_date',
                        'last_date_to_upset_bid'])->where('name_upset_bidder','like',"%$bidderName%")
                        ;
                    }
          ]);
              $info->whereIn("home_information.house_id", $request['house_ids'])
              ->where('name_upset_bidder','like',"%$bidderName%")
              ->groupBy('sale_details.sale_id'); 
        return $info->get();
    }

    public function bidderExport($request){
        Log::info("PropertyService: bidderExport called");
      //  DB::enableQueryLog();
        $info=PropertyModel::select('house_id','zpid','address','city','county','state','zip');
        $info->with(["last_owner_info" =>   function ($query) {
            $query->select(['house_id', 'full_name','full_address','phone'],);
        }]);

        if(!empty($request['search_fields']['w_bids'])){
            $info->with(
                [
                    'last_sale_details2.bidders'      => function ($query) {
                        $query->select(['house_id',
                        'sale_id',
                        'bidder_id',
                        'address',
                        'name_upset_bidder',
                        'amount_of_bid',
                        'bid_date',
                        'last_date_to_upset_bid']);
                    }
          ]);
        }
        else if(!empty($request['search_fields']['bidder_name'])){
            $bidderName=$request['search_fields']['bidder_name'];
            $info->with(
                [
                    'last_sale_details2.bidders'=> function ($query) use($bidderName){
                        $query->select(['house_id',
                        'sale_id',
                        'bidder_id',
                        'address',
                        'name_upset_bidder',
                        'amount_of_bid',
                        'bid_date',
                        'last_date_to_upset_bid'])->where('name_upset_bidder',$bidderName);
                    }
          ]);
        }
        else{
            $info->with(
                ['last_sale_details.last_bidder'    => function ($query) {
                      $query->select(['house_id',
                                      'sale_id',
                                      'bidder_id',
                                      'address',
                                      'name_upset_bidder',
                                      'amount_of_bid',
                                      'bid_date',
                                      'last_date_to_upset_bid']);
                  }
          ]);
        }

        $info->whereIn("home_information.house_id", $request['house_ids']);

        return $info->get();
      //  dd(DB::getQueryLog());
    }

     /**
     * @param $address
     * @param bool $is_cache
     * @return object
     */
    function propertyAddress($address,$state="",$county="")
    {
        Log::info("ScraperService: propertyAddress called");

        $find_short_address = config('constants.find_short_address');
        $replace_detail_address = \config('constants.replace_detail_address');

        $full_replace_count1 = 0;
        $full_phrase = str_ireplace($find_short_address, $replace_detail_address, $address, $full_replace_count1);

        $small_replace_count2 = 0;
        $small_phrase = str_ireplace($replace_detail_address, $find_short_address, $address, $small_replace_count2);


        $propertyObj = PropertyModel::where(function($propertyObj) use ($address,$full_replace_count1, $full_phrase,$small_replace_count2,$small_phrase ) {
               
            $propertyObj->where('address', 'like', "$address%");
            if ($full_replace_count1 > 0) {
                $propertyObj->orWhere("address", "like", "$full_phrase%");
            }
            if ($small_replace_count2 > 0) {
                $propertyObj->orWhere("address", "like", "$small_phrase%");
            }

        });

        if (!empty($state)) {
            $propertyObj->where("state", $state);
        }
        if (!empty($county)) {
            $propertyObj->where("county", $county);
        }

        return $propertyObj;
    }

     /**
     * @param $address
     * @param bool $is_cache
     * @return object
     */
    public function subToExport($house_ids) {
        Log::info("PropertyService: subToExport called");
        $houseIds = is_array($house_ids) ? $house_ids : json_decode($house_ids,true);

        $info = PropertyModel::select(["home_information.house_id",
                        "home_information.address",
                        "home_information.city",
                        "home_information.county",
                        "home_information.state",
                        "home_information.zip"
            ## Needed this field, Important this line
        ]);

        //$info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        $info->whereIn("home_information.house_id", $houseIds);
        ;
        $info->with([
            "last_sale_details" => function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'sale_date',
                    'sale_time',
                    'sale_type']);
            },
            "last_cma_arv_recommendations" => function ($query) {
                $query->select(['house_id','recommended_cma_arv']);
            },
            "first_liens"  => function ($query) {
                $query->select(['house_id',
                    'lender',
                    'lien_amount',
                    'loan_type',
                    'date_recorded',
                    'no_str_no_appt']);
            },
            "second_liens" => function ($query) {
                $query->select(['house_id',
                    'lender',
                    'lien_amount',
                    'loan_type',
                    'date_recorded',
                    'no_str_no_appt']);
            },
            "third_liens" => function ($query) {
                $query->select(['house_id',
                    'lender',
                    'lien_amount',
                    'loan_type',
                    'date_recorded',
                    'no_str_no_appt']);
            },
            "hoa_liens" => function ($query) {
                $query->select(['house_id',
                    'hoa_lien_amount',
                    'date_of_hoa_lien',
                    'redemption_date',
                    'affidavit_date'
                    ]);
            },
            "tax_liens" => function ($query) {
                $query->select(['house_id',
                    'date_of_tax_lien',
                    'tax_name',
                    'redemption_date'
                    ]);
            }
            ]);

        return $info->get();
                
    }

    public function winningBidderExport($house_ids){

        $houseIds = is_array($house_ids) ? $house_ids : json_decode($house_ids,true);
        $info = PropertyModel::select(["home_information.house_id",
        "home_information.address",
        "home_information.county",
        "home_information.city",
        "home_information.zip",
        ## Needed this field, Important this line
        ]);

        $info->whereIn("home_information.house_id", $houseIds);
        $info->with([
            "last_sale_details"=> function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'sale_date',
                    'sale_time',
                    'sale_place',
                    'case_number',
                    'opening_bid',
                    'sale_type']);
            },
            'last_sale_details.last_bidder'    => function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'min_amt_nxt_ub',
                    'last_date_to_upset_bid',
                    'name_upset_bidder as wining_bidder',
                    'amount_of_bid as winning_bid',
                    'address',
                    'phone'
                    ]);
            },
            "last_sale_details.bidders"=>function($query){
                $query->selectRaw('sale_id, count(*) as bidderCount')->groupBy('sale_id');
            },
            "last_owner_info" =>   function ($query) {
                $query->select(['house_id', 'full_name','full_address','phone'],);
            }]);
            

            return $info->get();
    }

    public function findTexasAuctionDetails2($house_ids) {
        Log::info("PropertyService: findTexasAuctionDetails2 called");

        $info = PropertyModel::select(["home_information.house_id"
            ## Needed this field, Important this line
        ]);
        
        $info->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id');
        $info->whereIn("home_information.house_id", $house_ids);
        $info->select(["home_information.address",
                    "home_information.city",
                    "home_information.county",
                    "home_information.state",
                    "home_information.zip",
                    "home_information.year_built",
                    "home_information.property_type",
                    "home_information.specific_property_type",
                    "property_descriptions.*",
                    "home_information.house_id"
                    ## Needed this field, Important this line
        ]);
        
        $info->with([
            "last_sale_details"            => function ($query) {
                $query->select(['house_id',
                    'sale_id',
                    'trustee',
                    'sale_date',
                    'sale_type',
                    'sale_status','case_number','opening_bid','trustee'
                   ])->whereNotNull('sale_date')->orderBy('sale_id','desc');
            },
            "first_liens" => function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'amortization_loan_estimate_balance',
                    'est_late_payment_and_fees',
                    'total_est_debt',
                    'date_recorded',
                    'no_str_no_appt']);
            },
            "second_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'amortization_loan_estimate_balance',
                    'est_late_payment_and_fees',
                    'total_est_debt',
                    'date_recorded',
                    'no_str_no_appt']);
            },
            "third_liens"=> function ($query) {
                $query->select(['house_id',
                    'lien_amount',
                    'amortization_loan_estimate_balance',
                    'est_late_payment_and_fees',
                    'date_recorded',
                    'total_est_debt',
                    'no_str_no_appt']);
            },
            "hoa_liens" => function ($query) {
                $query->select( ['house_id',
                                'hoa_name',
                                'hoa_lien_amount',
                                'winning_bidder',
                                'winning_bid',
                                'redemption_expires',
                                ]);
            }, 
            "tax_liens"=> function ($query) {
                $query->select(['house_id','trdeed_date','winning_bidder','winning_bid','redemption_expires','tax_code']);
            },

            "last_owner_info" =>   function ($query) {
                $query->select(['house_id','full_name','full_address','phone']);
            },
            "local_real_estate_details"=> function ($query) {
                $query->select(['house_id',
                                'zestimate'
                               ]);
            }
            ]);

        $info->orderBy('home_information.county');

        return $info->get();
    }

    function get_merge_data($houseid){
        if (empty($houseid) || $houseid == 'undefined')
            return false;

        return PropertyModel::where('house_id',$houseid)->first()->toArray();
       
    }

    function get_merge2_delete_data($value) {

        if (empty($value) || $value == 'undefined')
            return false;

        return PropertyModel::whereIn('house_id',$value)->get()->toArray();
    }

    public function merge_property_records($house_ids)
    {
        return PropertyModel::whereIn('house_id',$house_ids)->update(['deleted_at'=>time()]);
    }

    public function getSumPropertyOwed($houseid){
        $info=PropertyModel::select(['home_information.house_id', DB::raw('sum(property_taxes_owed) as total_property_taxes_owed')])
         ->leftJoin('property_assessment', 'home_information.house_id', '=', 'property_assessment.house_id')
         ->where('home_information.house_id',$houseid)   
         ->groupBy('home_information.house_id')->first();
        if(!empty($info)){
            return $info->total_property_taxes_owed;
        }else{
            return 0;
        }
    }

    public function getSaleDocumentByHouseId($houseid){
        $info=SaleDetailsModel::select(['sale_details.house_id', DB::raw('count(document_sale.id) as sale_document')])
        ->leftJoin('document_sale', 'document_sale.sale_id', '=', 'sale_details.sale_id')
        ->where('sale_details.house_id',$houseid)  
        ->whereNull('document_sale.deleted_at') 
        ->groupBy('sale_details.house_id')
        ->first();
       if(!empty($info)){
           return $info->sale_document;
       }else{
           return 0;
       }
    }

    public function getLastSaleDatePropertyDetail($houseid){
       return  PropertyModel::select('home_information.house_id','zpid','address','city','county','state','zip')
                ->with(
                    ["last_sale_details"=> function($query) {
                        $query->select(['house_id','sale_id','sale_date','sale_type'])->whereNotNull('sale_date')->orderBy('sale_id','desc');
                    }])->where('home_information.house_id',$houseid)->first();


    }


    public function getAssessment($house_id){
        return AssessmentModel::where('house_id',$house_id)->get();
    }


    public function redemptionExcessFundExport($house_ids) {
        Log::info("PropertyService: redemptionExcessFundExport called");

        $info=PropertyModel::select('home_information.house_id','zpid','address','city','county','state','zip', "property_descriptions.legal_description","property_type","specific_property_type")
                ->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id')
                ->with(
                    ["last_sale_details"=> function($query) {
                        $query->select(['house_id','sale_id','sale_date','sale_type','sale_status','case_number','opening_bid','trustee'])
                        ->whereNotNull('sale_date')->orderBy('sale_id','desc');
                        },
                        'last_sale_details.last_bidder'    => function ($query) {
                            $query->select(['house_id',
                                            'sale_id',
                                            'name_upset_bidder as wining_bidder',
                                            'amount_of_bid as winning_bid'
                                            ]);
                        },
                    "first_liens"  => function ($query) {
                        $query->select(['house_id',
                            'lien_amount',
                            'amortization_loan_estimate_balance',
                            'est_late_payment_and_fees',
                            'total_est_debt',
                            'date_recorded']);
                    },
                    "second_liens" => function ($query) {
                        $query->select(['house_id',
                            'lien_amount',
                            'amortization_loan_estimate_balance',
                            'est_late_payment_and_fees',
                            'total_est_debt',
                            'date_recorded']);
                    },
                    "third_liens" => function ($query) {
                        $query->select(['house_id',
                            'lien_amount',
                            'amortization_loan_estimate_balance',
                            'est_late_payment_and_fees',
                            'total_est_debt',
                            'date_recorded']);
                    },
                  "hoa_liens"=> function ($query) {$query->select(
                    ['house_id',
                    'hoa_name',
                    'hoa_lien_amount',
                    'winning_bidder',
                    'winning_bid',
                    'redemption_expires',
                    ]);
                  },
                  "last_cma_arv_recommendations" => function ($query) {
                    $query->select(['house_id','recommended_cma_arv']);
                  },
                  "tax_liens"=> function ($query) {$query->select(
                    ['house_id','trdeed_date','winning_bidder','winning_bid','redemption_expires','tax_code']);
                  },

                  "front_picture"=> function($query) {
                      $query->select(['house_id','org_name','store_name']);
                  },
                  "last_owner_info" =>   function ($query) {
                      $query->select(['house_id','full_name','full_address','phone']);
                  }

              ]);
              // "first_liens"=> function ($query) {$query->select(['house_id','lien_amount','date_recorded']); },

        $info->whereIn("home_information.house_id", $house_ids);


        return $info->get();
    }
}
