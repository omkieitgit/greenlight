<?php
/**
 * Created By Rativardhan Singh Sengar  05/10/2020 5:48 PM
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPaymentLog extends Model
{
    public $timestamps = true;
    protected $dateFormat = 'U';
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $table = 'user_payment_log';
    protected $primaryKey = 'id';

    # you should use either  $fillable or $guarded - not both
    //    protected $guarded = ['house_id'];


    protected $fillable = [
        'user_id', 'status', 'amount', 'error_code', 'response', 'payment_type'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
