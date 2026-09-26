<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:42 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 11/6/18 8:38 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class DepositSpreadsheetModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'deposit_spreadsheet';

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
        'house_id','deposit_date', 'transaction', 'in_amount_bidding', 'out_amount_bidding', 'checksum',
        'bidding','etienne', 'avignon', 'mike_tripp', 'larochelle', 'marseille',
        'llc_bank_ac','cash_check', 'llc_name', 'sp_number', 'county', 'deposit_link','withdrawal_link'

    ];
   
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function deposit_lender(){
        return $this->hasMany(DepositSheetLenderModel::class,"deposit_id","id"); 
    }

    public function deposit_link(){
        return $this->hasMany(DepositSheetLinkModel::class,"deposit_id","id");
    }

    public function house(){
        return $this->hasOne(HouseModel::class,"house_id","house_id")->select(['house_id','address']);
    }
}
