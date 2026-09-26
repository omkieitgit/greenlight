<?php

namespace App\Services;

use App\Models\PropertyModel;
use App\Models\SaleDetailsModel;
use DB;

class ReportService{


    function getMonthlyReport($state,$county,$from_date, $to_date){
        
        $result=SaleDetailsModel::select([DB::raw("count(sale_date) as total_estimated_sale"),       
                DB::raw("DATE_FORMAT(sale_date,'%M %Y') as months"),
                DB::raw("SUM(IF(sale_bidder.amount_of_bid!=0, 1, 0)) as total_atual_estimated_sale"),
                DB::raw("SUM(IF(sale_type=2, 1, 0)) as total_est_1st_lien_frcl_sale"),
                DB::raw("SUM(IF(sale_type=2 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_est_1st_lien_frcl_sale"),

                DB::raw("SUM(IF(sale_type=3, 1, 0)) as total_est_2nd_lien_frcl_sale"),
                DB::raw("SUM(IF(sale_type=3 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_est_2nd_lien_frcl_sale"),

                DB::raw("SUM(IF(sale_type=4, 1, 0)) as total_est_3rd_lien_frcl_sale"),
                DB::raw("SUM(IF(sale_type=4 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_est_3rd_lien_frcl_sale"),

                DB::raw("SUM(IF(sale_type=5, 1, 0)) as hoa_sale"),
                DB::raw("SUM(IF(sale_type=5 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_hoa_sale"),

                DB::raw("SUM(IF(sale_type=6, 1, 0)) as tax_sale"),
                DB::raw("SUM(IF(sale_type=5 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_tax_sale"),

                DB::raw("SUM(IF(sale_type=0, 1, 0)) as total_no_sale_type_of_est_sale"),
                DB::raw("SUM(IF(sale_type=0 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_no_sale_type_of_est_sale"),
               
                DB::raw("SUM(IF(sale_type=18, 1, 0)) as total_est_timeshare_sale"),
                DB::raw("SUM(IF(sale_type=18 AND sale_bidder.amount_of_bid!=0, 1, 0)) as total_actual_est_timeshare_sale"),

                
                ])
                ->join('home_information','home_information.house_id','sale_details.house_id')
                ->leftjoin('sale_bidder','sale_bidder.sale_id','sale_details.sale_id')
                ->whereNotNull('sale_date')
                ->where('sale_date' ,'>=',$from_date)
                ->where('sale_date' ,'<=',$to_date);
        
        if(!empty($state)){
            $result = $result->where('state', $state);
        }
        if(!empty($county)){
            $result = $result->where('county', $county);
        }
        $result->groupBy('months');
        $result->orderBy("sale_date","DESC");
        return $result->get();


    }
}