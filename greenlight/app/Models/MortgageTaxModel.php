<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;



class MortgageTaxModel extends Model implements AuthenticatableContract, AuthorizableContract
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

  protected $table = 'mortgage_tax';

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
      'house_id', 'tax_lien_foreclosing', 'defective_notice_tax', 'tax_name', 'tax_lien_amount', 'date_of_tax_lien', 'tax_lien_instrument', 'tax_lien_cause', 'sheriff_tax',
      'tax_code','redemption_info','redemption_notice','redemption_date','red_by_owner','redemption_expires','prop_sign_owner_1', 'prop_sign_owner_2', 'prop_sign_owner_3', 'prop_sign_owner_4', 'company_not_ct_rcd', 'dtc_first_check', 'dca_second_check',
      'dca_final_check','foreclosure_result','trdeep_instrument','trdeed_date','winning_bidder','winning_bid','instrument','created_at','updated_at','es_excess_funds'
  ];
  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
  ];

  public function mortgage_tax_document()
  {
      return $this->hasMany(DocumentMortgageTax::class,"house_id","house_id");
  }
  public function compSection(){
    return $this->hasMany(CompSectionHistoryModel::class,"foreign_id","house_id")->whereIn("section_type",["tax"]);
}
}
