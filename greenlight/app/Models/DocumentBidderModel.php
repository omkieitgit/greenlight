<?php
/**
 * Created By Rativardhan Singh Sengar  9/22/19 10:34 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/22/19 3:41 PM
 */

namespace App\Models;

use App\Helpers\CommonHelper;
use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class DocumentBidderModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'document_bidder';

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
        'bidder_id','added_by', 'document_type', 'org_name', 'store_name', 'other_name', 'case_number'
        , 'document_date','created_at'
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
    protected $appends = ['url'];


    public function getUrlAttribute()
    {
        return CommonHelper::getPublicMediaURl($this->attributes['store_name'], 'document_bidder/');
    }


    public function bidder()
    {
        ## ToDo: Verify this , thisi s wrong it should be BidderModel verify and remove
        return $this->belongsTo(DocumentBidderModel::class,"bidder_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class,'added_by','id')->select(["users.id","users.first_name","users.last_name"]);
    }
}
