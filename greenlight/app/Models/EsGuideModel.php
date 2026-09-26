<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class EsGuideModel extends Model implements AuthenticatableContract, AuthorizableContract
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

  protected $table = 'es_guide';

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
      'id','title','description','created_at','updated_at'
  ];
  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
  ];
}
