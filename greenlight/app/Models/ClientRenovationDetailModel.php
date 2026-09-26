<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;


class ClientRenovationDetailModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    //use SoftDeletes;

    protected $table = 'client_renovation_detail';
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];
    // /**
    //  * The attributes excluded from the model's JSON form.
    //  *
    //  * @var array
    //  */
    // protected $hidden = [
    //     'deleted_at'
    // ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['house_id', 'invoice_id', 'sub_category','amount','description','classification'];


    public function category()
    {
        return $this->hasOne(RenovationCategoryModel::class,"id","sub_category");
    }

    public function recipient()
    {
        return $this->hasOne(Form1099MiscModel::class,"client_renovation_detail_id","id")->select(['id','client_renovation_detail_id','recipients_id']);
    }

    public function homebuyer_reno_category()
    {
        return $this->hasOne(HomebuyerRenoCategoryModel::class,"category_id","sub_category");
    }
}
