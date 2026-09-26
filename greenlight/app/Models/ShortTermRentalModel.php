<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class ShortTermRentalModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'short_term_rental';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id', 'property_number', 'guest_name'
        , 'check_in_date', 'check_out_date'
        , 'property_description', 'amount_deposit'
        , 'account_deposited', 'rental_doc_org_name'
        , 'rental_doc_store_name', 'amount_received' ,'link','rental_type','deposite_link'        
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['created_at'] = date('Y-m-d H:i:s');
    }

    

    // public function mcdLender() {
    //     return $this->belongsTo(McdLenderModel::class, "lender_name", "id")->select(["mcd_lender.lender_name","mcd_lender.id"]);
    // }


}
