<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;


class BurnRateModelDetail extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'burn_rate_detail';
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['house_id', 'estimate_a_to_b_date','estimate_b_to_c_date','actual_a_to_b_date','actual_b_to_c_date'];

   
}
