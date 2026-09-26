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


class ContactRequestModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $dateFormat = 'U';
    protected $table = 'contact_request';
    protected $primaryKey = 'id';
    protected $fillable = [
         'house_id', 'user_id', 'request_type', 'position', 'is_deleted', 'created_at', 'updated_at'

    ];
    public $timestamps = true;
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    
}
