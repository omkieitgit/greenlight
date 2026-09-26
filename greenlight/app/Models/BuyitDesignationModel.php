<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class BuyitDesignationModel extends Model implements AuthenticatableContract, AuthorizableContract
{

    use Authenticatable, Authorizable;
    public $timestamps = false;
    protected $table = 'buyit_designation';
    protected $primaryKey = 'id';
    protected $fillable = ['house_buyit_id','house_id', 'user_id','house_id', 'designation','user_status'];
}