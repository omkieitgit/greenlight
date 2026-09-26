<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 9:33 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 11/6/18 9:32 PM
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\ParameterBag;

class TransformsRequest
{
    /**
     * The additional attributes passed to the middleware.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  ...$attributes
     * @return mixed
     */
    public function handle($request, Closure $next, ...$attributes)
    {
        //Log::info(get_class($this).": handle called");

        $this->attributes = $attributes;

        $this->clean($request);

        return $next($request);
    }

    /**
     * Clean the request's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clean($request)
    {
        //Log::info(get_class($this).": clean called");

        $this->cleanParameterBag($request->query);

        if ($request->isJson()) {
            $this->cleanParameterBag($request->json());
        } else {
            $this->cleanParameterBag($request->request);
        }
    }

    /**
     * Clean the data in the parameter bag.
     *
     * @param  \Symfony\Component\HttpFoundation\ParameterBag  $bag
     * @return void
     */
    protected function cleanParameterBag(ParameterBag $bag)
    {
        $bag->replace($this->cleanArray($bag->all()));
    }

    /**
     * Clean the data in the given array.
     *
     * @param  array  $data
     * @return array
     */
    protected function cleanArray(array $data)
    {
        return collect($data)->map(function ($value, $key) {
            return $this->cleanValue($key, $value);
        })->all();
    }

    /**
     * Clean the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function cleanValue($key, $value)
    {
        if (is_array($value)) {
            return $this->cleanArray($value);
        }

        return $this->transform($key, $value);
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
        return $value;
    }
}
