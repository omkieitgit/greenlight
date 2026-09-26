<?php
/**
 * Created By Rativardhan Singh Sengar  10/27/18 5:48 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 9/16/18 10:52 PM
 */

namespace App\Models;

use App\Services\HouseBuyItService;
use App\Services\SchoolNeighborhoodService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class PropertyModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;
    public $timestamps = true;
    protected $dateFormat = 'U';
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $table = 'home_information';
    protected $primaryKey = 'house_id';
    # you should use either  $fillable or $guarded - not both
    //    protected $guarded = ['house_id'];


    protected $fillable = [
         'zpid', 'address', 'city', 'county', 'state', 'zip', 'total_living_sqft', 'cost_per_sqft'
        , 'total_sqft', 'cost_sqft', 'main_floor_area', 'second_floor_area', 'third_floor_area', 'basement_area'
        , 'finished_basement_area', 'finished_attic', 'enclosed_porch', 'bonus_room', 'year_built', 'bed'
        , 'bath', 'full_bath', 'half_bath', 'three_quarter_bath', 'of_families', 'of_kitchen', 'fireplaces'
        , 'subdivision', 'ext_wall_type', 'roofing', 'ac', 'heating', 'pool', 'spa', 'garages', 'garage_types'
        , 'garage_sf', 'lot_acreage_sf', 'stories', 'property_type', 'specific_property_type', 'building_style'
        , 'parcel_id1', 'parcel_id2', 'prc_url', 'country_assessor_url', 'gis_url', 'treasurer_url',
        'tax_bill_url','record_number', 'choose_prc', 'county_value','county_url','deleted_at','specific_property_location',
        'tax_sale','pick_from','drive_list_link','legal_summary_report','excess_surplus_funds','want_to_sale'

    ];


    //    public static function getTableName()
    //    {
    //        return with(new static)->getTable();
    //    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function assessment()
    {
        return $this->hasMany(AssessmentModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
    }

    public function local_real_estate_details()
    {
        return $this->hasOne(LocalRealEstateModel::class,"house_id","house_id");
    }
    public function trustee()
    {
        return $this->hasOne(TrusteeModel::class,"house_id","house_id");
    }

    public function price_history()
    {
        return $this->hasMany(PriceHistoryModel::class,"house_id","house_id");
    }

    public function schools_and_neighborhood()
    {
        return $this->hasOne(SchoolNeighborhoodModel::class,"house_id","house_id");
    }
    public function property_descriptions()
    {
        return $this->hasOne(PropertyDescriptionsModel::class,"house_id","house_id");
    }
    public function document_property()
    {
        return $this->hasMany(DocumentPropertyModel::class,"house_id","house_id");
    }

    public function mortgage_liens()
    {
        return $this->hasMany(MortgageLiensModel::class,"house_id","house_id");
    }

    public function first_liens()
    {
        return $this->hasOne(MortgageLiensModel::class,"house_id","house_id")->where('lien_type',1);
    }
    public function second_liens()
    {
        return $this->hasOne(MortgageLiensModel::class,"house_id","house_id")->where('lien_type',2);
    }
    public function third_liens()
    {
        return $this->hasOne(MortgageLiensModel::class,"house_id","house_id")->where('lien_type',3);
    }

    public function other_liens()
    {
        return $this->hasOne(MortgageOtherModel::class,"house_id","house_id");
    }

    public function hoa_liens()
    {
        return $this->hasOne(MortgageHoaModel::class,"house_id","house_id");
    }

    public function tax_liens()
    {
        return $this->hasOne(MortgageTaxModel::class,"house_id","house_id");
    }


    public function owner_info()
    {
        return $this->hasMany(OwnerModel::class,"house_id","house_id");
    }

    public function last_owner_info()
    {
        return $this->hasOne(OwnerModel::class,"house_id","house_id")->orderBy('id','desc');
    }


    public function borrower_info()
    {
        return $this->hasMany(BorrowerModel::class,"house_id","house_id");
    }

    public function last_borrower_info()
    {
        return $this->hasOne(BorrowerModel::class,"house_id","house_id")->orderBy('id','desc');
    }


    public function sale_details()
    {
        return $this->hasMany(SaleDetailsModel::class,"house_id","house_id");
    }

    public function bidder_sale_details()
    {
        return $this->hasOne(SaleDetailsModel::class,"house_id","house_id")->whereNotNull('sale_date');
    }

    public function last_sale_details()
    {
        return $this->hasOne(SaleDetailsModel::class,"house_id","house_id")->whereNotNull('sale_date')->orderBy("sale_date",'desc');
    }

    public function last_sale_details2()
    {
        return $this->hasOne(SaleDetailsModel::class,"house_id","house_id")
        ->select(['sale_details.*',DB::raw('count(sale_bidder.bidder_id) as total_bidder')])
        ->join('sale_bidder', 'sale_bidder.sale_id', '=', 'sale_details.sale_id')
        ->whereNotNull('sale_date')
        ->whereNotNull('name_upset_bidder')
        ->groupBy('sale_id')
        ->orderBy("sale_date",'desc');
    }


    public function cma_arv_recommendations()
    {
        return $this->hasMany(CmaArvModel::class,"house_id","house_id");
    }

    public function last_cma_arv_recommendations()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->whereIn('info_added_by',['first_dtc','second_dca','third_dca','final_dca'])->whereNotNull('recommended_cma_arv')->orderBy('info_added_by','desc');
    }
    public function last_rental_rate()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->whereIn('info_added_by',['first_dtc','second_dca','third_dca','final_dca'])->whereNotNull('rental_rate')->orderBy('info_added_by','desc');
    }

    public function third_cma_arv_recommendations()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->whereIn('info_added_by',['third_dca'])->whereNotNull('recommended_cma_arv')->orderBy('info_added_by','desc');
    }


    public function first_dtc()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->where('info_added_by','first_dtc');
    }
    public function second_dca()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->where('info_added_by','second_dca');
    }
    public function third_dca()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->where('info_added_by','third_dca');
    }

    public function geo()
    {
        return $this->hasOne(GeoModel::class,"house_id","house_id");
    }


    public function front_picture()
    {
        return $this->hasOne(DocumentPictureModel::class,"house_id","house_id");
    }

    public function property_acquisition_a_to_b_first()
    {
        return $this->hasOne(PropertyAcquisitionAtoBFirstModel::class,"house_id","house_id");
    }

    public function property_acquisition_a_to_b_second()
    {
        return $this->hasOne(PropertyAcquisitionAtoBSecondModel::class,"house_id","house_id");
    }

    public function wholesale_buyer_strategy()
    {
        return $this->hasOne(WholesaleBuyerStrategyModel::class,"house_id","house_id");
    }

    public function buy_it_1()
    {
        // return $this->hasOne(HouseBuyItModel::class,"house_id","house_id")
        //             ->where('position', '=', 1)
        //             ->where('request_type', '=', 'buyit')
        //             ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
        //             ->orderBy('position', 'asc');


        return $this->hasOne(BuyitDesignationModel::class,"house_id","house_id")
                ->where('position', '=', 1)
                ->where('user_status', '=', 'active')
                ->leftJoin('users', 'users.id', '=', 'buyit_designation.user_id')
                ->orderBy('position', 'asc');
    }

    public function buy_it_2()
    {
        // return $this->hasOne(HouseBuyItModel::class,"house_id","house_id")
        //             ->where('position', '=', 2)
        //             ->where('request_type', '=', 'buyit')
        //             ->leftJoin('users', 'users.id', '=', 'house_buyit.user_id')
        //             ->orderBy('position', 'asc');

        return $this->hasOne(BuyitDesignationModel::class,"house_id","house_id")
        ->where('position', '=', 2)
        ->where('user_status', '=', 'active')
        ->leftJoin('users', 'users.id', '=', 'buyit_designation.user_id')
        ->orderBy('position', 'asc');
    }

    public function wholesale_buyer_n_total()
    {
        return $this->hasOne(WholesaleBuyerNTotalModel::class,"house_id","house_id");
    }

    public function off_site()
    {
        return $this->hasOne(OffSiteModel::class,"house_id","house_id");
    }

    public  function last_invite_date()
    {
      return $this->hasOne(InviteModel::class,"house_id","house_id")
                  ->orderBy('invitations_info_id', 'DESC');
    }
    public  function mapVideo()
    {
      return $this->hasOne(MapVideoModel::class,"house_id","house_id");
    }

    function mailing_list(){
        return $this->hasMany(MailingModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
   
    }
    public function subto_property()
    {
        return $this->hasOne(SubToPropertyModel::class,"house_id","house_id");
    }

    public function drive_list()
    {
        return $this->hasMany(DriveListModel::class,"house_id","house_id");
    }

    public function user_drive_list()
    {
        return $this->hasMany(UserDriveListModel::class,"house_id","house_id");
    }
}
