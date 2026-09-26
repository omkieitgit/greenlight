<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:46 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 11:45 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Storage;

class VehicleInfoModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'vehicle_info';

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
        'vehicle_year','vehicle_make', 'vehicle_id', 'registered_owner_1','vehicleId'
        ,'registered_owner_2','county_tax', 'year','vehicle_condition','org_name','store_name','created_at','updated_at'

    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['added_by'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['created_at'] = time();
    }

      /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];


    public function getUrlAttribute()
    {
        if(empty($this->attributes['store_name']))
            return '';

        $store_file_name = $this->attributes['store_name'];
        $url = url('document/vehicle_document/' . $store_file_name);

        return $url;
    }
}
