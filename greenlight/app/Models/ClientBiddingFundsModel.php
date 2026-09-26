<?php
/**
 * Created By Rativardhan Singh Sengar  12/22/19 11:11 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/22/19 11:11 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class ClientBiddingFundsModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'client_bidding_funds';

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

       'house_id','bid_upset_date','last_date_for_next_upset','trustee_deposit_returned'
        ,'trustee_deposit_returned_date','trustee_deposit_returned_to','county_deposit_returned'
        ,'county_deposit_returned_date','county_deposit_returned_to','notes',
        'id','document_date', 'added_by' , 'org_name', 'store_name'
        , 'org_name_receipt', 'store_name_receipt' , 'bidding_type', 'amount'
        , 'aa_account', 'approval', 'created_at', 'deleted_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function documents()
    {
        return $this->hasMany(DocumentClientBiddingFundsModel::class,"house_id","house_id");
    }


}
