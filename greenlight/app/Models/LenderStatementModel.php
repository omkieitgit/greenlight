<?php namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;


class LenderStatementModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'lender_statement';

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
        'house_id', 'statement_date','transaction','payee'
        , 'description', 'deposit'
        , 'payment', 'balance'
        ,'doc_org_name','doc_store_name'
        , 'created_at', 'updated_at'
        
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
    protected $appends = ['url'];


    public function getUrlAttribute()
    {

        if(empty($this->attributes['doc_store_name']) && empty($this->attributes['doc_store_name']))
            return '';

        $store_file_name1 = $this->attributes['doc_store_name'];
        $url['doc_store_name'] = url('document/document_lender_statement/' . $store_file_name1);
        //print_r($url);
        return $url;
    }

}

