<?php
/**
 * Created By Rativardhan Singh Sengar  1/18/19 1:09 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/17/19 1:03 AM
 */

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class MortgageHoaModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'mortgage_hoa';

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
        'house_id', 'defective_notice_hoa', 'hoa_lien_foreclosing', 'manual_search_hoa', 'no_str', 'hoa_name', 'hoa_lien_amount', 'date_of_hoa_lien', 'hoa_lien_book_page', 'str_date', 'str_book_page', 'trustee_hoa', 'prop_sign_owner_1', 'prop_sign_owner_2', 'prop_sign_owner_3', 'prop_sign_owner_4', 'company_not_ct_rcd', 'dtc_first_check', 'dca_second_check',
        'dca_final_check','dcc_rs_instrument','dcc_rs_date','judgement_record',
        'redemption_info','redemption_notice','redemption_date','red_by_owner','redemption_expires','tax_code',
        'foreclosure_result','trdeep_instrument','trdeed_date','winning_bidder','winning_bid','instrument','affidavit_date','total_debt','es_excess_funds'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function mortgage_hoa_document()
    {
        return $this->hasMany(DocumentMortgageHoa::class,"house_id","house_id");
    }

    public function check_by()
    {
        return $this->hasMany(MortgageHoaCheckbyModel::class,"house_id","house_id");
    }

    public function compSection(){
        return $this->hasMany(CompSectionHistoryModel::class,"foreign_id","house_id")->whereIn("section_type",["HOA"]);
    }
}
