<?php
/**
 * Created By Rativardhan Singh Sengar  4/14/19 8:41 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/25/19 11:47 PM
 */

namespace App\Models;

use App\Helpers\CommonHelper;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class InviteModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $dateFormat = 'U';
    protected $table = 'invitations_info';
    protected $primaryKey = 'invitations_info_id';
    protected $fillable = [
        'invitations_info_id', 'house_id', 'address', 'invitee_email', 'invitee_subject', 'invitee_message', 'invitee_to', 'invitee_from', 'created_at', 'updated_at','deleted_by','is_deleted'
    ];
    public $timestamps = true;
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    public function getInviteeEmailAttribute()
    {
        if(empty($this->attributes['invitee_email']))
            return '';

        //return $this->attributes['invitee_email'];
        return CommonHelper::maskEmail($this->attributes['invitee_email']);
    }
    


    public function invitee_to()
    {
        return $this->belongsTo(User::class,"invitee_to","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function invitee_from()
    {
        return $this->belongsTo(User::class,"invitee_from","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id");
    }

    # START : Only Rativardhan will modify this model, don't do this. it will mess up some code.
    public function sale_details()
    {
        return $this->hasMany(SaleDetailsModel::class,"house_id","house_id");
    }

    public function mortgage_liens()
    {
        return $this->hasMany(MortgageLiensModel::class,"house_id","house_id");
    }

    public function geo()
    {
        return $this->hasOne(GeoModel::class,"house_id","house_id");
    }

    public function last_sale_details()
    {
        return $this->hasOne(SaleDetailsModel::class,"house_id","house_id")->whereNotNull('sale_date')->orderBy("sale_id",'desc');
    }
    public function last_cma_arv_recommendations()
    {
        return $this->hasOne(CmaArvModel::class,"house_id","house_id")->whereIn('info_added_by',['first_dtc','second_dca','third_dca'])->orderBy('info_added_by','desc');
    }

    public function first_liens()
    {
        return $this->hasOne(MortgageLiensModel::class,"house_id","house_id")->where('lien_type',1);
    }
    public function front_picture()
    {
        return $this->hasOne(DocumentPictureModel::class,"house_id","house_id");
    }
    # END: Only Rativardhan will modify this model, don't do this. it will mess up some code.

}
