<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 9:32 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/27/18 5:42 PM
 */

namespace App\Http\Middleware;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class ConvertEmptyStringsToNull extends TransformsRequest
{

    public function __construct()
    {
        //Log::info(get_class($this).": __construct called");
    }
    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        // Log::info(get_class($this).": transform");
        return is_string($value) && $value === '' ? null : $value;
    }
}
