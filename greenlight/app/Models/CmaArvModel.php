<?php
/**
 * Created By Mranalinee Chouhan
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class CmaArvModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

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

    protected $table = 'cma_arv_recommendations';

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
        'house_id', 'user_id', 'date', 'info_added_by', 'specific_demand', 'general_demand', 'days_on_market'
        , 'phase_renovation', 'price_sqft_sale_comps_from', 'price_sqft_sale_comps_to', 'ssd_sale_comps'
        , 'gsd_sale_comps', 'rent_gsd', 'price_sqft_sold_comps_from', 'price_sqft_sold_comps_to'
        , 'ssd_sold_comps', 'gsd_sold_comps', 'rental_comps_map', 'p1_value', 'p2_value', 'p3_value'
        , 'rents_zestimate', 'p1_adom', 'p2_adom', 'p3_adom', 'rental_rate', 'comp_url_1', 'comp_url_2'
        , 'comp_url_3', 'comp_url_4', 'recommended_cma_arv', 'wholetail_value'
    ];

    public function setSsdSaleCompsAttribute($value)
    {
        $compressed = gzdeflate($value,  9);
        $compressed = base64_encode($compressed);
        $this->attributes['ssd_sale_comps'] = ($compressed);
    }
    public function getSsdSaleCompsAttribute($value)
    {
        if(empty($value) || base64_decode($value, true) == false)
            return $value;


        $value = base64_decode($value);
        $compressed = @gzinflate($value);
//        $deflated = @gzinflate($data); // to avoid getting a warning
//        if ($data != $deflated && $deflated !== FALSE) {
//            $source  = gzinflate($data);
//        }
        return $this->attributes['ssd_sale_comps'] = ($compressed);
    }


    public function setGsdSaleCompsAttribute($value)
    {
        $compressed = gzdeflate($value,  9);
        $compressed = base64_encode($compressed);
        $this->attributes['gsd_sale_comps'] = ($compressed);
    }
    public function getGsdSaleCompsAttribute($value)
    {
        if(empty($value) || base64_decode($value, true) == false)
            return $value;

        $value = base64_decode($value);
        $compressed = @gzinflate($value);
        return $this->attributes['gsd_sale_comps'] = ($compressed);
    }


    public function setSsdSoldCompsAttribute($value)
    {
        $compressed = gzdeflate($value,  9);
        $compressed = base64_encode($compressed);
        $this->attributes['ssd_sold_comps'] = ($compressed);
    }
    public function getSsdSoldCompsAttribute($value)
    {
        if(empty($value) || base64_decode($value, true) == false)
            return $value;

        $value = base64_decode($value);
        $compressed = @gzinflate($value);
        return $this->attributes['ssd_sold_comps'] = ($compressed);
    }

    public function setGsdSoldCompsAttribute($value)
    {
        $compressed = gzdeflate($value,  9);
        $compressed = base64_encode($compressed);
        $this->attributes['gsd_sold_comps'] = ($compressed);
    }

    public function getGsdSoldCompsAttribute($value)
    {
        if(empty($value) || base64_decode($value, true) == false)
            return $value;

        $value = base64_decode($value);
        $compressed = @gzinflate($value);
        return $this->attributes['gsd_sold_comps'] = ($compressed);
    }

    public function setRentalCompsMapAttribute($value)
    {
        $compressed = gzdeflate($value,  9);
        $compressed = base64_encode($compressed);
        $this->attributes['rental_comps_map'] = ($compressed);
    }

    public function getRentalCompsMapAttribute($value)
    {
        if(empty($value) || base64_decode($value, true) == false)
            return $value;

        $value = base64_decode($value);
        $compressed = @gzinflate($value);
        return $this->attributes['rental_comps_map'] = ($compressed);
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function compSection(){
        return $this->hasMany(CompSectionHistoryModel::class,"foreign_id","id")->whereIn("section_type",["first_dtc","second_dca","third_dca","final_dca"]);
    }
}
