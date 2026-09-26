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


class VehicleSaleInfoModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'vehicle_sale_info';

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
        'state','county', 'case_number', 'sale_type'
        ,'sale_date','sale_time', 'sale_location','trustee','petitioner','created_at','updated_at'

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

        //$this->attributes['created_at'] = time();
    }

    public function respondent()
    {
        return $this->hasMany(VehicleSaleRespondentInfoModel::class,"vehicle_sale_id","id")->select(["id","vehicle_sale_id","respondent"]);
    }

    public function vehicle_info()
    {
        return $this->hasMany(VehicleInfoModel::class,"vehicleId","id");
    }

    public function vehicle_nos_info()
    {
        return $this->hasMany(VehicleNosInfoModel::class,"vehicleId","id");
    }

    public function vehicle_cmaArv_info()
    {
        return $this->hasMany(VehicleCmaArvInfoModel::class,"vehicleId","id");
    }
}
