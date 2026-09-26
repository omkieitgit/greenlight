<?php
/**
 * Created By Rativardhan Singh Sengar  9/30/19 11:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 9/30/19 11:23 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class SaleBidderNotesModel extends Model implements AuthenticatableContract, AuthorizableContract {
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

    protected $table = 'sale_bidder_notes';

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
        'bidder_id',
        'notes'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

}
