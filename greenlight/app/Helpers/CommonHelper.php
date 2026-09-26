<?php
/**
 * Created by Rativardhan Singh Sengar on 9/20/18 10:46 PM
 * Copyright (c) 2018 . All rights reserved.
 * Last modified 9/20/18 10:46 PM
 */

namespace App\Helpers;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommonHelper {

    public static function getPublicMediaURl($file_name, $folder_name) {
        if(empty($file_name))
            return '';

        $store_file_name = $file_name;
        $url = url('document/'.$folder_name . $store_file_name);

        return $url;
    }

    public static function startEndDateCheck(&$from, &$to) {
        $from_strtotime = strtotime($from);
        $to_strtotime = strtotime($to);
        if (!empty($from) && !empty($to)) {

            if (($from_strtotime) > ($to_strtotime)) {
                $temp = $from;
                $from = $to;
                $to = $temp;
            } else {

            }
        }
    }

    public static function strToTime($date) {
        if(empty($date))
            return $date;
        return strtotime($date);
    }

    public static function dbNumberFormat($str) {
        if (empty($str) && $str != 0)
            return null;
        $str = preg_replace("/[^0-9.-]/", "", $str);
        if (is_numeric($str)) {

            if ($str > 9999999999.99)
                return 9999999999.99;

            if ($str < -9999999999.99)
                return -9999999999.99;

            return $str;
        }

        return null;

    }

    public static function intToDate($date)
    {

        if(is_numeric($date))
        {
            return date('Y-m-d H:i:s', $date);
        }

        return $date;
    }

    /**
     * Return Message into one array
     *
     * @param $validator
     * @return array
     *
     */
    public static function customValidatorMessageArray($validator) {
        Log:info("CommonHelper:customValidatorMessageArray called");
        $errors  = $validator->errors();
        $message = [];
        foreach ($errors->all() as $value) {
            $message[] = $value;
        }
        return $message;
    }

    /**
     * Return api standard response
     *
     * @param $data
     * @return json
     *
     */
    public static function apiResponse($response) {
        $code       = isset($response['code']) ? $response['code'] : 0;
        $message    = isset($response['message']) ? $response['message'] : "NA";
        $data       = isset($response['data']) ? $response['data'] : array();
        $pegination = isset($response['pegination']) ? $response['pegination'] : array();
        $response   = array(
            "meta"       => array(
                "code"    => $code,
                "message" => $message
            ),
            "data"       => $data,
            "pegination" => $pegination
        );
        return json_encode($response);
    }

    public static function showdetail_url($house_id, $property_info)
    {
        return env("APP_FRONTEND") . 'home/showdetail/' . $house_id . '/' . CommonHelper::url_slug($property_info);
    }

    public static function url_slug($objectArray) {
        if (empty($objectArray))
            return false;

        $address      = @$objectArray->address . ' ' . @$objectArray->city . ' ' . @$objectArray->state . ' ' . @$objectArray->zip;
        $temp_address = preg_replace("/[^A-Za-z0-9 ]/", '', $address);
        return urlencode(str_replace(" ", "-", trim($temp_address)));
    }

    public static function addressFormat($objectArray) {
        if (is_object($objectArray)) {
            $address = @$objectArray->address . ' ' . @$objectArray->city . ' ' . @$objectArray->state . ' ' . @$objectArray->zip;

        }
        else if (is_array($objectArray)) {
            $address = $objectArray['address'] . ' ' . @$objectArray['city'] . ' ' . @$objectArray['state'] . ' ' . @$objectArray['zip'];
        }
        return $address;
    }

    public static function countyAddressFormat($objectArray) {

        $address  = '' ;
        if (is_object($objectArray)) {
            $address = @$objectArray->county . ', '.@$objectArray->address . ' '  . @$objectArray->state . ' ' . @$objectArray->zip;

        }
        else if (is_array($objectArray)) {
            $address =  @$objectArray['county'] . ', '.$objectArray['address'] . ' ' .' ' . @$objectArray['state'] . ' ' . @$objectArray['zip'];
        }
        return $address;
    }


