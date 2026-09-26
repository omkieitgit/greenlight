<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class DriveListModel extends Model implements AuthenticatableContract, AuthorizableContract
{
  use Authenticatable, Authorizable;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    //protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'drive_list';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'drive_list_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
         'house_id', 'drive_list_id','drive_list_name','status','created_at','updated_at'
    ];

    public function drive_list_detail()
    {
        return $this->hasMany(DriveListDetailModel::class,"drive_list_id","drive_list_id");
    }
}