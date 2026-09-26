<?php
/**
 * Created By Rativardhan Singh Sengar  1/13/19 8:28 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/25/18 6:44 PM
 */

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class MortgageOtherLiensPropertyTaxesModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'mortgage_other_liens_property_taxes';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'house_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id', 'county_rod_ur', 'manual_search', 'no_active_mortgage_lien'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function mortgage_liens()
    {
        //return $this->hasMany(MortgageLiensModel::class,"house_id","house_id");
    }
}
