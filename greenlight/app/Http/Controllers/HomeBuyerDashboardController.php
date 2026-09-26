<?php
/**
 * Created By Rativardhan Singh Sengar  4/15/19 8:53 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/15/19 8:18 PM
 */

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;
use App\Models\HouseBuyItModel;
use App\Models\InviteModel;
use App\Services\HouseBuyItHistoryService;
use App\Services\HouseBuyItService;
use App\Services\HouseTokenService;
use App\Services\InviteService;
use App\Services\MailService;
use App\Services\PropertyService;
use App\Services\UserService;
use App\Services\WholesaleBuyerNService;
use Illuminate\Support\Facades\DB;
use Validator;
Use Log;
use Illuminate\Http\Request;

class HomeBuyerDashboardController extends Controller {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;
    private $houseTokenService;
    private $propertyService;
    private $mailService;
    private $houseBuyItService;
    private $inviteService;
    private $wholesaleBuyerNService;


    public function __construct(Request $request
        , UserService $userService
        , HouseTokenService $houseTokenService
        , PropertyService $propertyService
        , MailService $mailService
        , HouseBuyItService $houseBuyItService
        , InviteService $inviteService
        , WholesaleBuyerNService $wholesaleBuyerNService
    ) {
        Log::info("InviteController: __construct called");
        $this->request                = $request;
        $this->userService            = $userService;
        $this->houseTokenService      = $houseTokenService;
        $this->propertyService        = $propertyService;
        $this->mailService            = $mailService;
        $this->houseBuyItService      = $houseBuyItService;
        $this->inviteService          = $inviteService;
        $this->wholesaleBuyerNService = $wholesaleBuyerNService;

    }

    private function likeWhere($query, $field, $value) {
        if (empty($value))
            return $query;
        return $query->where($field, 'LIKE', "$value%");
    }

    private function where($query, $field, $value, $condition = "=") {
        if (empty($value))
            return $query;
        return $query->where($field, $condition, $value);
    }

