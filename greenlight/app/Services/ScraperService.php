<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:52 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/12/19 9:51 PM
 */

namespace App\Services;


use App\Models\PropertyModel;
use App\Models\SaleDetailsModel;
use App\Models\SaleBidderModel;
use App\Models\MortgageLiensModel;
use App\Models\SaleDetailsDescriptionsModel;
use App\Models\SaleTrusteeNotesModel;

use Log;

class ScraperService
{
    private $findOneById;
    private $findAllByHouseId;
    private $userService;
    private $alarmMeService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AlarmMeService $alarmMeService)
    {
        Log::info("ScraperService: __construct called");
        $this->alarmMeService                 = $alarmMeService;
    }

    /**
     * @param $address
     * @param bool $is_cache
     * @return object
     */
    function propertyAddress($address,$state='')
    {
        Log::info("ScraperService: propertyAddress called");

        $find_short_address = config('constants.find_short_address');
        $replace_detail_address = \config('constants.replace_detail_address');

        $full_replace_count1 = 0;
        $full_phrase = str_ireplace($find_short_address, $replace_detail_address, $address, $full_replace_count1);

        $small_replace_count2 = 0;
        $small_phrase = str_ireplace($replace_detail_address, $find_short_address, $address, $small_replace_count2);


        $propertyObj = PropertyModel::where('address', 'like', "$address%");

        if ($full_replace_count1 > 0) {
            $propertyObj->orWhere("address", "like", "$full_phrase%");
        }
        if ($small_replace_count2 > 0) {
            $propertyObj->orWhere("address", "like", "$small_phrase%");
        }
        if (!empty($state)) {
            $propertyObj->where("state", $state);
        }

        return $propertyObj;
    }

    function storeSaleDetail($houseId, $saleDetail)
    {
        Log::info("ScraperService: storeSaleDetail called");
        $sale_detail_info = SaleDetailsModel::where('house_id', $houseId)->where('sale_date', $saleDetail['sale_date']);


        $saleDetail['trustee_scraped'] = time();

        if ($sale_detail_info->count() > 0) {
            $sale_detail = $sale_detail_info->first();

            if(!empty($sale_detail->book) && !empty($sale_detail->page_number)){
                unset($saleDetail['book']);unset($saleDetail['page_number']);
            }
            $sale_detail->update($saleDetail);
            $saleInfo = $sale_detail;
        } else {
            $saleInfo = SaleDetailsModel::create($saleDetail);
            $this->alarmMeService->sendEmailAlarmSettingUser($houseId);
        }

        return $saleInfo;
    }

    function getSaleDate($houseId,$saleDetail){
        $is_sale_date_exist=false;        
        $sale_detail = SaleDetailsModel::where('house_id', $houseId)->where('sale_date', $saleDetail['sale_date']);
        if ($sale_detail->count() > 0) {
            $is_sale_date_exist= true;
        }
        return $is_sale_date_exist;
    }
    /**
     * @param $idSaleDetailsDescriptionsModel
     * @param bool $is_cache
     * @return mixed
     */
    public function findOneById($id)
    {
        Log::info("ScraperService: findOneById called");

        $this->findOneById = PropertyModel::find($id);
        return $this->findOneById;
    }


    function mortgageLiens($mortgage)
    {
        Log::info("ScraperService: mortgageLiens called");

        $mortgage_info = MortgageLiensModel::where('house_id', $mortgage['house_id'])->where('dt_book_page', $mortgage['dt_book_page']);

        if ($mortgage_info->count() > 0) {
            $mortgage_info->first()->update($mortgage);
        } else {
            MortgageLiensModel::create($mortgage);
        }

    }

    function bidderDetail($bid_detail)
    {
        Log::info("ScraperService: bidderDetail called");
        $bid_detail_info = SaleBidderModel::
            where('sale_id', $bid_detail['sale_id'])
            ->where('house_id', $bid_detail['house_id']);

        if(!empty($bid_detail['bid_date'])){
            $bid_detail_info->where('bid_date', $bid_detail['bid_date']);
        }
       
        if ($bid_detail_info->count() > 0) {
            $bid_detail_info->first()->update($bid_detail);
        } else {
            SaleBidderModel::create($bid_detail);
        }
    }

    function saleBeforeNotes($saleDesc){
        $sale_id=$saleDesc['sale_id'];
        SaleTrusteeNotesModel::updateOrCreate(
        [
        'sale_id'=>$sale_id,
        'before_sale_trustee_notes'=>$saleDesc['before_sale_trustee_notes']
        ],
        $saleDesc);
        
        }
    

    function saleAfterNotes($saleDesc){
        $sale_id=$saleDesc['sale_id'];
        SaleTrusteeNotesModel::updateOrCreate(
        [
        'sale_id'=>$sale_id,
        'after_sale_trustee_notes'=>$saleDesc['after_sale_trustee_notes']
        ],
        $saleDesc);
        
    }


    function propertyForecloseAddress($address,$state='',$parcel_id1="")
    {
        Log::info("ScraperService: propertyAddress called");

        $find_short_address = config('constants.find_short_address');
        $replace_detail_address = \config('constants.replace_detail_address');

        $full_replace_count1 = 0;
        $full_phrase = str_ireplace($find_short_address, $replace_detail_address, $address, $full_replace_count1);

        $small_replace_count2 = 0;
        $small_phrase = str_ireplace($replace_detail_address, $find_short_address, $address, $small_replace_count2);


        $propertyObj = PropertyModel::where('address', 'like', "$address%");

        if ($full_replace_count1 > 0) {
            $propertyObj->orWhere("address", "like", "$full_phrase%");
        }
        if ($small_replace_count2 > 0) {
            $propertyObj->orWhere("address", "like", "$small_phrase%");
        }
        if (!empty($state)) {
            $propertyObj->where("state", $state);
        }
        if (!empty($parcel_id1)) {
            $propertyObj->where("parcel_id1", $parcel_id1);
        }

        return $propertyObj;
    }



}
