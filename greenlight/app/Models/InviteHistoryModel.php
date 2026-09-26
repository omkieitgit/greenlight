<?php
/**
 * Created By Rativardhan Singh Sengar  4/15/19 9:48 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/14/19 11:48 PM
 */

namespace App\Models;

use App\Helpers\CommonHelper;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class InviteHistoryModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $dateFormat = 'U';
    protected $table = 'invitations_info_history';
    protected $primaryKey = 'invitations_info_id';
    protected $fillable = [
        'invitations_info_id', 'house_id', 'address', 'invitee_email', 'invitee_subject', 'invitee_message', 'invitee_to', 'invitee_from', 'created_at', 'updated_at'

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
}
