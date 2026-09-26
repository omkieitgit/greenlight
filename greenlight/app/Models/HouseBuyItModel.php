<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:19 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/25/18 6:44 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class HouseBuyItModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes,Authenticatable, Authorizable;
    public $timestamps = false;
    protected $table = 'house_buyit';
    protected $dateFormat = 'U';
    protected $primaryKey = 'house_buyit_id';
    protected $fillable = [
        'house_buyit_id','request_type', 'user_id','house_id', 'position', 'notes', 'question', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function setUpdatedAtAttribute($date)
    {
        $this->attributes['updated_at'] = strtotime($date);
    }

    public function setCreatedAtAttribute($date)
    {
        $this->attributes['created_at'] = strtotime($date);
    }

    public function buyIt(){
        return $this->belongsTo(BuyitDesignationModel::class,"house_buyit_id","house_buyit_id")->where('designation','buyer')->where('user_status','active');
    }

    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function last_sale_details()
    {
        return $this->hasOne(SaleDetailsModel::class,"house_id","house_id")->whereNotNull('sale_date')->orderBy("sale_date",'desc');
    }
}
