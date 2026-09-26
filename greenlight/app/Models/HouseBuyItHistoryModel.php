<?php
/**
 * Created By Rativardhan Singh Sengar  3/25/19 11:21 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/25/19 11:21 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class HouseBuyItHistoryModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'house_buyit_history';
    protected $primaryKey = 'house_buyit_id';
    protected $fillable = [
        'request_type', 'house_buyit_id', 'request_type', 'user_id', 'house_id', 'position', 'notes', 'question', 'status'
    ];

    protected $hidden = [
    ];

    protected $dateFormat = 'U';
    public $timestamps = true;
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

}
