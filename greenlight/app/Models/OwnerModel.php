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


class OwnerModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'owner_info';

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
        'house_id','full_name', 'full_address', 'email', 'phone', 'phone2'
        , 'deed_bp_instrument', 'deed_recorded_date', 'beenverified_url','pacer_url',
        'is_check_marck','current_owner','current_owner_address','owner_percentage','warranty_deed_inst','wdeed_signed_date'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function borrower_info()
    {
        return $this->hasMany(BorrowerModel::class,"house_id","house_id");
    }

    public function owner_document()
    {
        return $this->hasMany(DocumentOwnerModel::class,"house_id","house_id");
    }

    public function borrower_document()
    {
        return $this->hasMany(DocumentBorrowerModel::class,"house_id","house_id");
    }

    public function social_media_info()
    {
        return $this->hasMany(OwnerSocailMediaModel::class,"owner_id","id");
    }
}
