<?php
/**
 * Created By Mranalinee Chouhan 
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Services;

use App\Models\Form1099MiscModel;
use App\Models\RecipientsInfoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;
use DB;


class Form1099MiscService
{
    private $findAllByHouseId;

    /**
     * ClientService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("Form1099MiscService: __construct called");
    }

    public function storeForm1099Misc($id, $updateData){
        Log::info("Form1099MiscService: updateProperty called");
        return Form1099MiscModel::updateOrCreate(["id"=>$id],$updateData);

    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */

    public function findForm1099Misc($payers_id,$recipient_id,$year,$offset,$limit)
    {
        Log::info("Form1099MiscService: findAllByHouseId called");

        $info= Form1099MiscModel::
                select([DB::raw('form_1099_misc.recipients_id'),
                 'recipient_info.recipients_name',
                 'recipient_info.recipients_address',
                 'recipient_info.recipients_city_state',
                 'recipient_info.recipients_tin',
                 'payers_info.id as payers_id',
                 'payers_info.payers_name',
                 'payers_info.payers_tin',
                 'payers_info.payers_address',
                 DB::raw('form_1099_misc.form_year'),
                 DB::raw('SUM(client_renovation_detail.amount) as amount')])
                ->leftJoin('client_renovation_detail','client_renovation_detail.id','form_1099_misc.client_renovation_detail_id')
                ->leftJoin('recipient_info','recipient_info.id','form_1099_misc.recipients_id')
                ->leftJoin('mcd_other_info','mcd_other_info.house_id','form_1099_misc.house_id')
                ->leftJoin('payers_info','mcd_other_info.payers_id','payers_info.id')
                ->groupBy('recipients_id')
                ->groupBy('form_year')
                ->groupBy('mcd_other_info.payers_id');
        if(!empty($payers_id)){
            $info->where('mcd_other_info.payers_id',$payers_id);
        }
        if(!empty($recipient_id)){
            $info->where('form_1099_misc.recipients_id',$recipient_id);
        }
        if(!empty($year)){
            $info->where('form_1099_misc.form_year',$year);
        }
        $info  = $info->skip(intval($offset))->take(intval($limit))->get();

        return $info;
    }


    function findForm1099MscByHouseId($house_id){
        return Form1099MiscModel::where('mcd_other_info.house_id',$house_id)
        ->select([DB::raw('form_1099_misc.recipients_id'),
                 'recipient_info.recipients_name',
                 'recipient_info.recipients_address',
                 'recipient_info.recipients_city_state',
                 'recipient_info.recipients_tin',
                 'payers_info.id as payers_id',
                 'payers_info.payers_name',
                 'payers_info.payers_tin',
                 'payers_info.payers_address',
                 DB::raw('form_1099_misc.form_year'),
                 DB::raw('SUM(client_renovation_detail.amount) as amount')])
        ->leftJoin('client_renovation_detail','client_renovation_detail.id','form_1099_misc.client_renovation_detail_id')
        ->leftJoin('recipient_info','recipient_info.id','form_1099_misc.recipients_id')
        ->leftJoin('mcd_other_info','mcd_other_info.house_id','form_1099_misc.house_id')
        ->leftJoin('payers_info','mcd_other_info.payers_id','payers_info.id')
        ->groupBy('recipients_id')
        ->groupBy('form_year')
        ->groupBy('mcd_other_info.payers_id')
        ->get();
    }

    function createUpdateRecipients($id,$updateData){
        return RecipientsInfoModel::updateOrCreate(["id"=>$id],$updateData);
    }

    function getRecipints($recipientId=''){
        if(!empty($recipientId)){
            return RecipientsInfoModel::where('id',$recipientId)->first();
        }else{
            return RecipientsInfoModel::get();
        }
    }


    



}

