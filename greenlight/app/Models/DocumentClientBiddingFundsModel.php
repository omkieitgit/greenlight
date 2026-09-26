<?php
/**
 * Created By Rativardhan Singh Sengar  9/22/19 10:34 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/22/19 3:41 PM
 */

namespace App\Models;

use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class DocumentClientBiddingFundsModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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

    protected $table = 'document_client_bidding_funds';

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
        'id', 'house_id','document_date', 'added_by'
        , 'org_name', 'store_name'
        , 'org_name_receipt', 'store_name_receipt'
        , 'bidding_type', 'document_date', 'amount'
        , 'aa_account', 'approval', 'created_at', 'deleted_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url_authorization','url_receipt'];


    public function getUrlAuthorizationAttribute()
    {
        if(empty($this->attributes['store_name']))
            return '';

        $store_file_name = $this->attributes['store_name'];
        $url = url('document/document_client_bidding_funds_authorization/' . $store_file_name);

        return $url;
    }

    public function getUrlReceiptAttribute()
    {
        if(empty($this->attributes['store_name_receipt']))
            return '';

        $store_file_name = $this->attributes['store_name_receipt'];
        $url = url('document/document_client_bidding_funds_receipt/' . $store_file_name);

        return $url;
    }

    public function house()
    {
        return $this->belongsTo('App\Models\OwnerModel',"id");
    }

    public function user()
    {
        return $this->belongsTo(User::class,'added_by','id')->select(["users.id","users.first_name","users.last_name"]);
    }
}
