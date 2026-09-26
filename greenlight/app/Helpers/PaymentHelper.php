<?php
/**
 * Created by Rativardhan Singh Sengar on 9/20/18 10:46 PM
 * Copyright (c) 2018 . All rights reserved.
 * Last modified 9/20/18 10:46 PM
 */

namespace App\Helpers;

class PaymentHelper {

    public static function calculateInternetFees($user_type, $total_records) {
        $cost = 0;

        if(in_array($user_type, ['first_dtc','second_dca','third_dca','chief_dca']))
        {
            if($total_records < 49)
            {
                $cost = 0;
            }
            else if($total_records <= 100)
            {
                $cost = 5;
            }
            else if($total_records <= 199)
            {
                $cost = 10;
            }
            else if($total_records <= 249)
            {
                $cost = 15;
            }
            else if($total_records >= 250)
            {
                $cost = 20;
            }
        }
        else if(in_array($user_type, ['nos_by']))
        {
            if($total_records < 499)
            {
                $cost = 0;
            }
            else if($total_records <= 899)
            {
                $cost = 10;
            }
            else
            {
                $cost = 20;
            }
        }

        return $cost;
    }

    public static function calculatePayRates($user_type, $userInfo) {

        $pay_rates = config('pay_rates');
        $pay_rates = @$pay_rates[@$userInfo->work_profile_team];
        $cost = @$pay_rates[$user_type]?$pay_rates[$user_type]:0.00;

        return $cost;
    }

}

