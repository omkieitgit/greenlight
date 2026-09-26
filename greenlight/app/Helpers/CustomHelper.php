<?php
/**
 * Created by Rativardhan Singh Sengar on 9/20/18 10:46 PM
 * Copyright (c) 2018 . All rights reserved.
 * Last modified 9/20/18 10:46 PM
 */

namespace App\Helpers;

class CustomHelper  {
    /**
     * Return Message into one array
     *
     * @param $validator
     * @return array
    Log::info("DepositWiredService: findAllByHouseId called");
    if($is_cache == true)
    {
    return $this->findAllByHous
     *
     */
    public static function date_format_database($date)
    {
        $date = str_replace("-", "/", $date);

        if ($date == "0000/00/00" || $date == "00-00-0000" || empty($date))
            return NULL;

        return date("Y-m-d", strtotime($date));;
    }


    public static function revert_date_format_database($date)
    {
        if ($date == "0000-00-00" || empty($date))
            return NULL;

        $date = str_replace('-', '/', $date);
        return date("m/d/Y", strtotime($date));;
    }

}

