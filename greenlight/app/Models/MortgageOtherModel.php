<?php
/**
 * Created By Rativardhan Singh Sengar  1/17/19 12:45 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/17/19 12:44 AM
 */

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class MortgageOtherModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'mortgage_other';

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
        'house_id', 'lender', 'lien_amount', 'date_recorded', 'book_page_assignment_bp', 'assignment_bp',
        'redemption_info','redemption_notice','redemption_date','red_by_owner','redemption_expires','prop_sign_owner_1', 'prop_sign_owner_2', 'prop_sign_owner_3', 'prop_sign_owner_4', 'company_not_ct_rcd', 'dtc_first_check', 'dca_second_check',
        'dca_final_check',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function mortgage_other_document()
    {
        return $this->hasMany(DocumentMortgageOther::class,"house_id","house_id");
    }

    public function compSection(){
        return $this->hasMany(CompSectionHistoryModel::class,"foreign_id","house_id")->whereIn("section_type",["other"]);
    }

}
