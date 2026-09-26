<?php
/**
 * Created By Mranalinee Chouhan
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;
use DB;
use App\Models\ListingDocumentModel;


class ListingService
{
    private $findAllByHouseId;

    /**
     * ListingService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("ListingService: __construct called");
        $this->request = $request;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getListingDocument($house_id, $is_cache = false)
    {
        Log::info("ListingService: getListingDocument called");
        if ($is_cache == true)
        {
            return $this->getListingDocument;
        }
        $this->getListingDocument = ListingDocumentModel::with([
            'user' => function ($query) {
                $query->select(['id',
                    'first_name',
                    'last_name',
                    'username'
                ]);
            }
        ])->where('house_id', $house_id)->get();

        return $this->getListingDocument;
    }

    
}
