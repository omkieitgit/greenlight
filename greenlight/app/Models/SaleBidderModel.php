<?php
/**
 * Created By Rativardhan Singh Sengar  12/25/18 6:41 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 12/21/18 10:09 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;


class SaleBidderModel extends Model implements AuthenticatableContract, AuthorizableContract {
    use Authenticatable, Authorizable;
    use SoftDeletes;
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

    protected $table = 'sale_bidder';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'bidder_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
        'sale_id',
        'house_id',
        'name_upset_bidder',
        'amount_of_bid',
        'bid_date',
        'last_date_to_upset_bid',
        'min_amt_nxt_ub',
        'deposit_upset',
        'address',
        'phone',
        'email',
        'fax',
        'date_of_sale',
        'date_of_report',
        'name_of_mortage',
        'name_of_cryer',
        'bid_confirmed',
        'bid_upset',
        'city',
        'zipcode',
        'attorney_name',
        'attorney_address',
        'attorney_city',
        'attorney_zipcode',
        'attorney_phone',
        'deposit_clerk',
        'filling_date',
        'last_date_to_next_upset_bid',
        'deposit_amt_nxt_ub',
        'deputy_csc',
        'assistant_csc',
        'clerk_superior_court',
        'im_by',
        'im_date',
        'auction',
        'nos_by',
        'nos_date',
        'im_checked_by',
        'im_checker_date',
        'auction_by',
        'auction_date',
        'deleted_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function im() {
        return $this->belongsTo(User::class, "im_by", "id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function nos() {
        return $this->belongsTo(User::class, "nos_by", "id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function im_checked() {
        return $this->belongsTo(User::class, "im_checked_by", "id")->select(["users.id","users.first_name","users.last_name"]);
    }
    
    public function auction() {
        return $this->belongsTo(User::class, "auction_by", "id")->select(["users.id","users.first_name","users.last_name"]);
    }
    public function notes() {
        return $this->hasOne(SaleBidderNotesModel::class, "bidder_id", "bidder_id");
    }

    public function document_bidder() {
        return $this->hasMany(DocumentBidderModel::class, "bidder_id", "bidder_id");
    }
}
