<?php

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;


class McdModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    //
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'house_mcd';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id', 'lender_name', 'purpose_of_funds'
        , 'in_date', 'out_date'
        , 'amount', 'returned_amount'
        , 'funded_days', 'pts'
        , 'interest', 'lender_referral'
        , 'org_name', 'store_name'
        , 'created_at','updated_at',
        'interest_type','mcd_type','document_url','lender_referral_pre'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];


    public function getUrlAttribute()
    {

        if(empty($this->attributes['store_name']) && empty($this->attributes['store_name']))
            return '';

        $store_file_name1 = $this->attributes['store_name'];
        $url['store_name'] = url('document/document_investor/' . $store_file_name1);
        //print_r($url);
        return $url;
    }

    public function mcdLender() {
        return $this->belongsTo(McdLenderModel::class, "lender_name", "id")->select(["mcd_lender.lender_name","mcd_lender.id"]);
    }

  

}
