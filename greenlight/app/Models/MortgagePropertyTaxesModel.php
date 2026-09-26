<?php
/**
 * Created By Rativardhan Singh Sengar  1/21/19 11:46 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/21/19 11:46 PM
 */

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class MortgagePropertyTaxesModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'mortgage_property_taxes';

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
        'house_id', 'treasure_url', 'tax_bill_url', 'total_property_taxes_owed' 
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function mortgage_property_taxes_document()
    {
        return $this->hasMany(DocumentMortgagePropertyTaxesModel::class,"house_id","house_id");
    }
    public function mortgage_property_taxes_owed()
    {
        return $this->hasMany(MortgagePropertyTaxesOwedModel::class,"house_id","house_id");
    }
}
