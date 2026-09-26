<?php
namespace App\Services;


use App\Models\DriveListModel;
use App\Models\DriveListDetailModel;
use App\Models\PropertyModel;
use App\Models\UserDriveListModel;
use Illuminate\Http\Request;
use Log;
 
Class DriveListService{

    public function __construct(Request $request)
    {
        Log::info("UserFavoritesController: __construct called");
        $this->request  = $request;
    }

    function getDriveList($house_id){
        
        return PropertyModel::select('home_information.house_id')
                ->with(['drive_list',
                        'drive_list.drive_list_detail',
                        ])
                ->where('home_information.house_id', $house_id)->first();
    }

    function storeDriveList($info){
        
        return DriveListModel::updateOrCreate(["drive_list_id" => $info['drive_list_id']], $info);
    }

    function storeDriveListDetail($info){
        return DriveListDetailModel::updateOrCreate(["drive_list_detail_id" => $info['drive_list_detail_id']], $info);
    }

     /**
     * Find property record
     * @param $house_byit_id
     * @return mixed
     */
    public function findUserDriveListById($id)
    {
        Log::info("DriveListService: findOneById called");

        $this->findOneById =  UserDriveListModel::find($id);
        return $this->findOneById;
    }
    public function getUserDriveListInfo($house_id)
    {
        Log::info("DriveListService: isFavourite called");

        return UserDriveListModel::where([
            'user_id'=>$this->userService->user_id(),
            'house_id'=>$house_id  ])->count();

    }
    public function createUserDriveList($infoData){
        Log::info("DriveListService: create called");

        $infoData['user_id'] = $this->userService->user_id();
        return UserDriveListModel::create($infoData);
    }

    public function updateOrCreateUserDriveList($propertyData){
        Log::info("DriveListService: updateOrCreate called");

        if(empty($propertyData))
            return false;

        //$propertyData['user_id'] = $this->userService->user_id();
        return UserDriveListModel::updateOrCreate($propertyData);
    }

    public function getUserDriveList($user_id){

        $limit              = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset             = $this->request->get('offset') ? $this->request->get('offset') : 0;

        $info = PropertyModel::select(["home_information.house_id"]);
        $info->select([
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
           //  "property_descriptions.*",
           //  "local_real_estate.zestimate",
            "home_information.house_id"## Needed this field, Important this line
        ]);
        $info->with([
            "geo",
            "property_descriptions",
            "local_real_estate_details",
            "user_drive_list"=> function ($query) use($user_id) {
                $query->select(['house_id', 'user_id'])->where('user_id',$user_id);
            },
            "off_site"=> function ($query) {
                $query->select(['house_id', 'off_site', 'updated_at'
                ]);
            },
            "last_cma_arv_recommendations"=> function ($query) {
            $query->select(['house_id', 'recommended_cma_arv', 'general_demand', 'specific_demand','days_on_market']);
            },
            "cma_arv_recommendations"=> function ($query) {
                $query->select(['house_id', 'info_added_by', 'user_id', 'date']);
            },
            "cma_arv_recommendations.user"=> function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "first_liens"=> function ($query) {
            $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                            ,'no_str_no_appt','amortization_loan_estimate_balance','total_est_debt'
                           ]);
            },
            "second_liens"                                => function ($query) {
                $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                ,'no_str_no_appt'
                               ]);
            },
            "third_liens"                         => function ($query) {
                $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                                ,'no_str_no_appt'
                               ]);
            },

            "hoa_liens"  => function ($query) {
                $query->select(['house_id', 'hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_foreclosing'
                                ,'no_str','hoa_name','redemption_expires'
                               ]);
            },
            "last_borrower_info" =>function ($query) {
                $query->select(['house_id', 'full_name']);
            }
            ,"last_owner_info" =>function ($query) {
                $query->select(['house_id', 'full_name']);
            },
            "front_picture"=> function ($query) {
            $query->select(['house_id', 'org_name', 'store_name']);
            },
             
            'last_invite_date' =>function ($query) {
                $query->select(["invitations_info_id"
                                ,"house_id"
                                ,"created_at"]);
            },
            "last_sale_details" => function ($query) {
                $query->select(['house_id', 'sale_id', 'trustee', 'sale_date', 'sale_time'
                                , 'case_number', 'priceint', 'opening_bid', 'sale_type', 'sale_status'
                                , 'nos_by','nos_date','sale_place','trustee_file_no'
                               ]);
                },
            'last_sale_details.last_bidder'=> function ($query) {
                $query->select(['house_id', 'sale_id','bidder_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                                ,'name_upset_bidder','name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                                ,'im_by','im_date'
                                ]);
            },
            'bidder_sale_details.last_bidder.im'      => function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "last_sale_details.nos"=> function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "owner_info"
        ]);

        $info = $info->leftJoin('user_drive_list', 'home_information.house_id', '=', 'user_drive_list.house_id');
        $info = $this->where($info, 'user_drive_list.user_id',$user_id);
        $info = $info->whereNull('home_information.deleted_at');
        $isManyJoin = false;
        if ($isManyJoin === false)
            $total = $info->count();
        else
        {
            $info->groupBy('home_information.house_id');
            $total = $info->get()->count();
        }
        $info = $info->skip(intval($offset))->take(intval($limit))->get();

       return  $info;
    }

    public function getPropertyDriveListInfo($house_id){
        
        $info = PropertyModel::select(["home_information.house_id"]);
        $info->select([
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
            "home_information.house_id"## Needed this field, Important this line
        ]);
        $info->with([
            "property_descriptions",
            "local_real_estate_details",
            "off_site"=> function ($query) {
                $query->select(['house_id', 'off_site', 'updated_at'
                ]);
            },
            "last_cma_arv_recommendations"=> function ($query) {
            $query->select(['house_id', 'recommended_cma_arv', 'general_demand', 'specific_demand','days_on_market']);
            },
            "cma_arv_recommendations"=> function ($query) {
                $query->select(['house_id', 'info_added_by', 'user_id', 'date']);
            },
            "cma_arv_recommendations.user"=> function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "first_liens"=> function ($query) {
            $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing'
                            ,'no_str_no_appt','amortization_loan_estimate_balance','total_est_debt'
                           ]);
            },
            "second_liens"=> function ($query) {
                $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing','no_str_no_appt']);
            },
            "third_liens"=> function ($query) {
                $query->select(['house_id', 'lien_amount', 'date_recorded', 'lien_foreclosing','no_str_no_appt']);
            },
            "hoa_liens"=> function ($query) {
                $query->select(['house_id', 'hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_foreclosing'
                                ,'no_str','hoa_name','redemption_expires'
                               ]);
            },
            "last_borrower_info" =>function ($query) {
                $query->select(['house_id', 'full_name']);
            },
            "front_picture"=> function ($query) {
            $query->select(['house_id', 'org_name', 'store_name']);
            },
            "last_sale_details" => function ($query) {
                $query->select(['house_id', 'sale_id', 'trustee', 'sale_date', 'sale_time'
                                , 'case_number', 'priceint', 'opening_bid', 'sale_type', 'sale_status'
                                , 'nos_by','nos_date','sale_place','trustee_file_no'
                               ]);
                },
            'last_sale_details.last_bidder'=> function ($query) {
                $query->select(['house_id', 'sale_id','bidder_id', 'min_amt_nxt_ub', 'last_date_to_upset_bid'
                                ,'name_upset_bidder','name_upset_bidder as wining_bidder', 'amount_of_bid as winning_bid'
                                ,'im_by','im_date'
                                ]);
            },
            'bidder_sale_details.last_bidder.im'      => function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "last_sale_details.nos"=> function ($query) {
                $query->select(["users.id","users.email","users.first_name","users.last_name"]);
            },
            "owner_info"
        ]);

        $info = $this->where($info, 'home_information.house_id',$house_id);
        $info = $info->whereNull('home_information.deleted_at');
        $info = $info->first();

        return  $info;
    }

    private function where($query, $field, $value, $condition = "=")
    {
        if (empty($value))
            return $query;
        return $query->where($field, $condition, $value);
    }
}