    public static function nameFormat($userArray) {
        if (is_object($userArray)) {
            $top_user = $userArray->username;
            if(CommonHelper::isEmail($top_user))
            {
                $top_user = Str::of($top_user)->explode('@')[0];
            }
            if (!empty($userArray->first_name))

                $top_user = $userArray->first_name . ' ' . $userArray->last_name;
            if (empty($top_user) || $top_user == " ") {
                $top_user = 'A user ';
            }

            return $top_user;
        }
        else if (is_array($userArray)) {
            $top_user = $userArray['username'];
            if(CommonHelper::isEmail($top_user))
            {
                $top_user = Str::of($top_user)->explode('@')[0];
            }

            if (!empty($userArray['first_name']))
                $top_user = $userArray['first_name'] . ' ' . $userArray['last_name'];

            if (empty($top_user) || $top_user == " ") {
                $top_user = 'A user ';
            }

            return $top_user;
        }
        return 'User: Name Missing';

    }
    public static function isEmail($string) {
        if(strpos($string,"@") !== false && strpos($string,".") !== false)
        {
            return true;
        }

        return false;
    }


    public static function maskEmail($str) {
        $parts = explode('@', $str);
        $email = substr($parts[0],0,1).str_repeat("x", strlen($parts[0])-1).'@'.$parts[1];
        return $email;
    }


    public static function mapType($objectArray) {
        if (is_object($objectArray)) {
            $address  = @$objectArray->address . ' ' . @$objectArray->city . ' ' . @$objectArray->state . ' ' . @$objectArray->zip;
            $map_type = 'http://maps.google.com/?q=' . urlencode($address);
        }
        else if (is_array($objectArray)) {
            $address  = $objectArray['address'] . ' ' . @$objectArray['city'] . ' ' . @$objectArray['state'] . ' ' . @$objectArray['zip'];
            $map_type = 'http://maps.google.com/?q=' . urlencode($address);
        }

        return $map_type;
    }

    public static function address_url_anchor($link) {
        return "<a href='" . $link . "'>" . $link . '</a>';
    }

