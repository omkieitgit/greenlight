<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:42 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 11/6/18 8:38 PM
 */

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class SaleDetailsModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    #public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'sale_details';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'sale_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
        'sale_id', 'house_id', 'sale_date', 'case_number'
        , 'opening_bid', 'sale_type', 'sale_status', 'sale_place', 'sale_time', 'trustee_file_no'
        , 'priceint', 'trustee_scraped', 'trustee', 'trustee_url', 'trustee_address', 'trustee_phone'
        , 'trustee_hours', 'legal_notice_url', 'legal_date_pulled', 'auction_com_url', 'auction_date_pulled'
        , 'newspapaer_url', 'newspapaer_date_pulled', 'nos_by','nos_date','book','page_number',
        'redemption_expires','im_by','im_date','trustee_caller','trustee_caller_date','auction_by','auction_date',
        'redemption_date','redemption_by','excess_fund_by','excess_fund_date','roddy_frcl','redemption_notes'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function document_sale()
    {
        return $this->hasMany(DocumentSaleModel::class,"sale_id","sale_id");
    }

    public function bidders()
    {
        return $this->hasMany(SaleBidderModel::class,"sale_id","sale_id")->with('notes');
    }

    public function bidders_info()
    {
        return $this->hasMany(SaleBidderModel::class,"sale_id","sale_id");
    }


    public function last_bidder()
    {
        return $this->hasOne(SaleBidderModel::class,"sale_id","sale_id")->orderBy('bidder_id','desc');
    }

    public function nos()
    {
        return $this->belongsTo(User::class,"nos_by","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function im()
    {
        return $this->belongsTo(User::class,"im_by","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function trustee_callers()
    {
        return $this->belongsTo(User::class,"trustee_caller","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function auction()
    {
        return $this->belongsTo(User::class,"auction_by","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function redemption()
    {
        return $this->belongsTo(User::class,"redemption_by","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function excess_fund()
    {
        return $this->belongsTo(User::class,"excess_fund_by","id")->select(["users.id","users.first_name","users.last_name"]);
    }


    public function sale_descriptions()
    {
        return $this->hasOne(SaleDetailsDescriptionsModel::class,"sale_id","sale_id");
    }

    public function sale_trustee_notes()
    {
        return $this->hasMany(SaleTrusteeNotesModel::class,"sale_id","sale_id")->orderBy('id','DESC');
    }

}
