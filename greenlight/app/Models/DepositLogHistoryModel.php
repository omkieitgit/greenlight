<?php
/**
 * Created By Rativardhan Singh Sengar  5/28/19 8:09 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/1/19 1:21 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class DepositLogHistoryModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'deposit_log_history';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'deposit_log_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
         'house_id', 'user_id', 'turn_around_time', 'rate_of_return', 'renovation_risk', 'funding_deposit', 'loan_current', 'needed_for_deposit', 'needed_for_renovation', 'miscelainous_fees', 'estimated_values_ab', 'estimated_values_bc', 'full_scope_work', 'full_material_list', 'your_timeline', 'purchased_over', 'flip_transactions', 'rehab_currently', 'p1_value', 'p1_adom', 'p2_value', 'p2_adom', 'p3_adom', 'p3_value', 'wholetail_value', 'rental_rate', 'deposit_token', 'loan_type', 'deposit_notes', 'created_at', 'updated_at'
    ];

    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }
}