    private function whereBetween($query, $field, $from, $to) {
        if (!empty($from))
            $query->where($field, '>=', $from);

        if (!empty($to))
            $query->where($field, '<=', $to);

        return $query;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function list() {

        Log::info("HomeBuyerDashboardController: list called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 100;
        if ($limit > 1000) $limit = 1000;
        $offset   = $this->request->get('offset') ? $this->request->get('offset') : 0;
        $address  = $this->request->get('address') ? $this->request->get('address') : $this->request->get('property_address');
        $city     = $this->request->get('city');
        $county   = $this->request->get('county');
        $state    = $this->request->get('state');
        $zip      = $this->request->get('zip');
        $mortgage = $this->request->get('mortgage');

        $sale_date_from = $this->request->get('sale_date_from');
        $sale_date_from = $sale_date_from == "null" ? '' : $sale_date_from;
        $sale_date_to   = $this->request->get('sale_date_to');
        $sale_date_to = $sale_date_to == "null" ? '' : $sale_date_to;

        $close_date_from = $this->request->get('close_date_from');
        $close_date_to   = $this->request->get('close_date_to');

        $year_built_from = $this->request->get('built_year_from');
        $year_built_to   = $this->request->get('built_year_to');

        $filter_type = $this->request->get('filter_type') ? $this->request->get('filter_type') : '';


        //amount_in
        $filter_type_array = explode(",", $filter_type);
        $or_where          = [];
        if (in_array('potential_buy', $filter_type_array)) {
            $or_where[] = ["potential_buy" => 1];
        }
        if (in_array('bid_offer_on_property', $filter_type_array)) {
            $or_where[] = ["bid_offer_on_property" => 1];

        }
        if (in_array('bidding_in_process', $filter_type_array)) {
            $or_where[] = ["bidding_in_process" => 1];

        }
        if (in_array('bid_offer_confirmed', $filter_type_array)) {
            $or_where[] = ["bid_offer_confirmed" => 1];

        }
        if (in_array('property_purchased_acq_a_to_b', $filter_type_array)) {
            $or_where[] = ["property_purchased_acq_a_to_b" => 1];

        }
        if (in_array('property_in_escrow', $filter_type_array)) {
            $or_where[] = ["property_in_escrow" => 1];

        }
        if (in_array('deposit_to_be_returned', $filter_type_array)) {
            $or_where[] = ["deposit_to_be_returned" => 1];

        }
        if (in_array('property_closed', $filter_type_array) || in_array('properties_closed_b_to_c', $filter_type_array)) {
            $or_where[] = ["property_closed" => 1];

        }
        if (in_array('passit', $filter_type_array)) {
            $or_where[] = ["request_type" => 'passit'];

        }
        if (in_array('buyit', $filter_type_array)) {
            $or_where[] = ["request_type" => 'buyit'];
        }


        $info = InviteModel::select([
                                        "home_information.house_id"
                                        ## Needed this field, Important this line
                                    ])
                           ->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id')
                           // ->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id')
                           // ->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id')
                           ->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id')
                           ->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id')
                           ->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id')
                           ->leftJoin('house_buyit', function ($join) {
                               $join->on('home_information.house_id', '=', 'house_buyit.house_id');
                               $join->where('house_buyit.user_id', '=', $this->userService->user_id());
                           });

        $info->where( function ($query) {
            $query->where('invitee_to', $this->userService->user_id());
            $query->orWhere( 'house_buyit.user_id', $this->userService->user_id());
        });
        // $this->where($info, 'invitee_to', $this->userService->user_id());
        $this->likeWhere($info, 'home_information.address', $address);
        $this->likeWhere($info, 'city', $city);
        $this->likeWhere($info, 'county', $county);
        $this->where($info, 'state', $state);
        $this->where($info, 'zip', $zip);
        $this->whereBetween($info, 'wholesale_buyer_strategy.est_close_date_b_to_c', $close_date_from, $close_date_to);
        $this->whereBetween($info, 'home_information.year_built', $year_built_from, $year_built_to);

        if (!empty($or_where)) {
            $info->where(function ($query) use ($or_where) {

                foreach ($or_where as $key => $value) {
                    $query->orWhere(key($value), '=', $value[key($value)]);
                }
            });
        }


        $isManyJoin = false;

        if (!empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');

            $info       = $this->whereBetween($info, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
            $isManyJoin = true;
        }

        //        if (!empty($mortgage)) {
        //            $info->leftJoin('mortgage_liens', 'home_information.house_id', '=', 'mortgage_liens.house_id');
        //            $info       = $this->where($info, 'mortgage_liens.loan_term', $mortgage);
        //            $isManyJoin = true;
        //        }

        $info->groupBy('home_information.house_id');

        if ($isManyJoin === false)
            $total = $info->count();
        else {
            $info->groupBy('home_information.house_id');
            $total = $info->get()->count();
        }

        $info->select(DB::raw("ANY_VALUE(house_buyit.position) as position"));
        $info->addSelect(DB::raw("ANY_VALUE(house_buyit.request_type) as request_type"));

        $info->addSelect([
                          "home_information.address",
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
                          // "property_descriptions.*",
                          // "local_real_estate.zestimate",
                          "home_information.house_id"
                          ## Needed this field, Important this line
                      ]);

        $info->with(

            [

                "house.geo",
                "house.property_descriptions",
                "house.local_real_estate_details",
                "house.last_sale_details"     => function ($query) {
                    $query->select(['house_id',
                                    'sale_id',
                                    'trustee',
                                    'sale_date',
                                    'sale_time',
                                    'case_number',
                                    'priceint',
                                    'opening_bid',
                                    'sale_type',
                                    'nos_by',
                                    'nos_date',
                                    'redemption_expires'
                                   ]);
                },
                "house.last_sale_details.nos" => function ($query) {
                    $query->select(["users.id",
                                    "users.email",
                                    "users.first_name",
                                    "users.last_name"]);
                },

                "house.last_cma_arv_recommendations"        => function ($query) {
                    $query->select(['house_id',
                                    'recommended_cma_arv',
                                    'general_demand',
                                    'specific_demand']);
                },
                "house.cma_arv_recommendations"             => function ($query) {
                    $query->select(['house_id',
                                    'info_added_by',
                                    'user_id',
                                    'date']);
                },
                "house.cma_arv_recommendations.user"        => function ($query) {
                    $query->select(["users.id",
                                    "users.email",
                                    "users.first_name",
                                    "users.last_name"]);
                },
                "house.first_liens"                         => function ($query) {
                    $query->select(['house_id',
                                    'lien_amount',
                                    'date_recorded',
                                    'lien_foreclosing'
                                    ,
                                    'no_str_no_appt'
                                   ]);
                },
                "house.second_liens"                        => function ($query) {
                    $query->select(['house_id',
                                    'lien_amount',
                                    'date_recorded',
                                    'lien_foreclosing'
                                    ,
                                    'no_str_no_appt'
                                   ]);
                },
                "house.third_liens"                         => function ($query) {
                    $query->select(['house_id',
                                    'lien_amount',
                                    'date_recorded',
                                    'lien_foreclosing'
                                    ,
                                    'no_str_no_appt'
                                   ]);
                },
                "house.hoa_liens"  => function ($query) {
                    $query->select(['house_id', 'hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_foreclosing'
                        ,'no_str'
                    ]);
                },
                'house.last_sale_details.last_bidder'       => function ($query) {
                    $query->select(['house_id',
                                    'sale_id',
                                    'bidder_id',
                                    'min_amt_nxt_ub',
                                    'last_date_to_upset_bid'
                                    ,
                                    'name_upset_bidder as wining_bidder',
                                    'amount_of_bid as winning_bid'
                                    ,
                                    'im_by',
                                    'im_date'
                                   ]);
                },
                'house.last_sale_details.last_bidder.im'    => function ($query) {
                    $query->select(["users.id",
                                    "users.email",
                                    "users.first_name",
                                    "users.last_name"]);
                },
                'house.last_sale_details.sale_descriptions' => function ($query) {
                    $query->select(["sale_id",
                                    "notice_of_foreclosure"]);
                },
                "house.last_borrower_info"                  => function ($query) {
                    $query->select(['house_id',
                                    'full_name']);
                }
                ,
                "house.last_owner_info"                     => function ($query) {
                    $query->select(['house_id',
                                    'full_name']);
                },

                "house.wholesale_buyer_strategy" => function ($query) {
                    $query->select(['house_id',
                                    'est_close_date_a_to_b']);
                },
                "house.front_picture"            => function ($query) {
                    $query->select(['house_id',
                                    'org_name',
                                    'store_name']);
                },
                "house.buy_it_1"=> function ($query) {
                    $query->select(["house_buyit_id"
                        ,"house_id"
                        ,"user_id"
                        ,"position"
                        ,"email"
                        ,"email"
                        ,"first_name","last_name"]);
                },
                "house.buy_it_2"=> function ($query) {
                    $query->select(["house_buyit_id"
                        ,"house_id"
                        ,"user_id"
                        ,"position"
                        ,"email"
                        ,"email"
                        ,"first_name","last_name"]);
                },
                "house.wholesale_buyer_n_total" => function ($query) {
                    $query->select('*');
                },


                ]
        );


        $info = $info->skip(intval($offset))->take(intval($limit))->get();


        if (empty($info)) {
            return response()->json(['status'  => 'success',
                                     'total'   => $total,
                                     'data'    => [],
                                     'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success',
                                 'total'  => $total,
                                 'data'   => $info], 200);
    }

    /**
     * To Get dashboard cont report.
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardCount() {
        Log::info("HomeBuyerDashboardController: list2 called");



        $temp = DB::query()->fromSub(function ($query) {

            $address  = $this->request->get('address') ? $this->request->get('address') : $this->request->get('property_address');
            $city     = $this->request->get('city');
            $county   = $this->request->get('county');
            $state    = $this->request->get('state');
            $zip      = $this->request->get('zip');
            //$mortgage = $this->request->get('mortgage');

            $sale_date_from = $this->request->get('sale_date_from');
            $sale_date_from = $sale_date_from == "null" ? '' : $sale_date_from;
            $sale_date_to   = $this->request->get('sale_date_to');
            $sale_date_to = $sale_date_to == "null" ? '' : $sale_date_to;

            $close_date_from = $this->request->get('close_date_from');
            $close_date_to   = $this->request->get('close_date_to');

            $year_built_from = $this->request->get('built_year_from');
            $year_built_to   = $this->request->get('built_year_to');

            $filter_type = $this->request->get('filter_type') ? $this->request->get('filter_type') : '';


            //amount_in
            $filter_type_array = explode(",", $filter_type);

            $or_where          = [];
            if (in_array('potential_buy', $filter_type_array)) {
                $or_where[] = ["potential_buy" => 1];
            }
            if (in_array('bid_offer_on_property', $filter_type_array)) {
                $or_where[] = ["bid_offer_on_property" => 1];

            }
            if (in_array('bidding_in_process', $filter_type_array)) {
                $or_where[] = ["bidding_in_process" => 1];

            }
            if (in_array('bid_offer_confirmed', $filter_type_array)) {
                $or_where[] = ["bid_offer_confirmed" => 1];

            }
            if (in_array('property_purchased_acq_a_to_b', $filter_type_array)) {
                $or_where[] = ["property_purchased_acq_a_to_b" => 1];

            }
            if (in_array('property_in_escrow', $filter_type_array)) {
                $or_where[] = ["property_in_escrow" => 1];

            }
            if (in_array('deposit_to_be_returned', $filter_type_array)) {
                $or_where[] = ["deposit_to_be_returned" => 1];

            }
            if (in_array('property_closed', $filter_type_array) || in_array('properties_closed_b_to_c', $filter_type_array)) {
                $or_where[] = ["property_closed" => 1];

            }
            if (in_array('passit', $filter_type_array)) {
                $or_where[] = ["request_type" => 'passit'];

            }
            if (in_array('buyit', $filter_type_array)) {
                $or_where[] = ["request_type" => 'buyit'];
            }


            $info = $query->from('invitations_info')->select([
                "home_information.house_id"
                ## Needed this field, Important this line
            ])
                ->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id')
                // ->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id')
                // ->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id')
                ->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id')
                ->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id')
                ->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id')
                ->leftJoin('house_buyit', function ($join) {
                    $join->on('home_information.house_id', '=', 'house_buyit.house_id');
                    $join->where('house_buyit.user_id', '=', $this->userService->user_id());
                });

//            $info->where( function ($query) {
//                $query->where('invitee_to', $this->userService->user_id());
//                $query->orWhere( 'house_buyit.user_id', $this->userService->user_id());
//            });
            $this->where($info, 'invitee_to', $this->userService->user_id());
            $this->likeWhere($info, 'home_information.address', $address);
            $this->likeWhere($info, 'city', $city);
            $this->likeWhere($info, 'county', $county);
            $this->where($info, 'state', $state);
            $this->where($info, 'zip', $zip);
            $this->whereBetween($info, 'wholesale_buyer_strategy.est_close_date_b_to_c', $close_date_from, $close_date_to);
            $this->whereBetween($info, 'home_information.year_built', $year_built_from, $year_built_to);

            if (!empty($or_where)) {

                $info->where(function ($query2) use ($or_where) {

                    foreach ($or_where as $key => $value) {
                        $query2->orWhere(key($value), '=', $value[key($value)]);
                    }
                });
            }


            $isManyJoin = false;

            if (!empty($sale_date_from) || !empty($sale_date_to)) {
                $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');

                $info       = $this->whereBetween($info, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
                $isManyJoin = true;
            }

            //            if (!empty($mortgage)) {
            //                $info->leftJoin('mortgage_liens', 'home_information.house_id', '=', 'mortgage_liens.house_id');
            //                $info       = $this->where($info, 'mortgage_liens.loan_term', $mortgage);
            //                $isManyJoin = true;
            //            }

            $info->groupBy('home_information.house_id');

            // $query->select(DB::raw("ANY_VALUE(house_buyit.position) as position"));
            // $query->addSelect(DB::raw("ANY_VALUE(house_buyit.request_type) as request_type"));
            $info->select('home_information.house_id');
            $info->addSelect(DB::raw("count(home_information.house_id) total_invite"));
            $info->addSelect(DB::raw("SUM(IF(house_buyit.request_type = 'buyit',1,0)) buyit"));
            $info->addSelect(DB::raw("SUM(IF(house_buyit.request_type = 'passit',1,0)) passit"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.potential_buy)  potential_buy"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.bid_offer_on_property) as bid_offer_on_property"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.bidding_in_process) as bidding_in_process"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.bid_offer_confirmed) as bid_offer_confirmed"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.property_purchased_acq_a_to_b) as property_purchased_acq_a_to_b"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.property_in_escrow) as property_in_escrow"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.deposit_to_be_returned) as deposit_to_be_returned"));
            $info->addSelect(DB::raw("SUM(wholesale_buyer_strategy.property_closed) as property_closed"));

        }, 'a');


        $temp->addSelect(DB::raw("SUM(IF(a.total_invite >0,1,0)) total_invite"));
        $temp->addSelect(DB::raw("SUM(IF(a.buyit >0,1,0)) buyit"));
        $temp->addSelect(DB::raw("SUM(IF(a.passit >0,1,0)) passit"));
        $temp->addSelect(DB::raw("SUM(IF(a.potential_buy >0,1,0)) potential_buy"));
        //$temp->addSelect(DB::raw("SUM(IF(a.bid_offer_on_property >0,1,0)) bid_offer_on_property"));
        //$temp->addSelect(DB::raw("SUM(IF(a.bidding_in_process >0,1,0)) bidding_in_process"));
        //$temp->addSelect(DB::raw("SUM(IF(a.bid_offer_confirmed >0,1,0)) bid_offer_confirmed"));
        $temp->addSelect(DB::raw("SUM(IF(a.property_purchased_acq_a_to_b >0,1,0)) property_purchased_acq_a_to_b"));
        //$temp->addSelect(DB::raw("SUM(IF(a.property_in_escrow >0,1,0)) property_in_escrow"));
        //$temp->addSelect(DB::raw("SUM(IF(a.deposit_to_be_returned >0,1,0)) deposit_to_be_returned"));
        $temp->addSelect(DB::raw("SUM(IF(a.property_closed >0,1,0)) property_closed"));

        $first = $temp->get()->first();
        return response()->json(['status' => 'success',
            'total'  => @$first->total_invite, //$temp[0]->total_invite,
            'data'   => $first], 200);

    }


    /**
     * To Get dashboard cont report.
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardCount2() {
        Log::info("HomeBuyerDashboardController: dashboardCount2 called");

        $totalInvite = $this->homebuyerInvite();
        $totalBuyIt = $this->homebuyerBuyit();
        $totalPassIt = $this->homebuyerPassit();
        $totalPotential = $this->homebuyerPotentialBuy();
        $totalPropertyPurchased = $this->homebuyerPropertyPurchasedAcqAtoB();
        $totalPropertyClosed = $this->homebuyerPropertyClosed();

        ## if filter value is not empty apply them .
        $totalBuyIt = $this->applyFilter($totalBuyIt, 'buyit');
        $totalPassIt = $this->applyFilter($totalPassIt, 'passit');
        $totalInvite = $this->applyFilter($totalInvite, 'invite');
        $totalPotential = $this->applyFilter($totalPotential, 'wholesale_buyer_strategy');
        $totalPropertyPurchased = $this->applyFilter($totalPropertyPurchased, 'wholesale_buyer_strategy');
        $totalPropertyClosed = $this->applyFilter($totalPropertyClosed, 'wholesale_buyer_strategy');

        $first = [
            "total_invite"=> $totalInvite->count(),
            "buyit"=> $totalBuyIt->count(),
            "passit"=> $totalPassIt->count(),
            //"potential_buy"=> $totalPotential->count(),
            "property_purchased_acq_a_to_b"=> $totalPropertyPurchased->count(),
            "property_closed"=> $totalPropertyClosed->count()
        ];

        return response()->json(['status' => 'success',
            'total'  => $first['total_invite'],
            'data'   => $first], 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function list2() {

        Log::info("HomeBuyerDashboardController: list2 called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 100;
        if ($limit > 1000) $limit = 1000;
        $offset   = $this->request->get('offset') ? $this->request->get('offset') : 0;
        $address  = $this->request->get('address') ? $this->request->get('address') : $this->request->get('property_address');
        $city     = $this->request->get('city');
        $county   = $this->request->get('county');
        $state    = $this->request->get('state');
        $zip      = $this->request->get('zip');
        //$mortgage = $this->request->get('mortgage');

        $sale_date_from = $this->request->get('sale_date_from');
        $sale_date_from = $sale_date_from == "null" ? '' : $sale_date_from;
        $sale_date_to   = $this->request->get('sale_date_to');
        $sale_date_to = $sale_date_to == "null" ? '' : $sale_date_to;
        $close_date_from = $this->request->get('close_date_from');
        $close_date_to   = $this->request->get('close_date_to');
        $year_built_from = $this->request->get('build_year_from');
        $year_built_to   = $this->request->get('build_year_to');
        $filter_type = $this->request->get('filter_type') ? $this->request->get('filter_type') : '';
        $filter_type_array = explode(",", $filter_type);
        $sale_type = $this->request->get('sale_type');
        $redemption_expires_from     = $this->request->get('redemption_expires_from');
        $redemption_expires_to       = $this->request->get('redemption_expires_to');
        $case_number        = $this->request->get('case_number');

        if(!empty($year_built_from))
            $year_built_from =  date('Y',strtotime($year_built_from));
        if(!empty($year_built_to))
            $year_built_to =  date('Y',strtotime($year_built_to));


        $info = null;
        if (in_array('potential_buy', $filter_type_array)) {
            $info = $this->homebuyerPotentialBuy();
        }
        else if (in_array('property_purchased_acq_a_to_b', $filter_type_array)) {
            $info = $this->homebuyerPropertyPurchasedAcqAtoB();
        }
        else if (in_array('property_closed', $filter_type_array) || in_array('properties_closed_b_to_c', $filter_type_array)) {
            $info = $this->homebuyerPropertyClosed();
        }
        else if (in_array('passit', $filter_type_array)) {
            $info = $this->homebuyerPassit();
            //$info->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id') ;
            $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        }
        else if (in_array('buyit', $filter_type_array)) {
            $info = $this->homebuyerBuyit();
            //$info->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id') ;
            $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        }
        else //if (in_array('total_invite', $filter_type_array))
        {
            $info = $this->homebuyerInvite();
            //$info->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id') ;
            $info->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        }

        $info->select([
            "home_information.house_id"
            ## Needed this field, Important this line
        ]);

        //$info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id') ;
        //$info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id') ;
        if(!empty($address)){
            if (strpos($address, ',') !== false) {
                $addressArray= explode(",", $address);
                $addressArray=array_map('trim', $addressArray);
                $addressArray = array_filter($addressArray);
                $info->whereIn('home_information.address', $addressArray);
            }else{
                $this->likeWhere($info, 'home_information.address', $address);
            }
            
        }
        $this->likeWhere($info, 'city', $city);
        $this->likeWhere($info, 'county', $county);
        $this->where($info, 'state', $state);
        $this->where($info, 'zip', $zip);
        $this->whereBetween($info, 'wholesale_buyer_strategy.est_close_date_b_to_c', $close_date_from, $close_date_to);
        $this->whereBetween($info, 'home_information.year_built', $year_built_from, $year_built_to);


        $isManyJoin = false;
        if (!empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || ( (!empty($sale_type) || ($sale_type==0) ) && $sale_type != 'null' && $sale_type != null )) {
            
            $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
            $this->whereBetween($info, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
            if( $sale_type != 'null' && $sale_type != null){
                $this->where($info, 'sale_details.sale_type', $sale_type);
            }
            $this->likeWhere($info, 'sale_details.case_number', $case_number);
            $isManyJoin = true;
        }

        if (!empty($redemption_expires_from) || !empty($redemption_expires_to)) {
            $info->leftJoin('mortgage_hoa', 'home_information.house_id', '=', 'mortgage_hoa.house_id');
            $info->leftJoin('mortgage_tax', 'home_information.house_id', '=', 'mortgage_tax.house_id');

            $info->where(function($childQuery) use ($redemption_expires_from,$redemption_expires_to ){
                $field = 'mortgage_hoa.redemption_expires';
                $childQuery->where(function($childQueryOne) use ($field, $redemption_expires_from,$redemption_expires_to ){
                    if (!empty($redemption_expires_from))
                        $childQueryOne->where($field, '>=', $redemption_expires_from);

                    if (!empty($redemption_expires_to))
                        $childQueryOne->where($field, '<=', $redemption_expires_to);
                });

                $field = 'mortgage_tax.redemption_expires';
                $childQuery->orWhere(function($childQueryOne) use ($field, $redemption_expires_from,$redemption_expires_to ){
                    if (!empty($redemption_expires_from))
                        $childQueryOne->where($field, '>=', $redemption_expires_from);

                    if (!empty($redemption_expires_to))
                        $childQueryOne->where($field, '<=', $redemption_expires_to);
                });
            });
        }

        // if (!empty($mortgage)) {
        //     $info->leftJoin('mortgage_liens', 'home_information.house_id', '=', 'mortgage_liens.house_id');
        //     $this->where($info, 'mortgage_liens.loan_term', $mortgage);
        //     $isManyJoin = true;
        // }

        if($isManyJoin)
        $info->groupBy('home_information.house_id');


        // $info->select(DB::raw("ANY_VALUE(house_buyit.position) as position"));
        // $info->addSelect(DB::raw("ANY_VALUE(house_buyit.request_type) as request_type"));
        $info->addSelect(DB::raw("'0' as position"));
        $info->addSelect(DB::raw("' ' as request_type"));
        $info->addSelect([
            "home_information.address",
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
            // "property_descriptions.*",
            // "local_real_estate.zestimate",
            "home_information.house_id"
            ## Needed this field, Important this line
        ]);
        $info->with(
            [
                "house.geo",
                "house.property_descriptions",
                "house.local_real_estate_details",
                "house.last_sale_details"     => function ($query) {
                    $query->select(['house_id',
                        'sale_id',
                        'trustee',
                        'sale_date',
                        'sale_time',
                        'case_number',
                        'priceint',
                        'opening_bid',
                        'sale_type',
                        'nos_by',
                        'nos_date',
                        'redemption_expires'
                    ]);
                },
                "house.last_sale_details.nos" => function ($query) {
                    $query->select(["users.id",
                        "users.email",
                        "users.first_name",
                        "users.last_name"]);
                },
                "house.last_cma_arv_recommendations"        => function ($query) {
                    $query->select(['house_id',
                        'recommended_cma_arv',
                        'general_demand',
                        'specific_demand']);
                },
                "house.cma_arv_recommendations"             => function ($query) {
                    $query->select(['house_id',
                        'info_added_by',
                        'user_id',
                        'date']);
                },
                "house.cma_arv_recommendations.user"        => function ($query) {
                    $query->select(["users.id",
                        "users.email",
                        "users.first_name",
                        "users.last_name"]);
                },
                "house.first_liens"                         => function ($query) {
                    $query->select(['house_id',
                        'lien_amount',
                        'date_recorded',
                        'lien_foreclosing'
                        ,
                        'no_str_no_appt'
                    ]);
                },
                "house.second_liens"                        => function ($query) {
                    $query->select(['house_id',
                        'lien_amount',
                        'date_recorded',
                        'lien_foreclosing'
                        ,
                        'no_str_no_appt'
                    ]);
                },
                "house.third_liens"                         => function ($query) {
                    $query->select(['house_id',
                        'lien_amount',
                        'date_recorded',
                        'lien_foreclosing'
                        ,
                        'no_str_no_appt'
                    ]);
                },
                "house.hoa_liens"  => function ($query) {
                    $query->select(['house_id', 'hoa_lien_amount', 
                                    'date_of_hoa_lien', 'hoa_lien_foreclosing'
                                    ,'no_str','redemption_expires'
                    ]);
                },
                "house.tax_liens"  => function ($query) {
                    $query->select(['house_id', 'redemption_expires'
                    ]);
                },
                'house.last_sale_details.last_bidder'       => function ($query) {
                    $query->select(['house_id',
                        'sale_id',
                        'bidder_id',
                        'min_amt_nxt_ub',
                        'last_date_to_upset_bid'
                        ,
                        'name_upset_bidder as wining_bidder',
                        'amount_of_bid as winning_bid'
                        ,
                        'im_by',
                        'im_date'
                    ]);
                },
                'house.last_sale_details.last_bidder.im'    => function ($query) {
                    $query->select(["users.id",
                        "users.email",
                        "users.first_name",
                        "users.last_name"]);
                },
                'house.last_sale_details.sale_descriptions' => function ($query) {
                    $query->select(["sale_id",
                        "notice_of_foreclosure"]);
                },
                "house.last_borrower_info"                  => function ($query) {
                    $query->select(['house_id',
                        'full_name']);
                }
                ,
                "house.last_owner_info"                     => function ($query) {
                    $query->select(['house_id',
                        'full_name']);
                },
                "house.wholesale_buyer_strategy" => function ($query) {
                    $query->select(['house_id',
                        'est_close_date_a_to_b']);
                },
                "house.front_picture"            => function ($query) {
                    $query->select(['house_id',
                        'org_name',
                        'store_name']);
                },
                "house.buy_it_1"=> function ($query) {
                    $query->select(["house_buyit_id"
                        ,"house_id"
                        ,"user_id"
                        ,"position"
                        ,"email"
                        ,"email"
                        ,"first_name","last_name"]);
                },
                "house.buy_it_2"=> function ($query) {
                    $query->select(["house_buyit_id"
                        ,"house_id"
                        ,"user_id"
                        ,"position"
                        ,"email"
                        ,"email"
                        ,"first_name","last_name"]);
                },
                "house.wholesale_buyer_n_total" => function ($query) {
                    $query->select('*');
                },

            ]
        );
        $info->whereNull('home_information.deleted_at');
        $info = $info->skip(intval($offset))->take(intval($limit))->get();

        Log::info("HomeBuyerDashboardController: list2 end");
        if (empty($info)) {
            return response()->json(['status'  => 'success',
                'total'   => 0,
                'data'    => [],
                'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success',
            'total'  => 0,
            'data'   => $info], 200);
    }

    private function homebuyerPassit()
    {
        $info =HouseBuyItModel::where("request_type","passit")->where("house_buyit.user_id",$this->userService->user_id());
        $info->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id');
        $info->whereNull('home_information.deleted_at');
        return $info;
    }

    private function homebuyerBuyit()
    {
        $info=HouseBuyItModel::where("request_type","buyit")->where("house_buyit.user_id",$this->userService->user_id());
        $info->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id');
        $info->whereNull('home_information.deleted_at');
        return $info;
    }


    private function homebuyerInvite()
    {
       $info = InviteModel::where("invitee_to",$this->userService->user_id());
       $info->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id');
       $info->whereNull('home_information.deleted_at');
       return $info;

    }
    private function homebuyerPotentialBuy()
    {
        $info = InviteModel::where("invitee_to",$this->userService->user_id());
        $info->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id')
            ->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        $info->where('wholesale_buyer_strategy.potential_buy',1);
        $info->whereNull('home_information.deleted_at');
        return $info;
    }
    private function homebuyerPropertyPurchasedAcqAtoB()
    {

        $info = InviteModel::where("invitee_to",$this->userService->user_id());
        $info->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id')
            ->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        $info->where('wholesale_buyer_strategy.property_purchased_acq_a_to_b',1);
        $info->whereNull('home_information.deleted_at');
        return $info;
    }
    private function homebuyerPropertyClosed()
    {
        $info = InviteModel::where("invitee_to",$this->userService->user_id());
        $info->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id')
            ->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');
        $info->where('wholesale_buyer_strategy.property_closed',1);
        $info->whereNull('home_information.deleted_at');
        return $info;
    }

    private function applyFilter($queryReference, $call_type)
    {
        $address  = $this->request->get('address') ? $this->request->get('address') : $this->request->get('property_address');
        $city     = $this->request->get('city');
        $county   = $this->request->get('county');
        $state    = $this->request->get('state');
        $zip      = $this->request->get('zip');
        //$mortgage = $this->request->get('mortgage');
        $sale_date_from = $this->request->get('sale_date_from');
        $sale_date_from = $sale_date_from == "null" ? '' : $sale_date_from;
        $sale_date_to   = $this->request->get('sale_date_to');
        $sale_date_to = $sale_date_to == "null" ? '' : $sale_date_to;
        $close_date_from = $this->request->get('close_date_from');
        $close_date_to   = $this->request->get('close_date_to');
        $year_built_from = $this->request->get('build_year_from');
        $year_built_to   = $this->request->get('build_year_to');
        $sale_type = $this->request->get('sale_type');
        $redemption_expires_from     = $this->request->get('redemption_expires_from');
        $redemption_expires_to       = $this->request->get('redemption_expires_to');
        $case_number        = $this->request->get('case_number');

        if(!empty($year_built_from))
            $year_built_from =  date('Y',strtotime($year_built_from));
        if(!empty($year_built_to))
            $year_built_to =  date('Y',strtotime($year_built_to));


        if(
            !empty($address) || !empty($city) || !empty($county) || !empty($state) || !empty($year_built_from)
            || !empty($year_built_to)
            || !empty($sale_date_from)
            || !empty($sale_date_to)
            //|| !empty($mortgage)
            || !empty($close_date_from)
            || !empty($close_date_to)
            || ( (!empty($sale_type) || ($sale_type==0) ) && $sale_type != 'null' && $sale_type != null )
            || !empty($redemption_expires_from)
            || !empty($redemption_expires_to)
            || !empty($case_number)



        )
        {
            $queryReference->select([
                "home_information.house_id"
                ## Needed this field, Important this line
            ]);

            //if($call_type == 'buyit' || $call_type == 'passit')
            //$queryReference->leftJoin('home_information', 'home_information.house_id', '=', 'house_buyit.house_id');

            //if($call_type == 'invite' )
           //     $queryReference->leftJoin('home_information', 'home_information.house_id', '=', 'invitations_info.house_id');

            //$this->likeWhere($queryReference, 'home_information.address', $address);
            if(!empty($address)){
                if (strpos($address, ',') !== false) {
                    $addressArray= explode(",", $address);
                    $addressArray=array_map('trim', $addressArray);
                    $addressArray = array_filter($addressArray);
                    $queryReference->whereIn('home_information.address', $addressArray);
                }else{
                    $this->likeWhere($queryReference, 'home_information.address', $address);
                }
                
            }

            $this->likeWhere($queryReference, 'city', $city);
            $this->likeWhere($queryReference, 'county', $county);
            $this->where($queryReference, 'state', $state);
            $this->where($queryReference, 'zip', $zip);
            $this->whereBetween($queryReference, 'home_information.year_built', $year_built_from, $year_built_to);
          
        }

        if(!empty($close_date_from) || !empty($close_date_to))
        {
            // We already added left join in $queryReference of potential_buy
            if($call_type != 'wholesale_buyer_strategy')
            $queryReference->leftJoin('wholesale_buyer_strategy', 'home_information.house_id', '=', 'wholesale_buyer_strategy.house_id');

            $this->whereBetween($queryReference, 'wholesale_buyer_strategy.est_close_date_b_to_c', $close_date_from, $close_date_to);
        }

        $isManyJoin = false;
        if (!empty($case_number) || !empty($sale_date_from) || !empty($sale_date_to) || ( (!empty($sale_type) || ($sale_type==0) ) && $sale_type != 'null' && $sale_type != null )) {
            $queryReference->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
            $this->whereBetween($queryReference, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
            $this->likeWhere($queryReference, 'sale_details.case_number', $case_number);
            
            if( $sale_type != 'null' && $sale_type != null){
                $queryReference->where('sale_details.sale_type','=',"$sale_type");
            }

            $isManyJoin = true;
        }
         if (!empty($redemption_expires_from) || !empty($redemption_expires_to)) {
             $queryReference->leftJoin('mortgage_hoa', 'home_information.house_id', '=', 'mortgage_hoa.house_id');
             $queryReference->leftJoin('mortgage_tax', 'home_information.house_id', '=', 'mortgage_tax.house_id');

             $queryReference->where(function($childQuery) use ($redemption_expires_from,$redemption_expires_to ){
                 $field = 'mortgage_hoa.redemption_expires';
                 $childQuery->where(function($childQueryOne) use ($field, $redemption_expires_from,$redemption_expires_to ){
                     if (!empty($redemption_expires_from))
                         $childQueryOne->where($field, '>=', $redemption_expires_from);

                     if (!empty($redemption_expires_to))
                         $childQueryOne->where($field, '<=', $redemption_expires_to);
                 });

                 $field = 'mortgage_tax.redemption_expires';
                 $childQuery->orWhere(function($childQueryOne) use ($field, $redemption_expires_from,$redemption_expires_to ){
                     if (!empty($redemption_expires_from))
                         $childQueryOne->where($field, '>=', $redemption_expires_from);

                     if (!empty($redemption_expires_to))
                         $childQueryOne->where($field, '<=', $redemption_expires_to);
                 });
             });
         }

        // if (!empty($mortgage)) {
        //     $queryReference->leftJoin('mortgage_liens', 'home_information.house_id', '=', 'mortgage_liens.house_id');
        //     $this->where($queryReference, 'mortgage_liens.loan_term', $mortgage);
        //     $isManyJoin = true;
        // }

        if($isManyJoin == true)
        {
            
            $queryReference->groupBy('home_information.house_id');
            $queryReference = DB::table(DB::raw("(" . $queryReference->toSql() . ") as res"))
                ->mergeBindings($queryReference->getQuery()) ;
           
        }
         
        return $queryReference;

        //        if (!empty($or_where)) {
        //            $info->where(function ($query2) use ($or_where) {
        //                foreach ($or_where as $key => $value) {
        //                    $query2->orWhere(key($value), '=', $value[key($value)]);
        //                }
        //            });
        //        }

    }
}
