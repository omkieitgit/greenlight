<?php
/**
 * Created By Rativardhan Singh Sengar  10/30/18 11:54 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/29/18 9:28 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class HouseModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'home_information';

    /**
     * Indicates model primary keys.
     */
    //protected $primaryKey = 'house_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
     protected $fillable = [
         'zpid', 'address', 'city', 'county', 'state', 'zip', 'total_living_sqft', 'cost_per_sqft'
        , 'total_sqft', 'cost_sqft', 'main_floor_area', 'second_floor_area', 'third_floor_area', 'basement_area'
        , 'finished_basement_area', 'finished_attic', 'enclosed_porch', 'bonus_room', 'year_built', 'bed'
        , 'bath', 'full_bath', 'half_bath', 'three_quarter_bath', 'of_families', 'of_kitchen', 'fireplaces'
        , 'subdivision', 'ext_wall_type', 'roofing', 'ac', 'heating', 'pool', 'spa', 'garages', 'garage_types'
        , 'garage_sf', 'lot_acreage_sf', 'stories', 'property_type', 'specific_property_type', 'building_style'
        , 'parcel_id1', 'parcel_id2', 'prc_url', 'country_assessor_url', 'gis_url', 'treasurer_url', 'tax_bill_url', 'record_number', 'choose_prc'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    /**
     * Get the House that owns the Assessment.
     */
    public function home_information()
    {
        return $this->belongsTo('App\Models\PropertyModel',"house_id");
    }

}
