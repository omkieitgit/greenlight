<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\EmailSettingsModel;
use Log;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Barryvdh\DomPDF\PDF;

class UrlPdfCsvExcelService {

    public  $userService;
    public  $propertyService;
    public  $houseBuyItService;
    public  $houseTokenService;
    private $disk;
    private $pdf;

    public function __construct(UserService $userService, PropertyService $propertyService, HouseBuyItService $houseBuyItService, HouseTokenService $houseTokenService, PDF $pdf

    ) {
        Log::info("UrlPdfCsvExcelService: __construct called");
        $this->userService       = $userService;
        $this->propertyService   = $propertyService;
        $this->houseBuyItService = $houseBuyItService;
        $this->houseTokenService = $houseTokenService;
        $this->pdf               = $pdf;

        $this->disk = Storage::disk('public');
        #ToDO: make a seprate cotroller and return binary data with PDf format,
        # No need to save this file in server any more.

    }

    function houseLinkArray($info) {
        Log::info("UrlPdfCsvExcelService: houseLinkArray called");

        $temp     = [];
        $temp[]   = "Property URL";
        $houseAll = $this->propertyService->findMany(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return $temp;
        }

        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);
            $temp[]      = $address_url;
        }

        return $temp;
    }

    function house40DetailsArray($info) {
        Log::info("UrlPdfCsvExcelService: house40DetailsArray called");
        $temp = [];

        $houseAll = $this->propertyService->find40Details(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return $temp;
        }

        $header   = [];
        $header[] = "Address";
        $header[] = "City";
        $header[] = "County";
        $header[] = "State";
        $header[] = "Zip";
        $header[] = "Living SQFT";
        $header[] = "Bed";
        $header[] = "Bath";
        $header[] = "Lot Size";
        $header[] = "Parcel ID";
        $header[] = "Subdivision";
        $header[] = "Trustee";
        $header[] = "Sale Date";
        $header[] = "Sale Time";
        $header[] = "CASE #";
        $header[] = "Precinct #";
        $header[] = "Opening Bid";
        $header[] = "County Value";
        $header[] = "CMA/ARV";
        $header[] = "Zestimate";
        $header[] = "GSD";
        $header[] = "SSD";
        $header[] = "Sale Type";
        $header[] = "First Lien Amount";
        $header[] = "First Lien Date";
        $header[] = "Foreclosing First Lien";
        $header[] = "Min Amount To UPSET Bid";
        $header[] = "Last Date To UPSET Bid";
        $header[] = "Winning Bidder";
        $header[] = "Winning Bid";
        $header[] = "NOS BY";
        $header[] = "NOS DATE";
        $header[] = "DTC By";
        $header[] = "DTC DATE";
        $header[] = "PROPERTY CLOSE A TO B:";
        $header[] = "A TO B BONUS PAID DATE";
        $header[] = "Property Description";
        $temp[]   = $header;

        foreach ($houseAll as $key => $value) {
            $parse_data                      = [];
            $parse_data['address']           = CommonHelper::emptyDefault($value->address);
            $parse_data['city']              = CommonHelper::emptyDefault($value->city);
            $parse_data['county']            = CommonHelper::emptyDefault($value->county);
            $parse_data['state']             = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']               = CommonHelper::emptyDefault($value->zip);
            $parse_data['total_living_sqft'] = CommonHelper::emptyNumberDefault($value->total_living_sqft);
            $parse_data['bed']               = CommonHelper::emptyDefault($value->bed);
            $parse_data['bath']              = CommonHelper::emptyDefault($value->bath);
            $parse_data['lot_acreage_sf']    = CommonHelper::emptyNumberDefault($value->lot_acreage_sf);
            $parse_data['parcel_id1']        = CommonHelper::emptyDefault($value->parcel_id1);
            // $parse_data['parcel_id2']             = CommonHelper::emptyDefault($value->parcel_id2);
            $parse_data['subdivision'] = CommonHelper::emptyDefault($value->subdivision);

            // $parse_data['year_built']             = CommonHelper::emptyDefault($value->year_built);
            $parse_data['trustee']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['sale_date']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['sale_time']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            $parse_data['case_number']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['priceint']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'priceint', 'number');
            $parse_data['opening_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['county_value']           = CommonHelper::emptyMoneyDefault(@$value->county_value);
            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['zestimates']             = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'rents_zestimate', '');
            $parse_data['gsd']                    = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'general_demand', '');
            $parse_data['ssd']                    = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'specific_demand', '');
            $enum                                 = [0 => "No",
                                                     1 => "Yes"];
            $flf                                  = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_foreclosing', '');
            $sale_type_array                      = config('property_information.sale_type');
            $sale_type                            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']              = @$sale_type_array[@$sale_type];
            $parse_data['first_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['first_lien_foreclosing'] = @$enum[@$flf];
            $parse_data['min_amt_nxt_ub']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'min_amt_nxt_ub', 'currency');
            $parse_data['last_date_to_upset_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'last_date_to_upset_bid', 'date');
            $parse_data['wining_bidder']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['nos_by']                 = CommonHelper::nameFormat(@$value->last_sale_details->nos);
            $parse_data['nos_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'nos_date', 'date');
            $parse_data['dtc_by']                 = CommonHelper::nameFormat(@$value->first_dtc->user);
            $parse_data['dtc_date']               = CommonHelper::emptyDefaultObject(@$value->first_dtc, 'date', 'date');
            $parse_data['pcab']                   = "";
            $parse_data['abbonus']                = "";
            $parse_data['property_description']   = CommonHelper::emptyDefault(@$value->property_description);
            $temp[]                               = $parse_data;
        }

        return $temp;
    }

    function houseAcquistionInfoArray($info) {
        Log::info("UrlPdfCsvExcelService: houseAcquistionInfoArray called");
        $temp   = [];
        $header = [];

        $header[] = 'Sp Number';
        $header[] = 'Borrower';
        $header[] = 'Address';
        $header[] = 'City';
        $header[] = 'State';
        $header[] = 'County';
        $header[] = 'Zip';
        $header[] = 'Sale Time';
        $header[] = 'Sale Date';
        $header[] = 'Total Living';
        $header[] = 'Year Built';
        $header[] = 'Bed';
        $header[] = 'Bath';
        $header[] = 'Lot Size';
        $header[] = 'Trustee';
        $header[] = 'Opening Bid';
        $header[] = 'Deposit Required to Upset';
        $header[] = 'CMA/ARV';
        $header[] = 'CMA/ARV';
        $header[] = 'CMA/ARV Date';
        $header[] = 'Zestimate';
        $header[] = 'Winning Bidder';
        $header[] = 'Winning Bid';
        $header[] = 'Net Spread Amount';
        $header[] = '1st Lien Bank';
        $header[] = 'Foreclosing Lien 1st Lien';
        $header[] = 'Date of 1st Lien';
        $header[] = '1st Lien Amount';
        $header[] = '2nd Lien Bank';
        $header[] = 'Foreclosing Lien 2nd Lien';
        $header[] = 'Date of 2nd Lien';
        $header[] = '2nd Lien Amount';
        $header[] = '3rd Lien Bank';
        $header[] = 'Foreclosing Lien 3rd Lien';
        $header[] = 'Date of 3rd Lien';
        $header[] = '3rd Lien Amount';
        $header[] = 'Other Lien Bank';
        //$header[] = 'Other Lien 3rd Lien';
        $header[] = 'Date of Other Lien';
        $header[] = 'Other Lien Amount';
        $header[] = 'Property Rate of Return Estimated';
        $header[] = 'Property Rate of Return Actual';
        $header[] = 'TS#';
        $header[] = 'Trustee Phone#';
        $header[] = 'Supply and Demand';
        $header[] = 'Average Days On Market';
        $header[] = 'Schools';
        $header[] = '';

        $temp[] = $header;


        $houseAll = $this->propertyService->findAcquistionDetails(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return $temp;
        }


        foreach ($houseAll as $key => $value) {
            $parse_data                            = [];
            $enum                                  = [0 => "No",
                                                      1 => "Yes"];
            $flf                                   = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_foreclosing', '');
            $slf                                   = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_foreclosing', '');
            $tlf                                   = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_foreclosing', '');
            $sale_type_array                       = config('property_information.sale_type');
            $sale_type                             = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['case_number']             = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['borrower']                = CommonHelper::emptyDefault(@$value->last_borrower_info->full_name);
            $parse_data['address']                 = CommonHelper::emptyDefault($value->address);
            $parse_data['city']                    = CommonHelper::emptyDefault($value->city);
            $parse_data['state']                   = CommonHelper::emptyDefault($value->state);
            $parse_data['county']                  = CommonHelper::emptyDefault($value->county);
            $parse_data['zip']                     = CommonHelper::emptyDefault($value->zip);
            $parse_data['sale_time']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            $parse_data['sale_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['total_living_sqft']       = CommonHelper::emptyNumberDefault($value->total_living_sqft);
            $parse_data['year_built']              = CommonHelper::emptyDefault($value->year_built);
            $parse_data['bed']                     = CommonHelper::emptyDefault($value->bed);
            $parse_data['bath']                    = CommonHelper::emptyDefault($value->bath);
            $parse_data['lot_acreage_sf']          = CommonHelper::emptyNumberDefault($value->lot_acreage_sf);
            $parse_data['trustee']                 = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['opening_bid']             = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['deposit_upset']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'deposit_upset', 'currency');
            $parse_data['cma_arv']                 = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['cma_arv1']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['cma_arv_date']            = "cma_arv_date";
            $parse_data['zestimates']              = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'rents_zestimate', '');
            $parse_data['wining_bidder']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid']             = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['net_spread_amount']       = "net_spread_amount";
            $parse_data['first_lender']            = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lender', '');
            $parse_data['first_lien_foreclosing']  = @$enum[@$flf];
            $parse_data['first_date_recorded']     = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['first_lien_amount']       = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['second_lender']           = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lender', '');
            $parse_data['second_lien_foreclosing'] = @$enum[@$slf];
            $parse_data['second_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['second_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['third_lender']            = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lender', '');
            $parse_data['third_lien_foreclosing']  = @$enum[@$tlf];
            $parse_data['third_date_recorded']     = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['third_lien_amount']       = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['other_lender']            = CommonHelper::emptyDefaultObject(@$value->other_liens, 'lender', '');

            $parse_data['other_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->other_liens, 'date_recorded', 'date');
            $parse_data['other_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->other_liens, 'lien_amount', 'currency');
            $parse_data['est_prp_rate_of_return'] = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'est_prp_rate_of_return', 'percentage');
            $parse_data['act_prp_rate_of_return'] = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'act_prp_rate_of_return', 'percentage');
            $parse_data['ts_no']                  = 'ts_no';
            CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee_file_no');
            $parse_data['trustee_phone']      = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee_phone');
            $parse_data['supply_and_demand']  = 'supply_and_demand';
            $parse_data['est_days_on_market'] = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'est_days_on_market', 'number');
            $parse_data['school']             = 'school';

            //            $parse_data['parcel_id1']        = CommonHelper::emptyDefault($value->parcel_id1);
            //            $parse_data['parcel_id2']        = CommonHelper::emptyDefault($value->parcel_id2);
            //            $parse_data['subdivision']       = CommonHelper::emptyDefault($value->subdivision);
            //            $parse_data['priceint']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'priceint', 'number');
            //            $parse_data['county_value']      = CommonHelper::emptyMoneyDefault(@$value->county_value);
            //            $parse_data['gsd']               = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'general_demand', '');
            //            $parse_data['ssd']               = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'specific_demand', '');
            //            $parse_data['sale_type']         = @$sale_type_array[@$sale_type];
            //            $parse_data['min_amt_nxt_ub']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'min_amt_nxt_ub', 'currency');
            //            $parse_data['last_date_to_upset_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'last_date_to_upset_bid', 'date');
            //            $parse_data['nos_by']                 = CommonHelper::nameFormat(@$value->last_sale_details->nos);
            //            $parse_data['nos_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'nos_date', 'date');
            //            $parse_data['dtc_by']                 = CommonHelper::nameFormat(@$value->first_dtc->user);
            //            $parse_data['dtc_date']               = CommonHelper::emptyDefaultObject(@$value->first_dtc, 'date', 'date');
            //            $parse_data['property_description']   = CommonHelper::emptyDefault(@$value->property_description);

            $temp[] = $parse_data;
        }

        return $temp;
    }

    function houseTexasAuctionArray($info) {
        Log::info("UrlPdfCsvExcelService: houseTexasAuctionArray called");
        $temp   = [];
        $header = [];

        $header[] = 'Property Address';
        $header[] = 'County';
        $header[] = 'Specific Property Type';
        $header[] = 'Heated Area';
        $header[] = 'Legal Description';
        $header[] = 'Owner';
        $header[] = 'Owner Address';
        $header[] = '1st Lien $';
        $header[] = '1st Lien Date';
        $header[] = '1st Lien STR Y/N';
        $header[] = '2nd Lien $';
        $header[] = '2nd Lien Date';
        $header[] = '2nd Lien STR Y/N';
        $header[] = '3rd Lien $';
        $header[] = '3rd Lien Date';
        $header[] = '3rd Lien STR Y/N';
        $header[] = "Total Mortgage Lien";	
        $header[] = "Total Estimated Equity";
        $header[] = 'HOA Name';
        $header[] = 'HOA Lien $';
        $header[] = 'HOA Lien Date';
        $header[] = 'HOA Lien STR Y/N';
        $header[] = 'HOA Total Debt';
        $header[] = 'Total Est Equity';
        $header[] = 'Unpaid Taxes';
        $header[] = 'Opening Bid';
        $header[] = 'CMA/ARV';
        $header[] = 'Sale Type';
        $header[] = 'Sale Date';
        $header[] = 'Case Number';
        $header[] = 'Trustee';
        $header[] = 'URL';
        $header[] = 'Buyer';
        $header[] = 'Min Bid';
        $header[] = 'Max Bid';
        $header[] = 'Winning Bidder';
        $header[] = 'Winning Bid Amount';

        // $header[] = 'Sale Time';
        // $header[] = 'Loan Type';
        // $header[] = 'Sale Place';
        // $header[] = 'Precinct#';
       
        $header[] = '';
        $temp[] = $header;

        $houseAll = $this->propertyService->findTexasAuctionDetails(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return $temp;
        }
        $enum  = [0 => "No",1 => "Yes"];

        foreach ($houseAll as $key => $value) {
            $parse_data                            = [];
            $token       = $this->houseTokenService->token($value->house_id);
            $link        = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($value);
            $parse_data['Property Address'] = CommonHelper::emptyDefault($value->address);
            $parse_data['County'] = CommonHelper::emptyDefault($value->county);
            
            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');

            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['specific_property_type'] =$spPropertyType;
            $parse_data['heating'] = CommonHelper::emptyDefault(@$value->total_living_sqft);
            $parse_data['Legal Description'] = CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['Owner'] = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['Owner Address'] = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            $parse_data['1st Lien $'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['1st Lien Date'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['first_no_str_no_appt'] = $enum[CommonHelper::emptyNumberVal(@$value->first_liens, 'no_str_no_appt')];
            $parse_data['2nd Lien $'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['2nd Lien Date'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['second_no_str_no_appt'] = $enum[CommonHelper::emptyNumberVal(@$value->second_liens, 'no_str_no_appt')];
            $parse_data['3rd Lien $'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['3rd Lien Date'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['third_no_str_no_appt'] = $enum[CommonHelper::emptyNumberVal(@$value->third_liens, 'no_str_no_appt')];

            $total_lien = floatval(@$value->first_liens->lien_amount ) + floatval(@$value->second_liens->lien_amount ) + floatval(@$value->third_liens->lien_amount ) ;
            $parse_data['total_lien']                = CommonHelper::moneyFormat($total_lien);
            $total_equity = floatval(@$value->first_liens->est_equity ) + floatval(@$value->second_liens->est_equity ) + floatval(@$value->third_liens->est_equity ) ;
            $parse_data['total_equity']           = CommonHelper::moneyFormat($total_equity);
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            $parse_data['HOA Lien $'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['HOA Lien Date'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            $parse_data['hoa_no_str'] = $enum[CommonHelper::emptyNumberVal(@$value->hoa_liens, 'no_str')];
            $parse_data['total_debt']  = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'total_debt', '');
            $parse_data['Total Est Equity'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['unpaid_taxes']=$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['Opening Bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['CMA/ARV'] = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $sale_type_array         = config('property_information.sale_type');
            $sale_type               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['Sale Type']              = @$sale_type_array[@$sale_type];
            $parse_data['Sale Date'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['case_number'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['Trustee'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['Property URL'] = $link;
            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['min_bid'] = '';
            $parse_data['max_bid'] = '';
            $parse_data['Winning Bidder'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['Winning Bid Amount'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            // $parse_data['DCV#'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            // $parse_data['City'] = CommonHelper::emptyDefault($value->city);
            // $parse_data['Sale Time'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            
            // $loan_type = !empty(@$value->third_liens->loan_type) ? $value->third_liens->loan_type : (
            // !empty(@$value->second_liens->loan_type) ? $value->second_liens->loan_type : (
            // !empty(@$value->first->loan_type) ? $value->first->loan_type : '_ _'
            // )
            // );
            // $parse_data['Loan Type'] = CommonHelper::emptyDefault($loan_type);
            // $parse_data['Sale Place'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'sale_place', 'currency');
            // $parse_data['Precinct#'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'priceint', 'number');;

            $header[] = '';

            $temp[] = $parse_data;
        }

        return $temp;
    }

    function houseTexasAuctionPdf($info,$is_binary=false) {
        Log::info("UrlPdfCsvExcelService: houseTexasAuctionPdf called");
        $temp   = [];
        $header = [];

        $header[] = 'Property Address';
        $header[] = 'County';
        $header[] = 'Specific Property Type';
        $header[] = 'Heated Area';
        $header[] = 'Legal Description';
        $header[] = 'Owner';
        $header[] = 'Owner Address';
        $header[] = '1st Lien $';
        $header[] = '1st Lien Date';
        $header[] = '1st Lien STR Y/N';
        $header[] = '2nd Lien $';
        $header[] = '2nd Lien Date';
        $header[] = '2nd Lien STR Y/N';
        $header[] = '3rd Lien $';
        $header[] = '3rd Lien Date';
        $header[] = '3rd Lien STR Y/N';
        $header[] = "Total Mortgage Lien";	
        $header[] = "Total Estimated Equity";
        $header[] = 'HOA Name';
        $header[] = 'HOA Lien $';
        $header[] = 'HOA Lien Date';
        $header[] = 'HOA Lien STR Y/N';
        $header[] = 'HOA Total Debt';
        $header[] = 'Total Est Equity';
        $header[] = 'Unpaid Taxes';
        $header[] = 'Opening Bid';
        $header[] = 'CMA/ARV';
        $header[] = 'Sale Type';
        $header[] = 'Sale Date';
        $header[] = 'Case Number';
        $header[] = 'Trustee';
        $header[] = 'URL';
        $header[] = 'Buyer';
        $header[] = 'Min Bid';
        $header[] = 'Max Bid';
        $header[] = 'Winning Bidder';
        $header[] = 'Winning Bid Amount';

        // $header[] = 'Sale Time';
        // $header[] = 'Loan Type';
        // $header[] = 'Sale Place';
        // $header[] = 'Precinct#';
       
        $header[] = '';
        $temp[] = $header;

        $houseAll = $this->propertyService->findTexasAuctionDetails(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return $temp;
        }
        $enum  = [0 => "No",1 => "Yes"];
       
        $html = '<div style="text-align:center; color:red; font-size:22px;">Texas Auction Report</div><hr/>';

        foreach ($houseAll as $key => $value) {
            $parse_data = [];
            $token       = $this->houseTokenService->token($value->house_id);
            $link        = env("APP_FRONTEND") . 'quickview/' . $token . '/' . CommonHelper::url_slug($value);
            $parse_data['property_address'] = CommonHelper::emptyDefault($value->address);
            $parse_data['county'] = CommonHelper::emptyDefault($value->county);
            
            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');

            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['specific_property_type'] =$spPropertyType;
            $parse_data['heating'] = CommonHelper::emptyDefault(@$value->total_living_sqft);
            $parse_data['legal_description'] = CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['owner'] = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['owner_address'] = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            $parse_data['first_lien'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_lien_date'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['first_no_str_no_appt'] = $enum[CommonHelper::emptyDefaultObject(@$value->first_liens, 'no_str_no_appt')];
            $parse_data['second_lien'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_lien_date'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['second_no_str_no_appt'] = $enum[CommonHelper::emptyNumberVal(@$value->second_liens, 'no_str_no_appt')];
            $parse_data['third_lien'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_lien_date'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['third_no_str_no_appt'] = $enum[CommonHelper::emptyNumberVal(@$value->third_liens, 'no_str_no_appt')];

            $total_lien = floatval(@$value->first_liens->lien_amount ) + floatval(@$value->second_liens->lien_amount ) + floatval(@$value->third_liens->lien_amount ) ;
            $parse_data['total_lien']                = CommonHelper::moneyFormat($total_lien);
            $total_equity = floatval(@$value->first_liens->est_equity ) + floatval(@$value->second_liens->est_equity ) + floatval(@$value->third_liens->est_equity ) ;
            $parse_data['total_equity']           = CommonHelper::moneyFormat($total_equity);
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            $parse_data['HOA_lien'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['HOA_lien_date'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            $parse_data['hoa_no_str'] = $enum[CommonHelper::emptyNumberVal(@$value->hoa_liens, 'no_str')];
            $parse_data['total_debt']  = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'total_debt', '');
            $parse_data['total_est_equity'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['unpaid_taxes']=$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['opening_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['cma_arv'] = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $sale_type_array         = config('property_information.sale_type');
            $sale_type               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']              = @$sale_type_array[@$sale_type];
            $parse_data['sale_date'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['case_number'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['trustee'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['address_url'] = $link;
            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['min_bid'] = '';
            $parse_data['max_bid'] = '';
            $parse_data['winning_bidder'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid_amount'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            

            $html .= view('pdfs.texas_auction', $parse_data);
        }
        // echo $html;
        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }
        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);

        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function houseLinkPdf($info, $is_binary = false) {

        Log::info("UrlPdfCsvExcelService: houseLinkPdf called");

        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->findMany(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['header_logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $html                      = view('pdfs.header', $parse_data);

        $tr_html = '<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:\'Trebuchet MS\', Verdana, Arial;">';
        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);

            $tr_html .= '<tr>' . '<td >' . ' <a  target="_blank" href="' . $address_url . '">' . $address_url . '</a>' . ' </td>' . '</tr>';
        }

        $tr_html    .= "</table>";
        $html       = $html . $tr_html;
        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function house40DetailsPdf($info, $is_binary = false) {
        Log::info("UrlPdfCsvExcelService: house40DetailsPdf called");

        #ToDO: Make common function for this.
        // Common Code start
        $houseAll = $this->propertyService->find40Details(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['header_logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $html                      = view('pdfs.header', $parse_data);

        foreach ($houseAll as $key => $value) {
            $parse_data    = [];
            $house_picture = @$value->front_picture->store_name;

            $parse_data['img_src'] = CommonHelper::documentPictureStorage($house_picture);

            $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            $parse_data['city']                   = CommonHelper::emptyDefault($value->city);
            $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            $parse_data['state']                  = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']                    = CommonHelper::emptyDefault($value->zip);
            $parse_data['total_living_sqft']      = CommonHelper::emptyNumberDefault($value->total_living_sqft);
            $parse_data['bed']                    = CommonHelper::emptyDefault($value->bed);
            $parse_data['bath']                   = CommonHelper::emptyDefault($value->bath);
            $parse_data['lot_acreage_sf']         = CommonHelper::emptyNumberDefault($value->lot_acreage_sf);
            $parse_data['parcel_id1']             = CommonHelper::emptyDefault($value->parcel_id1);
            $parse_data['parcel_id2']             = CommonHelper::emptyDefault($value->parcel_id2);
            $parse_data['subdivision']            = CommonHelper::emptyDefault($value->subdivision);
            $parse_data['year_built']             = CommonHelper::emptyDefault($value->year_built);
            $parse_data['trustee']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['sale_date']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['sale_time']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            $parse_data['case_number']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['priceint']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'priceint', 'number');
            $parse_data['opening_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['county_value']           = CommonHelper::emptyMoneyDefault(@$value->county_value);
            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['zestimates']             = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'rents_zestimate', '');
            $parse_data['gsd']                    = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'general_demand', '');
            $parse_data['ssd']                    = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'specific_demand', '');
            $enum                                 = [0 => "No",
                                                     1 => "Yes"];
            $flf                                  = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_foreclosing', '');
            $sale_type_array                      = config('property_information.sale_type');
            $sale_type                            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']              = @$sale_type_array[@$sale_type];
            $parse_data['first_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['first_lien_foreclosing'] = @$enum[@$flf];
            $parse_data['min_amt_nxt_ub']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'min_amt_nxt_ub', 'currency');
            $parse_data['last_date_to_upset_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'last_date_to_upset_bid', 'date');
            $parse_data['wining_bidder']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['nos_by']                 = CommonHelper::nameFormat(@$value->last_sale_details->nos);
            $parse_data['nos_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'nos_date', 'date');
            $parse_data['dtc_by']                 = CommonHelper::nameFormat(@$value->first_dtc->user);
            $parse_data['dtc_date']               = CommonHelper::emptyDefaultObject(@$value->first_dtc, 'date', 'date');
            $parse_data['property_description']   = CommonHelper::emptyDefault(@$value->property_description);

            $html .= view('pdfs.40_details_email_list', $parse_data);
        }

        // echo $html;
        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }
        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);

        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;
    }

    function houseAcquistionInfoPdf($info, $is_binary = false) {
        Log::info("UrlPdfCsvExcelService: houseAcquistionInfoPdf called");

        #ToDO: Make common function for this.
        // Common Code start
        $houseAll = $this->propertyService->findAcquistionDetails(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['header_logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $html                      = view('pdfs.header', $parse_data);

        foreach ($houseAll as $key => $value) {
            $parse_data    = [];
            $house_picture = @$value->front_picture->store_name;
            $parse_data['img_src'] = CommonHelper::documentPictureStorage($house_picture);

            $parse_data['address']           = CommonHelper::emptyDefault($value->address);
            $parse_data['city']              = CommonHelper::emptyDefault($value->city);
            $parse_data['county']            = CommonHelper::emptyDefault($value->county);
            $parse_data['state']             = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']               = CommonHelper::emptyDefault($value->zip);
            $parse_data['total_living_sqft'] = CommonHelper::emptyNumberDefault($value->total_living_sqft);
            $parse_data['bed']               = CommonHelper::emptyDefault($value->bed);
            $parse_data['bath']              = CommonHelper::emptyDefault($value->bath);
            $parse_data['lot_acreage_sf']    = CommonHelper::emptyNumberDefault($value->lot_acreage_sf);
            $parse_data['parcel_id1']        = CommonHelper::emptyDefault($value->parcel_id1);
            $parse_data['parcel_id2']        = CommonHelper::emptyDefault($value->parcel_id2);
            $parse_data['subdivision']       = CommonHelper::emptyDefault($value->subdivision);
            $parse_data['year_built']        = CommonHelper::emptyDefault($value->year_built);
            $parse_data['trustee']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['trustee_phone']     = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee_phone');
            $parse_data['ts_no']             = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee_file_no');
            $parse_data['sale_date']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['sale_time']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            $parse_data['case_number']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['priceint']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'priceint', 'number');
            $parse_data['opening_bid']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['county_value']      = CommonHelper::emptyMoneyDefault(@$value->county_value);
            $parse_data['cma_arv']           = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['zestimates']        = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'rents_zestimate', '');
            $parse_data['gsd']               = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'general_demand', '');
            $parse_data['ssd']               = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'specific_demand', '');
            $enum                            = [0 => "No",
                                                1 => "Yes"];
            $flf                             = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_foreclosing', '');
            $slf                             = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_foreclosing', '');
            $tlf                             = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_foreclosing', '');
            $sale_type_array                 = config('property_information.sale_type');
            $sale_type                       = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']         = @$sale_type_array[@$sale_type];

            $parse_data['first_lender']           = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lender', '');
            $parse_data['first_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['first_lien_foreclosing'] = @$enum[@$flf];

            $parse_data['second_lender']           = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lender', '');
            $parse_data['second_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['second_lien_foreclosing'] = @$enum[@$slf];

            $parse_data['third_lender']           = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lender', '');
            $parse_data['third_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['third_lien_foreclosing'] = @$enum[@$tlf];

            $parse_data['other_lender']        = CommonHelper::emptyDefaultObject(@$value->other_liens, 'lender', '');
            $parse_data['other_lien_amount']   = CommonHelper::emptyDefaultObject(@$value->other_liens, 'lien_amount', 'currency');
            $parse_data['other_date_recorded'] = CommonHelper::emptyDefaultObject(@$value->other_liens, 'date_recorded', 'date');

            $parse_data['min_amt_nxt_ub']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'min_amt_nxt_ub', 'currency');
            $parse_data['last_date_to_upset_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'last_date_to_upset_bid', 'date');
            $parse_data['wining_bidder']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['deposit_upset']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'deposit_upset', 'currency');
            $parse_data['nos_by']                 = CommonHelper::nameFormat(@$value->last_sale_details->nos);
            $parse_data['nos_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'nos_date', 'date');
            $parse_data['dtc_by']                 = CommonHelper::nameFormat(@$value->first_dtc->user);
            $parse_data['dtc_date']               = CommonHelper::emptyDefaultObject(@$value->first_dtc, 'date', 'date');
            $parse_data['property_description']   = CommonHelper::emptyDefault(@$value->property_description);

            $parse_data['est_prp_rate_of_return'] = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'est_prp_rate_of_return', 'percentage');
            $parse_data['act_prp_rate_of_return'] = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'act_prp_rate_of_return', 'percentage');
            $parse_data['est_days_on_market']     = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'est_days_on_market', 'number');
            $parse_data['borrower']               = CommonHelper::emptyDefault(@$value->last_borrower_info->full_name);

            $html .= view('pdfs.40_details_acquistion_info_list', $parse_data);
        }

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);

        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;
    }


    function wholesaleRetailPdf($info, $is_binary = false) {
        Log::info("UrlPdfCsvExcelService: wholesaleRetailPdf called");

        $arv             = @$info['arv'];
        $cma_arv         = @$info['cma_arv'];
        $renovation_cost = @$info['renovation_cost'];
        $page_type       = @$info['page_show'];
        $house_id        = @$info['house_id'];
        $house_ids       = @$info['house_ids'];

        $chouse_ids = array_merge($house_id ? (is_array($house_id) ? $house_id : [$house_id]) : [],
                                  $house_ids ? (is_array($house_ids) ? $house_ids : [$house_ids]) : []);


        #ToDO: Make common function for this.
        // Common Code start
        $houseAll = $this->propertyService->findWholetailDetails(array_unique($chouse_ids));
        if (empty($houseAll->count())) {
            return false;
        }

        $parse_data                    = [];
        $path_binary                   = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['header_logo']     = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $path_binary                   = file_get_contents(resource_path('assets/images/dollars.png'));
        $parse_data['dollars_pic']     = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $path_binary                   = file_get_contents(resource_path('assets/images/foreclosure.gif'));
        $parse_data['foreclosure_pic'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $path_binary                   = file_get_contents(resource_path('assets/images/gohome.png'));
        $parse_data['gohome_pic']      = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $path_binary                   = file_get_contents(resource_path('assets/images/pictures.png'));
        $parse_data['pictures_pic']    = 'data:image/jpg;base64,' . base64_encode($path_binary);

        $css  = view('pdfs.wholesale.css', $parse_data);
        $html = $css;

        $header  = view('pdfs.wholesale.header', $parse_data);
        $header2 = view('pdfs.wholesale.header_2', $parse_data);

        foreach ($houseAll as $key => $value) {
            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data    = [];

            $house_picture = @$value->front_picture->store_name;
            $parse_data['img_src'] = CommonHelper::documentPictureStorage($house_picture);


            if (empty($cma_arv)) {
                $cma_arv            = (@$value->last_cma_arv_recommendations);
            }
            $parse_data['cma_arv']  = CommonHelper::moneyFormat(@$cma_arv);

            if (!empty($renovation_cost))
                $est_house_repairs = $renovation_cost;
            else if (!empty(@$value->property_acquisition_a_to_b_second->house_construction_est))
                $est_house_repairs = $value->property_acquisition_a_to_b_second->house_construction_est;
            else
                $est_house_repairs = '';

            if ($arv > 1) {
                $arv = $arv / 100;
            }
            if (empty($arv))
                $arv = 0.75;

            //Log::alert('message',[$est_house_repairs,$arv,$parse_data['cma_arv']]);
            //echo $whole_sale_cost = preg_replace('/[^0-9,.-]/s', '', '$150,000.00') * (2000) - (20000);
            //$whole_sale_cost =  ($cma_arv * $arv) - $est_house_repairs;
            $whole_sale_cost = ($cma_arv * @$arv) - (@$est_house_repairs);
            $whole_sale_cost = CommonHelper::moneyFormat($whole_sale_cost, '2');
            $est_house_repairs = CommonHelper::moneyFormat($est_house_repairs, '2');

            $parse_data['address']            = CommonHelper::emptyDefault($value->address, '');
            $parse_data['address_url']        = $address_url;
            $parse_data['city']               = CommonHelper::emptyDefault($value->city, '');
            $parse_data['county']             = CommonHelper::emptyDefault($value->county, '');
            $parse_data['state']              = CommonHelper::emptyDefault($value->state, '');
            $parse_data['zip']                = CommonHelper::emptyDefault($value->zip, '');
            $parse_data['est_days_on_market'] = CommonHelper::emptyDefaultObject(@$value->wholesale_buyer_strategy, 'est_days_on_market', 'number');
            $parse_data['school']             = 'school';
            $parse_data['rents_zestimate']    = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'rents_zestimate', '');
            $parse_data['gsd']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'general_demand', '');
            $parse_data['ssd']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'specific_demand', '');
            $parse_data['whole_sale_cost']    = $whole_sale_cost;
            $parse_data['est_house_repairs']  = $est_house_repairs;
            $parse_data['zestimates']         = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'zestimates', 'currency');
            $parse_data['zillow_url']         = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'zillow_url', '');
            $parse_data['realtor_url']        = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'realtor_url', '');
            $parse_data['total_living_sqft']  = CommonHelper::emptyNumberDefault($value->total_living_sqft);
            $parse_data['total_sqft']         = CommonHelper::emptyNumberDefault($value->total_sqft);
            $parse_data['bed']                = CommonHelper::emptyDefault($value->bed);
            $parse_data['bath']               = CommonHelper::emptyDefault($value->bath);
            $parse_data['lot_acreage_sf']     = CommonHelper::emptyNumberDefault($value->lot_acreage_sf);
            $parse_data['main_floor_area']    = CommonHelper::emptyNumberDefault($value->main_floor_area);
            $parse_data['second_floor_area']  = CommonHelper::emptyNumberDefault($value->second_floor_area);
            $parse_data['third_floor_area']   = CommonHelper::emptyNumberDefault($value->third_floor_area);
            $parse_data['basement_area']      = CommonHelper::emptyNumberDefault($value->basement_area);
            $parse_data['year_built']         = CommonHelper::emptyDefault($value->year_built);
            $parse_data['county_value']       = CommonHelper::emptyMoneyDefault(@$value->county_value);

            $desc                               = CommonHelper::lengthCheckerSplitStringToLimitDotted(CommonHelper::emptyDefault(@$value->property_descriptions->property_description), 298);
            $parse_data['property_description'] = $desc;


            // Second QuickView Data
            $phone = @$value->last_owner_info->phone;
            if(empty($phone))
                $phone = @$value->last_owner_info->phone2;


            $parse_data['full_name']        = CommonHelper::emptyDefaultObject(@$value->last_owner_info, 'full_name', '');
            $parse_data['owner_phone']        = CommonHelper::emptyDefault($phone,  '');
            $parse_data['garages']               = CommonHelper::emptyDefault($value->garages, '');
            $parse_data['lot_acreage_sf']    = CommonHelper::emptyNumberDefault($value->lot_acreage_sf);

            $parse_data['f_lien_amount']   = CommonHelper::emptyMoneyDefault(@$value->first_lien->lien_amount);
            $parse_data['f_date_recorded'] = CommonHelper::emptyDateFormat(@$value->first_lien->date_recorded);
            $parse_data['s_lien_amount']   = CommonHelper::emptyMoneyDefault(@$value->second_liens->lien_amount);
            $parse_data['s_date_recorded'] = CommonHelper::emptyDateFormat(@$value->second_liens->date_recorded);
            $parse_data['t_lien_amount']   = CommonHelper::emptyMoneyDefault(@$value->third_liens->lien_amount);
            $parse_data['t_date_recorded'] = CommonHelper::emptyDateFormat(@$value->third_liens->date_recorded);

            $parse_data['assessment'] = @$value->assessment;



            ## First Record
            $quickview = '';
            $quickview2 = '';
            if($page_type == 'one')
            {
                $quickview = view('pdfs.wholesale.quickview', $parse_data);
            }
            else if($page_type == 'two')
            {
                $quickview2 = view('pdfs.wholesale.quickview_2', $parse_data);
            }
            else
            {
                $quickview = view('pdfs.wholesale.quickview', $parse_data);
                $quickview2 = view('pdfs.wholesale.quickview_2', $parse_data);
            }

            if ($key == 0)
                $html .= $header . $quickview . $quickview2;
            else
                $html .= $header2 . $quickview . $quickview2;
        }

        if(empty($html))
            $html .= $header;

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);

        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;
    }

    function houseAHOAInfoPdf($info, $is_binary = false) {

        Log::info("UrlPdfCsvExcelService: houseAHOAInfoPdf called");

        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->findHoaDetails(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['header_logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $html                      = view('pdfs.header', $parse_data);

        $hoa_list_html = '<div style="text-align:center; color:red; font-size:22px;">HOA Report</div><hr/>';
        //$tr_html = '<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:\'Trebuchet MS\', Verdana, Arial;">';
        $enum                                 = [0 => "No",
            1 => "Yes"];

        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);

            $parse_data = [];
            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['trustee']                =  CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee', '');
            $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            $parse_data['address_url']            = $address_url;
            $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            $parse_data['year_built']             = CommonHelper::emptyDefault($value->year_built);
            $parse_data['total_living_sqft']      = CommonHelper::emptyDefault($value->total_living_sqft);
            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $total_lien = floatval(@$value->first_liens->lien_amount ) + floatval(@$value->second_liens->lien_amount ) + floatval(@$value->third_liens->lien_amount ) ;
            $parse_data['total_lien']                = CommonHelper::moneyFormat($total_lien);
            
            $total_equity = (floatval(@$total_lien)+floatval(@$value->hoa_liens->hoa_lien_amount))-floatval(@$value->last_cma_arv_recommendations->recommended_cma_arv);
            
            $parse_data['total_equity']           = CommonHelper::moneyFormat($total_equity);
            $parse_data['hoa_lien_amount']        = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['DCV']                    = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['first_no_str']  = @$value->first_liens->no_str_no_appt?'Yes':'No';
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['second_no_str']  = @$value->second_liens->no_str_no_appt?'Yes':'No';
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['third_no_str']  = @$value->third_liens->no_str_no_appt?'Yes':'No';
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $total_loan_est_lien = floatval(@$value->first_liens->amortization_loan_estimate_balance ) + floatval(@$value->second_liens->amortization_loan_estimate_balance ) + floatval(@$value->third_liens->amortization_loan_estimate_balance ) ;
            $parse_data['total_loan_estimated_balance'] = $total_loan_est_lien;

            // $parse_data['address_url']            = $address_url;
            // $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            // $parse_data['city']                   = CommonHelper::emptyDefault($value->city);
            // $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            // $parse_data['state']                  = CommonHelper::emptyDefault($value->state);
            // $parse_data['zip']                    = CommonHelper::emptyDefault($value->zip);
            // $parse_data['owner_full_address']     = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            // $parse_data['hoa_name']               = CommonHelper::emptyDefault(@$value->hoa_liens->hoa_name);
            // $parse_data['hoa_lien_amount']        = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            // $parse_data['hoa_lien_date']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            // $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');

            // $total_lien = floatval(@$value->first_liens->lien_amount ) + floatval(@$value->second_liens->lien_amount ) + floatval(@$value->third_liens->lien_amount ) ;
            // $parse_data['total_lien']                = CommonHelper::moneyFormat($total_lien);

            // $total_equity = floatval(@$value->first_liens->est_equity ) + floatval(@$value->second_liens->est_equity ) + floatval(@$value->third_liens->est_equity ) ;
            // $parse_data['total_equity']                = CommonHelper::moneyFormat($total_equity);

            // $parse_data['sale_date']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            // $parse_data['sale_time']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');

            // $parse_data['trustee']                =  CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee', '');
            // $parse_data['case_number']            =  CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            // $sale_type_array                      = config('property_information.sale_type');
            // $sale_type                            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            // $parse_data['sale_type']              = @$sale_type_array[@$sale_type];
            // $parse_data['owner_full_name']        = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            // $parse_data['owner_full_address']     = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            // $parse_data['opening_bid']            = '';//CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            // $parse_data['redemption_expires']     = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            // $hoa_code_array                         = config('property_information.tax_code');
            // $hoa_tax_code                           = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'tax_code', '');
            // $parse_data['hoa_tax_code']             = @$hoa_code_array[@$hoa_tax_code];
            // $parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            // $parse_data['tax_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_expires', 'date');

            // $tax_code_array                         = config('property_information.tax_code');
            // $tax_code                               = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'tax_code', '');
            // $parse_data['tax_tax_code']             = @$tax_code_array[@$tax_code];

            $hoa_list_html                      .= view('pdfs.hoa_list', $parse_data);
        }

        $html       = $html . $hoa_list_html;
        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function houseAHOAInfoArray($info) {

        Log::info("UrlPdfCsvExcelService: houseAHOAInfoArray called");

        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->findHoaDetails(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $header   = [];
        $header[] = "Buyer";	
        $header[] = "Trustee";	
        $header[] = "County";
        $header[] = "Property Url";	
        $header[] = "DCV#";	
        $header[] = "Property Address";	
        $header[] = "Year Built";	
        $header[] = "Living Sqft";	
        $header[] = "Cma/Arv";
        $header[] = "Total Mortgage Lien";	
        $header[] = "Total Estimated Equity";
        $header[] = "Hoa Lien";	
        $header[] = "1st STR Y/N";
        $header[] = 'Loan Estimated Balance First Lien';
        $header[] = '2nd STR Y/N';
        $header[] = 'Loan Estimated Balance Second Lien';
        $header[] = '3rd STR Y/N';
        $header[] = 'Loan Estimated Balance Third Lien';
        $header[] = "TOTAL EST Loan Balance";
        $header[] = "Max Bid";
        $header[] = "90/180 Stategy";	
        $header[] = "Notes";
        
        $temp[]   = $header;

        $enum  = [0 => "No",1 => "Yes"];
        foreach ($houseAll as $key => $value) {
            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);
            $parse_data = [];
            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['trustee']                =  CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee', '');
            $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            $parse_data['record_link']            = $address_url;
            $parse_data['DCV']                    = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            $parse_data['year_built']             = CommonHelper::emptyDefault($value->year_built);
            $parse_data['total_living_sqft']      = CommonHelper::emptyDefault($value->total_living_sqft);
            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $total_lien = floatval(@$value->first_liens->lien_amount ) + floatval(@$value->second_liens->lien_amount ) + floatval(@$value->third_liens->lien_amount ) ;
            $parse_data['total_lien']                = CommonHelper::moneyFormat($total_lien);
            //$total_equity = floatval(@$value->first_liens->est_equity ) + floatval(@$value->second_liens->est_equity ) + floatval(@$value->third_liens->est_equity ) ;
            $total_equity = (floatval(@$total_lien)+floatval(@$value->hoa_liens->hoa_lien_amount))-floatval(@$value->last_cma_arv_recommendations->recommended_cma_arv);
            $parse_data['total_equity']           = CommonHelper::moneyFormat($total_equity);
            $parse_data['hoa_lien_amount']        = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['first_no_str']  = @$value->first_liens->no_str_no_appt?'Yes':'No';
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['second_no_str']  = @$value->second_liens->no_str_no_appt?'Yes':'No';
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['third_no_str']  = @$value->third_liens->no_str_no_appt?'Yes':'No';
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $total_loan_est_lien = floatval(@$value->first_liens->amortization_loan_estimate_balance ) + floatval(@$value->second_liens->amortization_loan_estimate_balance ) + floatval(@$value->third_liens->amortization_loan_estimate_balance ) ;
            $parse_data['total_loan_estimated_balance'] = $total_loan_est_lien;
            $temp[] = $parse_data;
        }

        return $temp;

    }


    function quickViewPdf($info, $is_binary = false) {

        Log::info("UrlPdfCsvExcelService: quickViewPdf called");
        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->quickViewExport($info['house_ids']);

        if (empty($houseAll)) {
            return $temp;
        }


        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));

        $parse_data['header_logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $html                      = view('pdfs.header', $parse_data);

        $quickview_html = '<div style="text-align:center; color:red; font-size:22px;">Quickview Report</div><hr/>';
        //$tr_html = '<table cellpadding="5" cellspacing="0" width="100%" style="padding:10px; color:#333; font-size:11px; font-family:\'Trebuchet MS\', Verdana, Arial;">';
        foreach ($houseAll as $key => $value) {

            $house_picture = @$value->front_picture->store_name;

            $parse_data['img_src'] = CommonHelper::documentPictureStorage($house_picture);

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);

            $parse_data = [];
            $parse_data['address_url']        = $address_url;
            $parse_data['address']           = CommonHelper::emptyDefault($value->address);
            $parse_data['city']              = CommonHelper::emptyDefault($value->city);
            $parse_data['county']            = CommonHelper::emptyDefault($value->county);
            $parse_data['state']             = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']               = CommonHelper::emptyDefault($value->zip);

            $parse_data['sale_date']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['sale_time']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            $parse_data['case_number']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['opening_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');

            $sale_type_array                      = config('property_information.sale_type');
            $sale_type                            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']              = @$sale_type_array[@$sale_type];

            $sale_status_array                    = config('property_information.sale_status');
            $sale_status                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_status', '');
            $parse_data['sale_status']            = @$sale_status_array[@$sale_status];

            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['owner_full_name']        = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['owner_phone']            = CommonHelper::emptyDefault(@$value->last_owner_info->phone);

            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['county_value']           = CommonHelper::emptyMoneyDefault(@$value->county_value);

            $parse_data['first_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['second_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['third_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['hoa_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['hoa_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_date_recorded', 'date');
            $parse_data['other_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->other_liens, 'lien_amount', 'currency');
            $parse_data['other_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->other_liens, 'date_recorded', 'date');

            $quickview_html                      .= view('pdfs.quickview_export', $parse_data);

        }

        $html       =  $quickview_html;

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function quickViewArray($info) {
        Log::info("UrlPdfCsvExcelService: quickViewArray called");
        $temp = [];
        $houseAll = $this->propertyService->quickViewExport($info['house_ids']);
        if (empty($houseAll)) {
            return $temp;
        }

        $header   = [];
        $header[] = "Address";
        $header[] = "City";
        $header[] = "County";
        $header[] = "State";
        $header[] = "Zip";
        $header[] = "Sale Date";
        $header[] = "Sale Time";
        $header[] = "CASE #";
        $header[] = "Opening Bid";
        $header[] = "Sale Type";
        $header[] = "Sale Status";
        $header[] = "Buy It Request";
        $header[] = "Owner Name";
        $header[] = "Owner Phone";
        $header[] = "CMA/ARV";
        $header[] = "County Value";
        $header[] = "First Lien Amount";
        $header[] = "First Lien Date";
        $header[] = "Second Lien Amount";
        $header[] = "Second Lien Date";
        $header[] = "Third Lien Amount";
        $header[] = "Third Lien Date";
        $header[] = "HOA Lien Amount";
        $header[] = "HOA Lien Date";
        $header[] = "Other Lien Amount";
        $header[] = "Other Lien Date";

        $temp[]   = $header;

        foreach ($houseAll as $key => $value) {
            $parse_data                      = [];
            $parse_data['address']           = CommonHelper::emptyDefault($value->address);
            $parse_data['city']              = CommonHelper::emptyDefault($value->city);
            $parse_data['county']            = CommonHelper::emptyDefault($value->county);
            $parse_data['state']             = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']               = CommonHelper::emptyDefault($value->zip);

            $parse_data['sale_date']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['sale_time']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_time', '');
            $parse_data['case_number']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['opening_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');

            $sale_type_array                      = config('property_information.sale_type');
            $sale_type                            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']              = @$sale_type_array[@$sale_type];

            $sale_status_array                    = config('property_information.sale_status');
            $sale_status                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_status', '');
            $parse_data['sale_status']            = @$sale_status_array[@$sale_status];

            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['owner_full_name']        = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['owner_phone']            = CommonHelper::emptyDefault(@$value->last_owner_info->phone);

            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['county_value']           = CommonHelper::emptyMoneyDefault(@$value->county_value);

            $parse_data['first_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['second_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['third_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['hoa_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['hoa_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_date_recorded', 'date');
            $parse_data['other_lien_amount']      = CommonHelper::emptyDefaultObject(@$value->other_liens, 'lien_amount', 'currency');
            $parse_data['other_date_recorded']    = CommonHelper::emptyDefaultObject(@$value->other_liens, 'date_recorded', 'date');


            $temp[]                               = $parse_data;
        }


        return $temp;
    }

    function getBuyItRequestList($house_id){
      $head = $this->houseBuyItService->getDownBuyitUser($house_id,1 );

      $top_message ='';
      if(!empty($head))
      {
          foreach ($head as $userPosition)
          {
              $top_user = CommonHelper::nameFormat($userPosition);

              if ($userPosition->position < 10)
                  $u_position = ucwords($this->houseBuyItService->getBuyitPositionLetterConvert($userPosition->position));
              else
                  $u_position = $userPosition->position . 'th';

              $top_message.= $top_user . ' is in ' . $u_position . ' Position.';
          }
      }
      return $top_message;

    }

    function redemptionArray($info) {
        Log::info("UrlPdfCsvExcelService: quickViewArray called");
        $temp = [];
        $houseAll = $this->propertyService->redemptionExport($info['house_ids']);
        if (empty($houseAll)) {
            return $temp;
        }

        $header   = [];
        $header[] = "Sale Date";
        $header[] = "HOA Redemption Expiration Date";
        $header[] = "Opening Bid";
        $header[] = "HOA Winning Bidder";
        $header[] = "Affidavit";
        $header[] = "CMA/ARV";
        $header[] = '1st Lien $';
        $header[] = '1st Lien Date';
        $header[] = '2nd Lien $';
        $header[] = '2nd Lien Date';
        $header[] = '3rd Lien $';
        $header[] = '3rd Lien Date';
        $header[] = 'HOA Name';
        $header[] = 'HOA Lien $';
        $header[] = 'HOA Lien Date';
        $header[] = 'Taxes Due';
        $header[] = "Owner Name";
        $header[] = "Trustee";
        $header[] = "Property Address";
        $header[] = 'Legal Description';
        $header[] = "Property Url";
        $header[] = "County";


        // $header[] = "City";
        // $header[] = "State";
        // $header[] = "Zip";
        // $header[] = "Sale Type";
        // $header[] = "Case Number";
        // $header[] = "Owner Address";
        // $header[] = "HOA Name";
        // $header[] = "HOA Trustees Deed Date";
        // $header[] = "HOA Winning Bid Amount";
        // $header[] = "HOA Property Tax Code";
        // $header[] = "Tax Trustees Deed Date";
        // $header[] = "Tax Winning Bidder";
        // $header[] = "Tax Winning Bid Amount";
        // $header[] = "Tax Redemption Expiration Date";
        // $header[] = "Tax Property Tax Code";
        
        $temp[]   = $header;

        foreach ($houseAll as $key => $value) {

            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data                             = [];
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['hoa_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            $parse_data['opening_bid']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['hoa_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bid', 'currency');
            $parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['1st Lien $'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['1st Lien Date'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['2nd Lien $'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['2nd Lien Date'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['3rd Lien $'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['3rd Lien Date'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            $parse_data['HOA Lien $'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['HOA Lien Date'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            $parse_data['taxes_due']=$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['owner_full_name']          = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['Trustee'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['legal_description'] = CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['address_url']        = $address_url;
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);

            // $parse_data['city']                     = CommonHelper::emptyDefault($value->city);
            // $parse_data['state']                    = CommonHelper::emptyDefault($value->state);
            // $parse_data['zip']                      = CommonHelper::emptyDefault($value->zip);
            // $sale_type_array                        = config('property_information.sale_type');
            // $sale_type                              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            // $parse_data['sale_type']                = @$sale_type_array[@$sale_type];

            // $parse_data['case_number']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            // $parse_data['owner_full_address']       = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            // $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            // $parse_data['hoa_trdeed_date']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'trdeed_date', 'date');
            // $parse_data['hoa_winning_bidder']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bidder');

            // $hoa_code_array                         = config('property_information.tax_code');
            // $hoa_tax_code                           = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'tax_code', '');
            // $parse_data['hoa_tax_code']             = @$hoa_code_array[@$hoa_tax_code];


            // $parse_data['tax_trdeed_date']          = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'trdeed_date', 'date');
            // $parse_data['tax_winning_bidder']       = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'winning_bidder');
            // $parse_data['tax_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'winning_bid', 'currency');
            // $parse_data['tax_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_expires', 'date');

            // $tax_code_array                         = config('property_information.tax_code');
            // $tax_code                               = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'tax_code', '');
            // $parse_data['tax_tax_code']             = @$tax_code_array[@$tax_code];


            $temp[]                               = $parse_data;
        }

        return $temp;
    }

    function redemptionPdf($info, $is_binary = false) {

        Log::info("UrlPdfCsvExcelService: quickViewPdf called");
        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->redemptionExport($info['house_ids']);

        if (empty($houseAll)) {
            return $temp;
        }

        $quickview_html = '<div style="text-align:center; color:red; font-size:22px;">Redemption Report</div><hr/>';
        foreach ($houseAll as $key => $value) {

            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data = [];

            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['hoa_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            $parse_data['opening_bid']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['hoa_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bid', 'currency');
            $parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['cma_arv']                = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['first_lien']         = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_lien_date']    = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['second_lien']         = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_lien_date']    = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['third_lien']         = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_lien_date']    = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['hoa_name']         = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            $parse_data['hoa_lien']         = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['hoa_lien_date']    = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            $parse_data['taxes_due']=$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['owner_full_name']  = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['trustee']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['address']          = CommonHelper::emptyDefault($value->address);
            $parse_data['legal_description']= CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['address_url']      = $address_url;
            $parse_data['county']           = CommonHelper::emptyDefault($value->county);
            
            $quickview_html                      .= view('pdfs.redemption_export', $parse_data);

        }

        $html       =  $quickview_html;

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function bidderArray($info) {
        Log::info("UrlPdfCsvExcelService: quickViewArray called");
        $temp = [];
        $houseAll = $this->propertyService->bidderExport($info);
        //Log::info("UrlPdfCsvExcelService:".print_r($houseAll,true));
        
        if (empty($houseAll)) {
            return $temp;
        }
        
        if(!empty($info['search_fields']['w_bids'])){
            return $this->wBidsBidderArray($houseAll);
        }
        if(!empty($info['search_fields']['bidder_name'])){
            return $this->BidderNameArray($info);
        }

        $header   = [];
        $header[] = "Address";
        $header[] = "City";
        $header[] = "County";
        $header[] = "State";
        $header[] = "Zip";

        $header[] = 'Bidder Name';
        $header[] = 'Address of Upset Bidder';
        $header[] = 'Amount of New Upset Bid';
        $header[] = 'Bid Date';
        $header[] = 'Last Day for Next Upset Bid';
        $header[] = 'Owner Name';
        $header[] = 'Owner Address';
        $header[] = 'Owner Phone#';
        $temp[]   = $header;

        foreach ($houseAll as $key => $value) {
            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);

            $parse_data                           = [];
            $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            $parse_data['city']                   = CommonHelper::emptyDefault($value->city);
            $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            $parse_data['state']                  = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']                    = CommonHelper::emptyDefault($value->zip);
            $parse_data['bidder_name']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'name_upset_bidder', '');
            $parse_data['bidder_address']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'address', '');
            $parse_data['amount_of_bid']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'amount_of_bid', 'currency');
            $parse_data['bid_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'bid_date', 'date');
            $parse_data['last_date_to_upset_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'last_date_to_upset_bid', 'date');
            $parse_data['full_name']            = CommonHelper::emptyDefaultObject(@$value->last_owner_info, 'full_name', '');
            $parse_data['owner_full_address']   = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            $parse_data['owner_phone']          = CommonHelper::emptyDefault(@$value->last_owner_info->phone);

            $temp[]                               = $parse_data;
        }
        return $temp;
    }

    function BidderNameArray($info) {
        $houseAll = $this->propertyService->bidderExportByName($info);

        Log::info("UrlPdfCsvExcelService: wBidsBidderArray called");
        $temp = [];
        $header   = [];
        $header[] = "Address";
        $header[] = "County";
        $header[] = "Sale Date";
        $header[] = 'Case Number';
        $header[] = 'Record Url';
        $bidderCount=1;
        foreach ($houseAll as $key => $value) {
            if(count($value->last_sale_details2->bidders) > $bidderCount){
                $bidderCount=count($value->last_sale_details2->bidders);
            }
        }
        for($i=1; $i<=$bidderCount; $i++){
            $header[] = 'BID '.$i;
            $header[] = 'BID '.$i.' AMOUNT';
        }
        
        $temp[]   = $header;
        foreach ($houseAll as $key => $value) {
            $address_url = $this->houseTokenService->detail_url($value['house_id'], $value);

            $parse_data                             = [];
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details2, 'sale_date', '');
            $parse_data['case_number']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details2, 'case_number', '');
            $parse_data['record_url']               = $address_url;
            $i=1;
            for($i=1; $i<=$bidderCount; $i++){
                $parse_data['Bid '.$i]              = CommonHelper::emptyDefaultObject(@$value->last_sale_details2->bidders[$i-1], 'name_upset_bidder', '');
                $parse_data['bid '.$i.'Amount']     = CommonHelper::emptyDefaultObject(@$value->last_sale_details2->bidders[$i-1], 'amount_of_bid', 'currency');
            }
            $temp[]                               = $parse_data;
        }
        return $temp;
    }


    function wBidsBidderArray($houseAll) {
        Log::info("UrlPdfCsvExcelService: wBidsBidderArray called");
        $temp = [];
        $header   = [];
        $header[] = "Address";
        $header[] = "County";
        $header[] = "Sale Date";
        $header[] = 'Case Number';
        $header[] = 'Record Url';
        $bidderCount=1;
        foreach ($houseAll as $key => $value) {
            if(count($value->last_sale_details2->bidders) > $bidderCount){
                $bidderCount=count($value->last_sale_details2->bidders);
            }
        }
        for($i=1; $i<=$bidderCount; $i++){
            $header[] = 'BID '.$i;
            $header[] = 'BID '.$i.' AMOUNT';
            $header[] = 'LDUB '.$i;
        }
        
        $temp[]   = $header;
        foreach ($houseAll as $key => $value) {
            $address_url = $this->houseTokenService->detail_url($value['house_id'], $value);

            $parse_data                             = [];
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details2, 'sale_date', '');
            $parse_data['case_number']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details2, 'case_number', '');
            $parse_data['record_url']               = $address_url;
            $i=1;
            for($i=1; $i<=$bidderCount; $i++){
                $parse_data['Bid '.$i]              = CommonHelper::emptyDefaultObject(@$value->last_sale_details2->bidders[$i-1], 'name_upset_bidder', '');
                $parse_data['bid '.$i.'Amount']     = CommonHelper::emptyDefaultObject(@$value->last_sale_details2->bidders[$i-1], 'amount_of_bid', 'currency');
                $parse_data['ldub'.$i]              = CommonHelper::emptyDefaultObject(@$value->last_sale_details2->bidders[$i-1], 'last_date_to_upset_bid', 'date');
            }
            $temp[]                               = $parse_data;
        }
        return $temp;
    }

    function wbidsBidderPdf($houseAll,$is_binary = false) {


        $bidder_html = '<div style="text-align:center; color:red; font-size:22px;">Bidder Report</div><hr/>';
        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->detail_url($value['house_id'], $value);
            $parse_data = [];
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details2, 'sale_date', '');
            $parse_data['case_number']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details2, 'case_number', '');
            $parse_data['address_url']               = $address_url;
            $parse_data['bidderInfo']               = @$value->last_sale_details2->bidders;
            $bidder_html                         .= view('pdfs.wbids_bidder_export', $parse_data);

        }

        $html       =  $bidder_html;

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }


    function bidderPdf($info, $is_binary = false) {

        Log::info("UrlPdfCsvExcelService: quickViewPdf called");
        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->bidderExport($info);

        if (empty($houseAll)) {
            return $temp;
        }
        if($info['w_bids']){
            return $this->wbidsBidderPdf($houseAll,$is_binary);
            
        }

        $bidder_html = '<div style="text-align:center; color:red; font-size:22px;">Bidder Report</div><hr/>';
        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);
            $parse_data = [];

            $parse_data['address_url']            = $address_url;
            $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            $parse_data['city']                   = CommonHelper::emptyDefault($value->city);
            $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            $parse_data['state']                  = CommonHelper::emptyDefault($value->state);
            $parse_data['zip']                    = CommonHelper::emptyDefault($value->zip);
            $parse_data['bidder_name']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'name_upset_bidder', '');
            $parse_data['bidder_address']         = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'address', '');
            $parse_data['amount_of_bid']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'amount_of_bid', 'currency');
            $parse_data['bid_date']               = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'bid_date', 'date');
            $parse_data['last_date_to_upset_bid'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'last_date_to_upset_bid', 'date');
            $parse_data['full_name']              = CommonHelper::emptyDefaultObject(@$value->last_owner_info, 'full_name', '');
            $parse_data['owner_full_address']     = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            $parse_data['owner_phone']            = CommonHelper::emptyDefault(@$value->last_owner_info->phone);

            $bidder_html                         .= view('pdfs.bidder_export', $parse_data);

        }

        $html       =  $bidder_html;

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function subToArray($info) {
        Log::info("UrlPdfCsvExcelService: subToArray called");
        $temp = [];
        
        $houseAll = $this->propertyService->subToExport($info['house_ids']);
        
        if (empty($houseAll)) {
            return $temp;
            
        }
        
        $header   = [];
        $header[] = 'County';
        $header[] = "Address";
        // $header[] = "City";
        // $header[] = "Zip";
        $header[] = "Link";
        $header[] = 'Sale Date';
        $header[] = 'Sale Type';
        $header[] = "CMA/ARV";
        $header[] = '1st Lien';
       // $header[] = '1st Lien Date';
        $header[] = 'STR Y/N';
        $header[] = '2nd Lien';
       // $header[] = '2nd Lien Date';
        $header[] = 'STR Y/N';
        $header[] = '3rd Lien';
       // $header[] = '3rd Lien Date';
        $header[] = 'STR Y/N';
        $header[] = 'HOA Lien';
        $header[] = 'Sale Date';

      //  $header[] = 'HOA Lien ';
        $header[] = 'Redemption Date';
        $header[] = 'Tax Lien';
        $header[] = 'Date';
        $header[] = 'Redemption Date';
        $header[] = 'Link to';
        $header[] = 'Notice of Mailing Date';
        $header[] = '';
     
        $temp[]   = $header;
       
        foreach ($houseAll as $key => $value) {
          
            $address_url = $this->houseTokenService->address_url($value->house_id, $value);

            $parse_data                         = [];
            $parse_data['county']               = CommonHelper::emptyDefault($value->county);
            $parse_data['address']              = CommonHelper::emptyDefault($value->address);
            $parse_data['link']                 = $address_url;
            //$parse_data['zip']                  = CommonHelper::emptyDefault($value->zip);
            
            $parse_data['sale_date']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');

            $sale_type_array                    = config('property_information.sale_type');
            $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
        
            $parse_data['cma_arv']              = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['1st Lien']           = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            //$parse_data['1st Lien Date']        = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['1st STR Y/N']          = @$value->first_liens->no_str_no_appt?'Yes':'No';

            $parse_data['2nd Lien']           = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            //$parse_data['2nd Lien Date']        = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['2nd STR Y/N']          = @$value->second_liens->no_str_no_appt?'Yes':'No';

            $parse_data['3rd Lien']           = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            //$parse_data['3rd Lien Date']        = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['3rd STR Y/N']          = @$value->third_liens->no_str_no_appt?'Yes':'No';
            
            $parse_data['hoa_name']               = CommonHelper::emptyDefault(@$value->hoa_liens->hoa_name);
            //$parse_data['hoa_lien_date']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            $parse_data['sale_date_1']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['redemption_date']        = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_date', 'date');

            $parse_data['Tax Lien']               = CommonHelper::emptyDefault(@$value->tax_liens->tax_name);
            $parse_data['date']                   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'date_of_tax_lien', 'date');
            $parse_data['Redemption Date']       = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_date', 'date');

            $parse_data['Link to']       = $address_url;
            $parse_data['Notice of Mailing Date']  = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');

            $temp[]                               = $parse_data;
        }
        
        return $temp;
    }

    function subToPdf($info, $is_binary = false){
        Log::info("UrlPdfCsvExcelService: subToArray called");
        $temp = [];
        
        $houseAll = $this->propertyService->subToExport($info['house_ids']);
        
        if (empty($houseAll)) {
            return $temp;
            
        }

        $subto_html = '<div style="text-align:center; color:red; font-size:22px;">SubTo Report</div><hr/>';
        foreach ($houseAll as $key => $value) {

            $address_url = $this->houseTokenService->address_url($value['house_id'], $value);
            $parse_data = [];

            $parse_data['county']               = CommonHelper::emptyDefault($value->county);
            $parse_data['address']              = CommonHelper::emptyDefault($value->address);
            $parse_data['sale_date']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $sale_type_array                    = config('property_information.sale_type');
            $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
            $parse_data['cma_arv']                  = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['lien_amount']              = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['date_recorded']            = CommonHelper::emptyDefaultObject(@$value->first_liens, 'date_recorded', 'date');
            $parse_data['no_str']                   = @$value->first_liens->no_str_no_appt?'Yes':'No';
            $parse_data['second_lien_amount']        = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_date_recorded']      = CommonHelper::emptyDefaultObject(@$value->second_liens, 'date_recorded', 'date');
            $parse_data['second_no_str']            = @$value->second_liens->no_str_no_appt?'Yes':'No';
            $parse_data['third_lien_amount']        = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_date_recorded']      = CommonHelper::emptyDefaultObject(@$value->third_liens, 'date_recorded', 'date');
            $parse_data['third_no_str']             = @$value->third_liens->no_str_no_appt?'Yes':'No';
            $parse_data['hoa_name']                 = CommonHelper::emptyDefault(@$value->hoa_liens->hoa_name);
            $parse_data['hoa_lien_date']            = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'date_of_hoa_lien', 'date');
            $parse_data['redemption_date']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_date', 'date');
            $parse_data['notice_email']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['tax_name']                 = CommonHelper::emptyDefault(@$value->tax_liens->tax_name);
            $parse_data['date_of_tax_lien']                   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'date_of_tax_lien', 'date');
            $parse_data['redemption_date']          = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_date', 'date');
            $parse_data['address_url']              = $address_url;
            $parse_data['city']                 = CommonHelper::emptyDefault($value->city);
            $parse_data['zip']                  = CommonHelper::emptyDefault($value->zip);
           
            $subto_html  .= view('pdfs.subto_export', $parse_data);

        }

        $html       =  $subto_html;
       
        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function winningBidderArray($info){
        $houseAll = $this->propertyService->winningBidderExport($info['house_ids']);
        
        if (empty($houseAll)) {
            return $temp;
        }

        $header   = [];
        $header[] = "Address";
        $header[] = 'County';
        $header[] = 'city';
        $header[] = 'zip';
        $header[] = 'Sale Date';
        $header[] = 'Sale Type';
        $header[] = 'Case Number';
        $header[] = 'Opening Bid';
        $header[] = 'Winning Bidder';
        $header[] = 'winning bid amount';
        $header[] = 'bid #';
        $header[] = 'Bidder Phone';
        $header[] = 'Bidder Address';
        $header[] = 'Owner Name';
        $header[] = 'Owner Address';
        $header[] = 'Owner Phone#';
        $header[] = 'Record url';
        $temp[]   = $header;
       
        foreach ($houseAll as $key => $value) {
           
            //$address_url = $this->houseTokenService->address_url($value->house_id, $value);
            $address_url        = env("APP_FRONTEND") . 'home/showdetail/' . $value->house_id . '/' . CommonHelper::url_slug($value);

            $parse_data                         = [];
            $parse_data['address']              = CommonHelper::emptyDefault($value->address);
            $parse_data['county']               = CommonHelper::emptyDefault($value->county);
            $parse_data['city']                 = CommonHelper::emptyDefault($value->city);
            $parse_data['zip']                  = CommonHelper::emptyDefault($value->zip);
            
            $parse_data['sale_date']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
           
            $sale_type_array                    = config('property_information.sale_type');
            $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
            $parse_data['case_number']          =  CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['opening_bid']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['wining_bidder']        = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['bid']                  = CommonHelper::emptyDefaultObject(@$value->last_sale_details->bidders[0], 'bidderCount', '');
            $parse_data['phone']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'phone', '');
            $parse_data['bidder_address']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'address', '');
            $parse_data['full_name']            = CommonHelper::emptyDefaultObject(@$value->last_owner_info, 'full_name', '');
            $parse_data['owner_full_address']   = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            $parse_data['owner_phone']          = CommonHelper::emptyDefault(@$value->last_owner_info->phone);
            $parse_data['Link to']      = $address_url;
            $temp[]                             = $parse_data;
        }
        
        return $temp;

    }

    function winningBidderPdf($info, $is_binary = false) {

        Log::info("UrlPdfCsvExcelService: quickViewPdf called");
        #ToDO: Make common function for this.
        $houseAll = $this->propertyService->winningBidderExport($info['house_ids']);

        if (empty($houseAll)) {
            return $temp;
        }

        $bidder_html = '<div style="text-align:center; color:red; font-size:22px;">Winning Bidder Report</div><hr/>';
        foreach ($houseAll as $key => $value) {

            //$address_url = $this->houseTokenService->address_url($value['house_id'], $value);
            $address_url        = env("APP_FRONTEND") . 'home/showdetail/' . $value->house_id . '/' . CommonHelper::url_slug($value);

            $parse_data = [];

            $parse_data['address_url']            = $address_url;
            $parse_data['address']                = CommonHelper::emptyDefault($value->address);
            $parse_data['county']                 = CommonHelper::emptyDefault($value->county);
            $parse_data['sale_date']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');

            $sale_type_array                    = config('property_information.sale_type');
            $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
            $parse_data['case_number']          =  CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'case_number', '');
            $parse_data['opening_bid']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['wining_bidder']        = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bid']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['bid']                  = CommonHelper::emptyDefaultObject(@$value->last_sale_details->bidders[0], 'bidderCount', '');
            $parse_data['phone']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'phone', '');
            $parse_data['bidder_address']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'address', '');
            $parse_data['full_name']            = CommonHelper::emptyDefaultObject(@$value->last_owner_info, 'full_name', '');
            $parse_data['owner_full_address']   = CommonHelper::emptyDefault(@$value->last_owner_info->full_address);
            $parse_data['owner_phone']          = CommonHelper::emptyDefault(@$value->last_owner_info->phone);
            $bidder_html                      .= view('pdfs.winning_bidder_export', $parse_data);
            
        }

        $html       =  $bidder_html;

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        // echo $this->disk->get('temp/' . $store_file_name); //getDriver()->getAdapter()->getPathPrefix();
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;

    }

    function texasAuctionExportToArray($info) {
        Log::info("UrlPdfCsvExcelService: texasAuctionExportToArray called");
        
        
        $temp   = [];
        $houseAll = $this->propertyService->findTexasAuctionDetails2(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return $temp;
        }
        $header   = [];
       // 
        $header[] = "Address";
        $header[] = "Property Link";
        $header[] = "Owner Name";
        $header[] = "Sale Date";
        $header[] = "Opening Bid";
        $header[] = "Winning Bid";
        $header[] = "Winning Bidder";
        $header[] = "Truste caller Notes";
        $header[] = "Frcl Type";
        $header[] = '1st Lien $';
        $header[] = '1st Loan Estimated Balance';
        $header[] = "Total Estimated Debt w/ Late Payments & Attorney Fees $";
        $header[] = "Total Estimated Late Payments and Fees %";
        $header[] = '2nd Lien $';
        $header[] = '2nd Loan Estimated Balance';
        $header[] = '3rd Lien $';
        $header[] = '3rd Loan Estimated Balance';
        $header[] = 'HOA Name';
        $header[] = "CMA/ARV Value";
        $header[] = "Property Type";
        $header[] = "Total Estimated Equity";
        $header[] = "Clear";
        $header[] = "Pacer";
        $header[] = "Sub To";
        $header[] = "Affidavit (APM) Date";
        $header[] = "HOA Redemption Expiration Date";
        $header[] = "Tax Redemption Expiration Date";
        $header[] = "Record Source";
        $header[] = "Trusteee";
        $header[] = "County";
        $header[] = "Legal Description";
        $header[] = "Year Built";
        $header[] = 'Zestimate';
        $header[] = 'Amount Owed HOA Lien';
        $header[] = 'Amount Owed Taxes';
        $header[] = 'Buyer';
        
        $header[] = '';
        $temp[] = $header;

        

        foreach ($houseAll as $key => $value) {

            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data                             = [];
            
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['address_url']              = $address_url;
            $parse_data['owner_full_name']          = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['opening_bid']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['winning_bid_amount']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['winning_bidder']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['Truste caller Notes']      = '';
            
            //$parse_data['hoa_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bid', 'currency');
            $sale_type_array                        = config('property_information.sale_type');
            $sale_type                              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']                = @$sale_type_array[@$sale_type];

            $parse_data['1st Lien $']               = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            
            $parse_data['total_est_debt']           = $this->calculateTotalEstDebt($value);
            $parse_data['total_est_late_fee']       = $this->calculateTotalEstLateFee($value);

            $parse_data['2nd Lien $']               = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['3rd Lien $']               = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            //$parse_data['HOA Lien $']               = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['cma_arv']                  = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            
            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');
            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['property_type'] =$spPropertyType;
            $parse_data['Total Est Equity'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['Clear']                    = "";
            $parse_data['Pacer']                    = "";
            $parse_data['Sub To']                   = "";
            //$parse_data['wining_bidder']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            //$parse_data['winning_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['hoa_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            $parse_data['tax_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_expires', 'date');
            $parse_data['record_from']              = '';
            $parse_data['Trustee']                  = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['legal_description']        = CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['Year Built']               = CommonHelper::emptyDefault($value->year_built);
            $parse_data['zestimates']               = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'zestimate', 'currency');
            $parse_data['Amount Owed HOA Lien']     = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['Amount Owed Taxes']        =$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['buy_it_request']           = $this->getBuyItRequestList($value['house_id']);
            $parse_data['DAILY_CHECKER']            = '';
            $temp[]                                 = $parse_data;
        }

        return $temp;

            
    }

    function texasAuctionExportToPdf($info, $is_binary = false) {
        Log::info("UrlPdfCsvExcelService: texasAuctionExportToPdf called");

        #ToDO: Make common function for this.
        // Common Code start
        $houseAll = $this->propertyService->findTexasAuctionDetails2(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $parse_data                = [];
         $html = '<div style="text-align:center; color:red; font-size:22px;">Texas Auction Report</div><hr/>';
        $count=1;
        foreach ($houseAll as $key => $value) {
            $parse_data                            = [];
            $link        = env("APP_FRONTEND") . 'home/showdetail/' . $value->house_id . '/' . CommonHelper::url_slug($value);
            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data                             = [];
            
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['address_url']              = $address_url;
            $parse_data['owner_full_name']          = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['opening_bid']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['winning_bid_amount']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['winning_bidder']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['truste_caller_notes']      = '';
            
            //$parse_data['hoa_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bid', 'currency');
            $sale_type_array                        = config('property_information.sale_type');
            $sale_type                              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']                = @$sale_type_array[@$sale_type];

            $parse_data['first_lien']               = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            
            $parse_data['total_est_debt']           = $this->calculateTotalEstDebt($value);
            $parse_data['total_est_late_fee']       = $this->calculateTotalEstLateFee($value);

            $parse_data['second_lien']               = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['third_lien']               = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            //$parse_data['HOA Lien $']               = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['cma_arv']                  = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            
            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');
            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['property_type'] =$spPropertyType;
            $parse_data['total_est_equity'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['clear']                    = "";
            $parse_data['Pacer']                    = "";
            $parse_data['Sub_to']                   = "";
            //$parse_data['wining_bidder']          = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            //$parse_data['winning_bid']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['hoa_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            $parse_data['tax_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_expires', 'date');
            $parse_data['record_from']              = '';
            $parse_data['trustee']                  = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['legal_description']        = CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['year_built']               = CommonHelper::emptyDefault($value->year_built);
            $parse_data['zestimates']               = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'zestimate', 'currency');
            $parse_data['amount_owed_HOA_lien']     = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['amount_owed_taxes']        =$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['buy_it_request']           = $this->getBuyItRequestList($value['house_id']);
            $parse_data['DAILY_CHECKER']            = '';



            $parse_data['trustee'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['hoa_name'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            $parse_data['sale_date'] = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $sale_type_array                    = config('property_information.sale_type');
            $sale_type                          = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']            = @$sale_type_array[@$sale_type];
            $parse_data['street_name'] = CommonHelper::emptyDefault($value->address);            
            $parse_data['year_built']        = CommonHelper::emptyDefault($value->year_built);
            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');

            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['specific_property_type'] =$spPropertyType;
            $parse_data['legal_description'] = CommonHelper::emptyDefault(@$value->legal_description);
            
            $parse_data['owner_info']= @$value->owner_info;
            
            $parse_data['cma_arv'] = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['zestimates']         = CommonHelper::emptyDefaultObject(@$value->local_real_estate_details, 'zestimate', 'currency');
            $parse_data['first_no_str']  = @$value->first_liens->no_str_no_appt?'Yes':'No';
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['first_total_est_debt'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'total_est_debt', 'currency');
            $parse_data['second_no_str']  = @$value->second_liens->no_str_no_appt?'Yes':'No';
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['second_total_est_debt'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'total_est_debt', 'currency');
            $parse_data['third_no_str']  = @$value->third_liens->no_str_no_appt?'Yes':'No';
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['third_total_est_debt'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'total_est_debt', 'currency');
            $parse_data['hoa_lien_amount'] = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            //$parse_data['taxes_amount'] = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'tax_lien_amount', 'currency');
            $parse_data['taxes_amount']=$this->propertyService->getSumPropertyOwed($value->house_id); 
            $parse_data['address_url'] = $link;
            $parse_data['number_of_nos'] = $this->propertyService->getSaleDocumentByHouseId($value->house_id);
            $parse_data['buy_it_request']         = $this->getBuyItRequestList($value['house_id']);
            $parse_data['page_brack'] =($count%4==0)?true:false;
            $html .= view('pdfs.texas_auction_2', $parse_data);
            $count++;
        }

        $pdf_binary = $this->pdf->loadHTML($html)->output();

        if ($is_binary) {
            return $pdf_binary;
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);

        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;
    }


    function redemptionExcessFundArray($info) {
        Log::info("UrlPdfCsvExcelService: quickViewArray called");
        $temp = [];
        $houseAll = $this->propertyService->redemptionExcessFundExport($info['house_ids']);
        if (empty($houseAll)) {
            return $temp;
        }
       
        $header   = [];
        $header[] = "County";
        $header[] = "Address";
        $header[] = "Property Link";
        $header[] = "Owner Name";
        $header[] = "Sale Date";
        $header[] = "Opening Bid";
        $header[] = "Winning Bid";
        $header[] = "Winning Bidder";
        $header[] = "Truste caller Notes";
       // $header[] = 'Legal Description';
        $header[] = "Frcl Type";
        $header[] = '1st Lien $';
        $header[] = '1st Loan Estimated Balance';
        $header[] = "Total Estimated Debt w/ Late Payments & Attorney Fees $";
        $header[] = "Total Estimated Late Payments and Fees %";
        $header[] = '2nd Lien $';
        $header[] = '2nd Loan Estimated Balance';
        $header[] = '3rd Lien $';
        $header[] = '3rd Loan Estimated Balance';
        $header[] = 'HOA / TAX Line';
        $header[] = "CMA/ARV Value";
        $header[] = "Property Type";
        $header[] = "Affidavit (APM) Date";
        $header[] = "HOA Redemption Expiration Date";
        $header[] = "Tax Redemption Expiration Date";
        $header[] = "Record From";
        $header[] = "Trusteee";
        $header[] = "DAILY CHECKER / DATE";

        $temp[]   = $header;

        foreach ($houseAll as $key => $value) {

            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data                             = [];
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            $parse_data['address_url']              = $address_url;
            $parse_data['owner_full_name']          = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $parse_data['opening_bid']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['winning_bid_amount']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['winning_bidder']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['Truste caller Notes']      = '';
            //$parse_data['legal_description']        = CommonHelper::emptyDefault(@$value->legal_description);
            //$parse_data['hoa_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bid', 'currency');
            $sale_type_array                        = config('property_information.sale_type');
            $sale_type                              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']                = @$sale_type_array[@$sale_type];

            $parse_data['1st Lien $']               = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            
            $parse_data['total_est_debt']           = $this->calculateTotalEstDebt($value);
            $parse_data['total_est_late_fee']       = $this->calculateTotalEstLateFee($value);

            $parse_data['2nd Lien $']               = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['3rd Lien $']               = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            //$parse_data['HOA Lien $']               = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['cma_arv']                  = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            
            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');
            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['property_type'] =$spPropertyType;

            //$parse_data['wining_bidder']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            //$parse_data['winning_bid']             = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['hoa_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            $parse_data['tax_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_expires', 'date');
            $parse_data['record_from']              = '';
            $parse_data['Trustee']                  = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['DAILY_CHECKER']            = '';
            $temp[]                               = $parse_data;
        }

        return $temp;
    }

    function calculateTotalEstDebt($value){
        $firstLienEstDebt=CommonHelper::emptyNumberVal(@$value->first_liens, 'total_est_debt', '0');
        $secondLienEstDebt=CommonHelper::emptyNumberVal(@$value->second_liens, 'total_est_debt', '0');
        $thirdLienEstDebt= CommonHelper::emptyNumberVal(@$value->third_liens, 'total_est_debt', '0');
        return  ($firstLienEstDebt+$secondLienEstDebt+$thirdLienEstDebt);
    }

    function calculateTotalEstLateFee($value){
        $firstLienEstLateFee=CommonHelper::emptyNumberVal(@$value->first_liens, 'est_late_payment_and_fees', '0');
        $secondLienEstLateFee=CommonHelper::emptyNumberVal(@$value->second_liens, 'est_late_payment_and_fees', '0');
        $thirdLienEstLateFee= CommonHelper::emptyNumberVal(@$value->third_liens, 'est_late_payment_and_fees', '0');
        return  ($firstLienEstLateFee+$secondLienEstLateFee+$thirdLienEstLateFee);
    }

    function redemptionExcessFundPdf($info, $is_binary = false) {
        Log::info("UrlPdfCsvExcelService: texasAuctionExportToPdf called");

        #ToDO: Make common function for this.
        // Common Code start
       
        $houseAll = $this->propertyService->redemptionExcessFundExport(array_unique($info['house_ids']));
        if (empty($houseAll)) {
            return false;
        }

        $parse_data                = [];
         $html = '<div style="text-align:center; color:red; font-size:22px;">Redemption Excess Fund Report</div><hr/>';
        $count=1;
        foreach ($houseAll as $key => $value) {
            $parse_data                            = [];
            $link        = env("APP_FRONTEND") . 'home/showdetail/' . $value->house_id . '/' . CommonHelper::url_slug($value);

            $address_url = CommonHelper::showdetail_url($value['house_id'], $value);

            $parse_data                             = [];
            $parse_data['county']                   = CommonHelper::emptyDefault($value->county);
            $parse_data['address_url']              = $address_url;
            $parse_data['legal_description']        = CommonHelper::emptyDefault(@$value->legal_description);
            $parse_data['opening_bid']              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'opening_bid', 'currency');
            $parse_data['address']                  = CommonHelper::emptyDefault($value->address);
            
            $parse_data['winning_bid_amount']       = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            
            
            //$parse_data['hoa_winning_bid']          = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'winning_bid', 'currency');
            $parse_data['sale_date']                = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_date', 'date');
            $sale_type_array                        = config('property_information.sale_type');
            $sale_type                              = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'sale_type', '');
            $parse_data['sale_type']                = @$sale_type_array[@$sale_type];

            $property_type_array         = config('property_information.specific_property_types');
            $property_types_array         = config('property_information.property_types');
            
            $spPropertyType='N/A';
            if(!empty($value->property_type)){
                $specific_property_type      = CommonHelper::emptyDefault(@$value->specific_property_type, '');
                $property_type      = $property_types_array[CommonHelper::emptyDefault(@$value->property_type, '')];
                $spPropertyType =   @$property_type_array[@$value->property_type][@$property_type][@$specific_property_type];

            }
            $parse_data['property_type'] =$spPropertyType;

            $parse_data['first_lien_amount']             = CommonHelper::emptyDefaultObject(@$value->first_liens, 'lien_amount', 'currency');
            $parse_data['first_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->first_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['second_lien_amount']               = CommonHelper::emptyDefaultObject(@$value->second_liens, 'lien_amount', 'currency');
            $parse_data['second_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->second_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['third_lien_amount']               = CommonHelper::emptyDefaultObject(@$value->third_liens, 'lien_amount', 'currency');
            $parse_data['third_loan_estimated_balance'] = CommonHelper::emptyDefaultObject(@$value->third_liens, 'amortization_loan_estimate_balance', 'currency');
            $parse_data['hoa_name']                 = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_name', '');
            //$parse_data['HOA Lien $']               = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'hoa_lien_amount', 'currency');
            $parse_data['cma_arv']                  = CommonHelper::emptyDefaultObject(@$value->last_cma_arv_recommendations, 'recommended_cma_arv', 'currency');
            $parse_data['owner_full_name']          = CommonHelper::emptyDefault(@$value->last_owner_info->full_name);
            //$parse_data['wining_bidder']            = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            $parse_data['winning_bidder']           = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'wining_bidder', '');
            //$parse_data['winning_bid']             = CommonHelper::emptyDefaultObject(@$value->last_sale_details->last_bidder, 'winning_bid', 'currency');
            $parse_data['hoa_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'redemption_expires', 'date');
            $parse_data['tax_redemption_expires']   = CommonHelper::emptyDefaultObject(@$value->tax_liens, 'redemption_expires', 'date');

            //$parse_data['hoa_affidavit_date']       = CommonHelper::emptyDefaultObject(@$value->hoa_liens, 'affidavit_date', 'date');
            $parse_data['trustee']                  = CommonHelper::emptyDefaultObject(@$value->last_sale_details, 'trustee');
            $parse_data['total_est_debt']           = $this->calculateTotalEstDebt($value);
            $parse_data['total_est_late_fee']       = $this->calculateTotalEstLateFee($value);

            $temp[]                               = $parse_data;
            $html .= view('pdfs.redemption_excess_fund', $parse_data);
            $count++;
        }

        $pdf_binary = $this->pdf->loadHTML($html)->output();
        
        if ($is_binary) { 
            return $pdf_binary; 
        }

        $store_file_name = "GLPF_" . time() . $this->userService->user_id() . '' . '.pdf';
        $this->disk->put('temp/' . $store_file_name, $pdf_binary);
        $document_url = $this->disk->url('temp/' . $store_file_name);
        return $document_url;
    }

}
