<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class OwnerBorrowerInfoModel extends Model implements AuthenticatableContract, AuthorizableContract
{
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

  protected $table = 'owner_borrower_info';

  /**
   * Indicates model primary keys.
   */
  protected $primaryKey = 'house_id';

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  //protected $guarded = ['house_id'];
  protected $fillable = [
      'house_id','is_owner_same_property_address','is_borrower_same_owner_name','is_borrower_same_owner_address',
      'no_pacer_result'
  ];
  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
  ];

  public function owner_borrower_info_document()
  {
      return $this->hasMany(DocumentOwnerBorrowerInfo::class,"house_id","house_id");
  }
}
