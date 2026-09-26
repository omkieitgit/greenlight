<?php
namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;
use DB;

class McdLenderModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
     //
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

    protected $table = 'mcd_lender';

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
        'house_id', 'lender_name', 'percentage'
        , 'agreement_org_name', 'agreement_store_name'
        , 'artical_org_name', 'artical_store_name'
        , 'created_at', 'updated_at','ein_store_name','ein_org_name',
        'deposit_return_no','wiriing_routing_no','bank_ac_no','bank_name'
        ,'is_lender','is_craig_per'
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
    protected $appends = ['url','artical_url','ein_url'];


    public function getUrlAttribute()
    {

        if(empty($this->attributes['agreement_store_name']) && empty($this->attributes['agreement_store_name']))
            return '';

        $store_file_name1 = $this->attributes['agreement_store_name'];
        $url['agreement_store_name'] =url('document/document_agreement/' . $store_file_name1);
        //print_r($url);
        return $url;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public function getArticalUrlAttribute()
    {

        if(empty($this->attributes['artical_store_name']) && empty($this->attributes['artical_store_name']))
            return '';

        $store_file_name1 = $this->attributes['artical_store_name'];
        $url['artical_store_name'] = url('document/document_article/' . $store_file_name1);
        //print_r($url);
        return $url;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public function getEinUrlAttribute()
    {

        if(empty($this->attributes['ein_store_name']) && empty($this->attributes['ein_store_name']))
            return '';

        $store_file_name1 = $this->attributes['ein_store_name'];
        $url['ein_store_name'] = url('document/document_ein/' . $store_file_name1);
        //print_r($url);
        return $url;
    }

    public function invenstorInfo(){
        return $this->hasMany(McdModel::class,"lender_name","id")
        ->select(['lender_name',
                    DB::raw('sum(house_mcd.amount) as amount'),
                    DB::raw('sum(house_mcd.returned_amount) as returned_amount'),
                    DB::raw('sum(house_mcd.pts) as pts'),
                    DB::raw('sum(house_mcd.interest) as interest'),
                    DB::raw('sum(house_mcd.lender_referral) as lender_referral')])
        ->groupBy('lender_name');

    }

    
}