    public static function get_distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit  = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        }
        else if ($unit == "N") {
            return ($miles * 0.8684);
        }
        else {
            return $miles;
        }
    }

    public static function emptyDefault($str, $default = '_ _') {
        if (empty($str))
            return $default;
        return $str;
    }

    public static function emptyNumberDefault($str, $default = '_ _') {
        if (empty($str))
            return $default;
        return self::numberFormat($str);
    }

    public static function emptyNumberVal($object, $key, $default = '0') {
        if (empty($object) || ($object[$key] !== 0 && empty($object[$key])))
            return $default;

        return $object[$key];
    }

    public static function numberFormat($str, $decimal = 2) {
        return number_format(trim($str), $decimal, NULL, ',');
    }

    public static function emptyMoneyDefault($str) {
        if (empty($str))
            return '_ _';
        return self::moneyFormat($str);
    }

    public static function moneyFormat($str, $decimal = 2) {
        Log:info("CommonHelper:moneyFormat called ==>".$str);
        if(!empty($str)){
            $str=str_replace(',','',$str);
            return '$' . number_format(trim($str), $decimal, NULL, ',');
        }else{
            return '';
        }
    }

    public static function lengthCheckerSplitStringToLimitDotted($str, $length = 100) {
        $str = trim($str);
        return strlen($str) > $length ? substr($str, 0, $length) . '....' : $str;
    }

    public static function emptyDateFormat($date,$default = '_ _') {
        if (empty($date))
            return $default;
        return date("M-d-Y", strtotime($date));
    }

    public static function dateFormat($date) {
        return date("M-d-Y", strtotime($date));
    }

    public static function emptyPercentageDefault($str,  $default = '_ _') {
        if (empty($str))
            return $default;
        return self::numberFormat($str) . '%';
    }

    public static function percentageDefault($str) {
        return self::numberFormat($str) . '%';
    }

    public static function emptyDefaultObject($object, $key, $format = '') {
        if (empty($object) || ($object[$key] !== 0 && empty($object[$key])))
            return '_ _';

        if ($format == 'number')
            return self::numberFormat($object[$key]);
        else if ($format == 'currency')
            return self::moneyFormat($object[$key]);
        else if ($format == 'date')
            return self::dateFormat($object[$key]);
        else if ($format == 'percentage')
            return self::percentageDefault($object[$key]);


        return $object[$key];
    }


    public static function arrayToCSV($csvArray) {

        ob_start();
        $f = fopen('php://output', 'w') or show_error("Can't open php://output");
        $n = 0;

        foreach ($csvArray as $line) {
            $n++;
            if (!fputcsv($f, $line)) {
                show_error("Can't write line $n: $line");
            }
        }

        fclose($f) or show_error("Can't close php://output");
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    public static function decimalRemovalCharacter($str) {
        if (empty($str) && $str != 0)
            return null;
        $str = preg_replace("/[^0-9.-]/", "", $str);
        if (is_numeric($str)) {

            if ($str > 9999999999.99)
                return 9999999999.99;

            if ($str < -9999999999.99)
                return -9999999999.99;

            return $str;
        }

        return null;

    }

    public static function searchIndexByValue($str, $array, $default) {
        $isFind = array_search(
            strtolower($str),
            array_map('strtolower', $array)
        );

        if ($isFind === false)
            return $default;
        return $isFind;
    }

    public static function searchSimilarMatch($str, $array, $default) {
        $isPer = 0;
        $temp  = $default;

        foreach ($array as $key => $value) {

            if (is_array($value))
                continue;

            similar_text(strtolower($str), strtolower($value), $matchPer);

            if ($matchPer > 40 && $matchPer > $isPer) {
                $isPer = $matchPer;
                $temp  = $key;
            }
        }

        return $temp;
    }

    public static function documentPictureStorage($store_name) {
        //if (empty($store_name)) {
            $path_binary = file_get_contents(resource_path('assets/images/no-image.gif'));
            return 'data:image/jpg;base64,' . base64_encode($path_binary);
       // }

        $store_file_name       = $store_name;

        #-----------------------
        // Hard coded URL for temporary changes
        #ToDO; Remove bottom URL
        $url = '';
        if (Storage::disk('public')->exists('document_picture/' . $store_file_name) === false)
            $url = "http://greenlightpropertyfinder.com/pictures/" . $store_file_name;
        #-----------------------

        if(empty($url))
        {
            $image_path            = Storage::disk('public')->get('document_picture/' . $store_file_name);
            return 'data:image/jpg;base64,' . base64_encode($image_path);
        }
        else
        {
            $path_binary = file_get_contents($url);
            return 'data:image/jpg;base64,' . base64_encode($path_binary);
        }

    }

    public static function decimalNumberLimitV2($str, $is14 = false)
    {
        if (empty($str) && $str != 0)
            return null;

        $str = preg_replace("/[^0-9.-]/", "", $str);

        if($str == '')
            return NULL;

        if (is_numeric($str)) {

            if ($str > 999999999999.99)
                return 999999999999.99;

            if ($str < -999999999999.99)
                return -999999999999.99;

            return $str;
        }

        return $str;
    }

    public static function decimalNumberLimit($str, $max = 0, $min = 0)
    {
        $str = CommonHelper::decimalRemovalCharacter($str);
        if($str == '')
            return NULL;

        if($max>0 && $str > $max)
        {
            return $max;
        }

        if($min < 0 && $str < $min)
        {
            return $min;
        }

        return $str;
    }

    public static function numberOrdinal($number)
    {
        $locale = 'en_US';
        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
        return $nf->format($number);
    }


    /* Dummy code temp only*/
    public static function dbIntValValue255($str) {
        return intval($str) > 255
            ? 255
            : (
            intval($str) < 0 ?

                (0 - intval($str) > 255 ? 255 : 0 - intval($str))

                : intval($str)
            );
    }

    public static function dbIntValValue999999($str, $length = 999999) {
        return intval($str) > $length
            ? $length
            : (
            intval($str) < 0 ?
                (0 - intval($str) > $length ? $length : 0 - intval($str))
                : intval($str)
            );
    }

    public static function dbIsNumericValue255($str) {

        return is_numeric($str)
            ? ($str > 255 ? 255 : $str)
            : (
            intval($str) < 0 ?

                (0 - intval($str) > 255 ? 255 : 0 - intval($str))

                : intval($str)
            );
    }

    public static function dbDateFormat($str, $isStr = 0) {


        if (empty($str) || $str == "0000-00-00")
            return null;

        $temp   = date("Y-m-d", strtotime($str));
        $substr = substr($temp, 0, 4);
        if ($substr == '-000' || $substr == '0000') {
            return null;
        }

        if ($isStr == 1) {
            return strtotime($str);
        }

        return $temp;

    }

    public static function dbDateFormatHIS($str, $isStr = 0) {


        if (empty($str) || $str == "0000-00-00")
            return null;

        $temp   = date("Y-m-d H:i:s", strtotime($str));
        $substr = substr($temp, 0, 4);
        if ($substr == '-000' || $substr == '0000') {
            return null;
        }

        if ($isStr == 1) {
            return strtotime($str);
        }

        return $temp;

    }

    public static function isBoolean($str) {

        return strtolower($str) == 'yes' ? 1 : 0;

    }

    public static function lengthChecker($str, $length = 15, $default = null) {

        return strlen($str) > $length ? $default : $str;
    }

    public static function addressMakerFormat($address, $city, $county, $state, $zipcode) {

        $fa = '';
        if (!empty($address)) {
            $address = self::lengthCheckerSplitStringToLimit($address, 120);

            $fa .= trim($address);
        }

        if (!empty($city)) {
            if (!empty($fa))
                $fa .= ', ';

            $city = self::lengthCheckerSplitStringToLimit($city, 50);
            $fa   .= trim($city);
        }

        if (!empty($county)) {
            if (!empty($fa))
                $fa .= ', ';
            $county = self::lengthCheckerSplitStringToLimit($county, 50);
            $fa     .= trim($county);
        }

        if (!empty($state)) {
            if (!empty($fa))
                $fa .= ', ';
            $state = self::lengthCheckerSplitStringToLimit($state, 25);
            $fa    .= trim($state);
        }

        if (!empty($zipcode)) {
            if (!empty($fa))
                $fa .= ', ';
            $zipcode = self::lengthCheckerSplitStringToLimit($zipcode, 10);
            $fa      .= trim($zipcode);
        }

        if (strlen($fa) > 255) {
            echo '----------';
            echo $address;
            echo '<br/>';
            echo '----------';
            echo $city;
            echo '<br/>';
            echo '----------';
            echo $county;
            echo '<br/>';
            echo '----------';
            echo $state;
            echo '<br/>';
            echo '----------';
            echo $zipcode;
            echo '<br/>';
        }

        return $fa;
    }

    public static function lengthCheckerSplitStringToLimit($str, $length = 255) {
        $str = trim($str);
        return strlen($str) > $length ? substr($str, 0, $length) : $str;
    }

    public static function phoneCharRemove($str, $length= 255) {
        $str = trim($str);
        $str = preg_replace("/[^0-9.()-]/", "", $str);
        return strlen($str) > $length ? substr($str, 0, $length) : $str;
    }

    public static function parseEmailJunk($str, $isOriginal, $is_micro = true) {

        return $str;

        if ($isOriginal === true) {
            return $str;
        }

        if (strpos($str, '@') === false) {
            return $str;
        }

        $parts = explode('@', $str);

        if($is_micro == false)
        {
            return md5($str) .  '@' . 'mailinator.com';
        }

        return md5($str) .  '@' . 'mailinator.com';
    }

    public static function hbDiff($str1, $str2)
    {
        $diff = NULL;
        if ($str1 != '' && $str2 != '')
            $diff = CommonHelper::decimalRemovalCharacter($str1 )- CommonHelper::decimalRemovalCharacter($str2);

        return $diff;
    }
}

