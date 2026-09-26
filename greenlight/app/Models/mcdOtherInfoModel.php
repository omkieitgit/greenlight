<?php

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;


class mcdOtherInfoModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    public $timestamps = true;
    protected $dateFormat = 'U';
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
   
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'mcd_other_info';

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
        "house_id",	"client_id","payers_id","created_at","updated_at","is_sold"
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    

    public function client_info()
    {
        return $this->hasOne(ClientInfoModel::class,"id","client_id");
    }
    public function payers_info()
    {
        return $this->hasOne(PayersInfoModel::class,"id","payers_id");
    }

    public function payout(){
        return $this->hasOne(PayoutModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
    }

    function property_info(){
        return $this->hasOne(PropertyModel::class,"house_id","house_id"); 
    }

    public function property_lender(){
        return $this->hasMany(McdLenderModel::class,"house_id","house_id")->where('is_lender','1')->orWhere('is_craig_per','1');
    }
  

}
