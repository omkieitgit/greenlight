<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class W9Model extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'w9';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'house_id', 'business_name','federal_tax','address','city', 'account_number', 'requesters_name_address', 'exempt_payee_code', 'exemption_FATCA_reporting_code', 'taxpayer_identification_number', 'employer_identification_number','social_security_number','signature','w9_date','created_at','updated_at',
        'user_id',
        'social_security_number_2',
        'social_security_number_3',
        'employer_identification_number_2',
        'tax_classification',
        'other_instructions'
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
