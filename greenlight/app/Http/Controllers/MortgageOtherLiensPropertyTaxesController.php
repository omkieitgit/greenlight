<?php
/**
 * Created By Rativardhan Singh Sengar  1/14/19 12:08 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/14/19 12:02 AM
 */

namespace App\Http\Controllers;


use App\Http\Validations\MortgageOtherLiensPropertyTaxesValidations;
use App\Models\DocumentMortgage;
use App\Models\DocumentMortgageHoa;
use App\Models\DocumentMortgageTax;
use App\Models\DocumentMortgageOther;
use App\Models\DocumentMortgagePropertyTaxesModel;
use App\Models\MortgageLiensNotesModel;
use App\Models\PropertyModel;
use App\Services\DocumentService;
use App\Services\MortgageHoaService;
use App\Services\MortgageLiensService;
use App\Services\MortgageOtherLiensPropertyTaxesService;
use App\Services\MortgageOtherService;
use App\Services\MortgagePropertyTaxesOwedService;
use App\Services\MortgagePropertyTaxesService;
use App\Services\UserService;
use App\Services\MortgageTaxService;

use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use DB;

class MortgageOtherLiensPropertyTaxesController extends Controller {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $validations;
    private $mortgageOtherLiensPropertyTaxesService;
    private $mortgageOtherService;
    private $mortgageLiensService;
    private $documentService;
    private $mortgageHoaService;
    private $mortgagePropertyTaxesService;
    private $mortgagePropertyTaxesOwedService;
    private $userService;
    private $mortgageTaxService;

    public function __construct(
        Request $request,
        MortgageOtherLiensPropertyTaxesService $mortgageOtherLiensPropertyTaxesService,
        MortgageLiensService $mortgageLiensService,
        DocumentService $documentService,
        MortgageOtherService $mortgageOtherService,
        MortgageHoaService $mortgageHoaService,
        MortgagePropertyTaxesService $mortgagePropertyTaxesService,
        MortgagePropertyTaxesOwedService $mortgagePropertyTaxesOwedService,
        UserService $userService,
        MortgageTaxService $mortgageTaxService
    ) {
        Log::info("MortgageOtherLiensPropertyTaxes: __construct called");
        $this->request                                = $request;
        $this->mortgageOtherLiensPropertyTaxesService = $mortgageOtherLiensPropertyTaxesService;
        $this->mortgageLiensService                   = $mortgageLiensService;
        $this->documentService                        = $documentService;
        $this->mortgageOtherService                   = $mortgageOtherService;
        $this->mortgageHoaService                     = $mortgageHoaService;
        $this->mortgagePropertyTaxesService           = $mortgagePropertyTaxesService;
        $this->mortgagePropertyTaxesOwedService       = $mortgagePropertyTaxesOwedService;
        $this->userService                            = $userService;
        $this->mortgageTaxService                     = $mortgageTaxService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAll($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: indexAll called");
        $info = [];

        $info['other_4']        = $this->mortgageOtherLiensPropertyTaxesService->findAllInformation($house_id);
        $mortgage_liens         = $this->mortgageLiensService->findAllLiensByHouseId($house_id);

        $mortgage_liens = $mortgage_liens->map(function($liens) {

            $liens->notes = MortgageLiensNotesModel::where(['house_id'=>$liens->house_id,
                                                               'lien_type'=>$liens->lien_type
                                                               ])->get()->makeHidden('user_id');
            return $liens;
        });

        $info['mortgage_liens'] =$mortgage_liens;
        $info['tax_liens']    = $this->mortgageTaxService->findAllInformation($house_id);
        $info['other_liens']    = $this->mortgageOtherService->findAllInformation($house_id);
        $hoa_liens              = $this->mortgageHoaService->findAllInformation($house_id);

        if(!empty($hoa_liens) && !$hoa_liens->isEmpty())
        {
            $hoa_liens[0]->notes = MortgageLiensNotesModel::where(['house_id'=>$hoa_liens[0]->house_id,
                                                                'lien_type'=> 4
                                                               ])->get()->makeHidden('user_id');
        }
        $info['hoa_liens']      = $hoa_liens;
        //$info['taxes']          = $this->mortgagePropertyTaxesService->findAllInformation($house_id);

        //        $temp = $info['hoa_liens'][0]->check_by;
        //        $lastMeasures = $temp->map(function($hive) {
        //            $created_at = ($hive->created_at);
        //            $hive->user->created_at = $created_at;
        //            $hive = $hive->user;
        //            return $hive;
        //        });
        //
        //        $info['hoa_liens'][0]->check_by1 = $lastMeasures;
        //        return response()->json(['data' =>  $lastMeasures], 200);
        //        var_dump($lastMeasures);
        //        die;
        //
        //        var_dump($lastMeasures);die;
        ## ToDO: fetch direct from DB no need this with
        $temp = PropertyModel::with(
            [
                "last_sale_details" => function($query) {
                    $query->select(['house_id','sale_id','sale_date','case_number','opening_bid'
                                    ,'sale_time', 'sale_status','sale_type'
                                    , 'sale_place', 'sale_time', 'trustee_file_no'
                                    , 'priceint', 'trustee_scraped', 'trustee', 'trustee_url', 'trustee_address', 'trustee_phone'
                                    , 'trustee_hours'
                                    , 'nos_by','nos_date'

                                   ]);
                },

                "last_sale_details.sale_descriptions" ,
                "last_sale_details.document_sale" ,
                "last_sale_details.last_bidder"=>function($query){
                    $query->select(['sale_id','house_id','amount_of_bid']);
                } ,
                //                "last_sale_details.nos"       => function ($query) {
                //                    $query->select(["users.id","users.email","users.first_name","users.last_name"]);
                //                },
                "last_cma_arv_recommendations" => function ($query) {
                    $query->select(['house_id',
                                    'recommended_cma_arv',
                                    'rents_zestimate']);
                },
                "last_rental_rate" => function ($query) {
                    $query->select(['house_id',
                                    'rental_rate']);
                },
                "assessment"=>function($query){
                    $query->select(['house_id', DB::raw('sum(property_taxes_owed) as total_taxes_owed')]);
                }
                ]
        )->where('home_information.house_id', $house_id)->first();

        // ToDo: Modify this operation fetch all data at once and add normal if condition
        
        $info['last_sale_details']            = @$temp->last_sale_details;
        $info['last_cma_arv_recommendations'] = @$temp->last_cma_arv_recommendations;
        $info['last_rental_rate'] = @$temp->last_rental_rate;
        $info['last_sale_details']['assessment'] = @$temp->assessment[0]??[];

        if (empty($info)) {
            return response()->json(['data'    => [],
                                     'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 4 field in top
     */
    public function update($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: update called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: update validation check");
        $rules           = MortgageOtherLiensPropertyTaxesValidations::mortgageOtherLiensPropertyTaxesUpdateValidation();
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $this->mortgageOtherLiensPropertyTaxesService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLien() {
        Log::info("MortgageOtherLiensPropertyTaxes: createLien called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: createLien validation check");
        $rules = MortgageOtherLiensPropertyTaxesValidations::liensCreateValidation($this->request->house_id);

        /*
        $message   =
            [
                'lien_type.unique' => __('The combination [":lien_type", ":house_id"] already exists', [

                    'lien_type' => $this->request->lien_type,
                    'house_id'  => $this->request->house_id
                ]),
            ];
        $validator = Validator::make($this->request->all(), $rules, $message);
        */
        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # create information
        $info = $this->mortgageLiensService->create($this->request->all());
        if(isset($info['status']) && $info['status']=='failed'){
            return response()->json($info,400);
        }
        return response()->json(['data'    => ['mortgage_id' => $info->mortgage_id],
                                 'message' => __("messages.record_saved")], 200);

    }

    /**
     * @param $mortgage_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLien($mortgage_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: createLien called");

        # first check record exists or not
        $info = $this->mortgageLiensService->findOneById($mortgage_id);

        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: createLien validation check");
        $rules = MortgageOtherLiensPropertyTaxesValidations::liensUpdateValidation($this->request->house_id);

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # create information
        $info = $this->mortgageLiensService->update($mortgage_id, $this->request->all());
        if(isset($info['status']) && $info['status']=='failed'){
            return response()->json($info,400);
        }
        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function liensDocumentUpload() {
        #var_dump($this->request->all());die;
        Log::info("MortgageOtherLiensPropertyTaxes: LiensDocumentUpload called");

        try {
            ## check input validation
            Log::info("LiensDocumentUpload: update validation check");
            $rules = MortgageOtherLiensPropertyTaxesValidations::liensDocumentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_mortgage');
            if ($doc_info != false) {
                $doc = $this->documentService->mortgage_liens($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'row'     => ['url' => $doc_info['document_url'],
                                                       'id'  => $doc->document_mortgage_id]], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function liensDocumentDelete($document_id) {
        Log::info("propertyDocumentUpload: documentDelete called");

        try {
            $info = DocumentMortgage::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 5field in other lien
     */
    public function updateOtherLien($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: updateOtherLien called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: updateOtherLien validation check");
        $rules           = MortgageOtherLiensPropertyTaxesValidations::mortgageOtherUpdateValidation();
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $this->mortgageOtherService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function otherDocumentUpload() {
        #var_dump($this->request->all());die;
        Log::info("MortgageOtherLiensPropertyTaxes: otherDocumentUpload called");

        try {
            ## check input validation
            Log::info("otherDocumentUpload: update validation check");
            $rules = MortgageOtherLiensPropertyTaxesValidations::otherDocumentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_mortgage_other');
            if ($doc_info != false) {
                $doc = $this->documentService->mortgage_other_liens($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'row'     => ['url' => $doc_info['document_url'],
                                                       'id'  => $doc->document_mortgage_other_id]], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function otherDocumentDelete($document_id) {
        Log::info("propertyDocumentUpload: otherDocumentDelete called");

        try {
            $info = DocumentMortgageOther::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 5field in other lien
     */
    public function updateHoaLien($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: updateHoaLien called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: updateHoaLien validation check");
        $rules           = MortgageOtherLiensPropertyTaxesValidations::mortgageHoaUpdateValidation();
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $all['winning_bid'] = CommonHelper::dbNumberFormat($all['winning_bid']);
        $this->mortgageHoaService->updateOrCreate($house_id, $all);

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function hoaDocumentUpload() {
        #var_dump($this->request->all());die;
        Log::info("MortgageOtherLiensPropertyTaxes: hoaDocumentUpload called");

        try {
            ## check input validation
            Log::info("hoaDocumentUpload: update validation check");
            $rules = MortgageOtherLiensPropertyTaxesValidations::hoaDocumentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_mortgage_hoa');
            if ($doc_info != false) {
                $doc = $this->documentService->mortgage_hoa_liens($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'row'     => ['url' => $doc_info['document_url'],
                                                       'id'  => $doc->document_mortgage_hoa_id]], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function hoaDocumentDelete($document_id) {
        Log::info("propertyDocumentUpload: otherDocumentDelete called");

        try {
            $info = DocumentMortgageHoa::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 5field in other lien
     */
    public function updatePropertyTaxes($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: updatePropertyTaxes called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: updatePropertyTaxes validation check");
        $rules           = MortgageOtherLiensPropertyTaxesValidations::mortgagePropertyTaxesUpdateValidation();
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $this->mortgagePropertyTaxesService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function propertyTaxesDocumentUpload() {
        #var_dump($this->request->all());die;
        Log::info("MortgageOtherLiensPropertyTaxes: propertyTaxesDocumentUpload called");

        try {
            ## check input validation
            Log::info("hoaDocumentUpload: update validation check");
            $rules = MortgageOtherLiensPropertyTaxesValidations::propertyTaxesDocumentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_mortgage_property_taxes');
            if ($doc_info != false) {
                $doc = $this->documentService->mortgage_property_taxes($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'row'     => ['url' => $doc_info['document_url'],
                                                       'id'  => $doc->document_mortgage_property_taxes_id]], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function propertyTaxesDocumentDelete($document_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: propertyTaxesDocumentDelete called");

        try {
            $info = DocumentMortgagePropertyTaxesModel::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTaxesOwed() {
        Log::info("MortgageOtherLiensPropertyTaxes: createTaxesOwed called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: update validation check");
        $rules = MortgageOtherLiensPropertyTaxesValidations::createTaxesOwedValidation();


        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->mortgagePropertyTaxesOwedService->create($this->request->all());

        return response()->json(['row'     => ['id' => $info->tax_id],
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $tax_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTaxesOwed($tax_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: priceHistoryUpdate called");

        # first check record exists or not
        $info = $this->mortgagePropertyTaxesOwedService->findOneById($tax_id);

        if (empty($info)) {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: update validation check");
        $rules = MortgageOtherLiensPropertyTaxesValidations::updateTaxesOwedValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->mortgagePropertyTaxesOwedService->update($tax_id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    public function createNotes($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: createNotes called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: createNotes validation check");
        $rules = MortgageOtherLiensPropertyTaxesValidations::notesCreateValidation();

        $all = $this->request->all();
        $all['house_id'] = $house_id;
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $all['user_id'] = $this->userService->user_id();
        $info = MortgageLiensNotesModel::create($all);
        return response()->json(['status'  => 'success',
                                 'data'    => $info,
                                 'message' => __("messages.notes_saved")], 200);

    }

    public function updateNotes($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: createNotes called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: createNotes validation check");
        $rules = MortgageOtherLiensPropertyTaxesValidations::notesCreateValidation();

        $all = $this->request->all();
        $all['id'] = $all['id'];
        $all['house_id'] = $house_id;
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $all['user_id'] = $this->userService->user_id();

        $info = MortgageLiensNotesModel::find($id);
        $info->notes = $all['notes'] ;
        $info->save();

        return response()->json(['status'  => 'success',
                                 'data'    => $info,
                                 'message' => __("messages.notes_saved")], 200);

    }

    public function destroyNinfoiinfonfootes($id) {
        Log::info("MortgageOtherLiensPropertyTaxes: destroy called");
        try
        {
            $info = MortgageLiensNotesModel::find($id);
            if ($info != null)
            {
                // check user id of notes
                if($info->user_id != $this->userService->user_id())
                {
                    return response()->json(['status'  => 'failed','message' => __("messages.not_delete")], 200);
                }
                $info->delete();
                return response()->json(['status'  => 'success','message' => __("messages.record_delete")
                ], 200);
            }
            else
            {
                return response()->json(['status'  => 'failed','message' => __("error_messages.record_not_exists")], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status'  => 'failed','message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function getAllNotes($house_id, $lien_type = '') {
        Log::info("MortgageOtherLiensPropertyTaxes: getAllNotes called");

        # create informationinfoiinfonfo
        $info = MortgageLiensNotesModel::with(['user'=>function ($query) {
            $query->select(["users.id","users.email","users.first_name","users.last_name"]);
        }])->where('house_id','=',$house_id);

        if(!empty($lien_type))
        {
            $info->where('lien_type','=',$lien_type);
        }
        $info = $info ->get();
        return response()->json(['status'  => 'success',
                                 'data'    => $info,
                                 ], 200);

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 5field in other lien
     */
    public function updateTaxLien($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: updateTaxLien called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: updateTaxLien validation check");
        $rules           = MortgageOtherLiensPropertyTaxesValidations::mortgageTaxUpdateValidation();
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();

        //$all['created_at'] = time();
        //$all['updated_at'] = time();
        # create information
        $this->mortgageTaxService->updateOrCreate($house_id, $this->request->all());

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function taxDocumentUpload() {
        #var_dump($this->request->all());die;
        Log::info("MortgageOtherLiensPropertyTaxes: taxDocumentUpload called");

        try {
            ## check input validation
            Log::info("taxDocumentUpload: update validation check");
            $rules = MortgageOtherLiensPropertyTaxesValidations::taxDocumentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_mortgage_tax');
            if ($doc_info != false) {
                $doc = $this->documentService->mortgage_tax_liens($doc_info);
                return response()->json(['message' => __("messages.document_upload"),
                                         'row'     => ['url' => $doc_info['document_url'],
                                                       'id'  => $doc->document_mortgage_tax_id]], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function taxDocumentDelete($document_id) {
        Log::info("propertyDocumentUpload: taxDocumentDelete called");

        try {
            $info = DocumentMortgageTax::find($document_id);
            if ($info == true) {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

      /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 4 field in top
     */
    public function updateSingleRecord($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: update called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: update validation check");
        //$rules           = MortgageOtherLiensPropertyTaxesValidations::mortgageOtherLiensPropertyTaxesUpdateValidation();
        $all             = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $saveData['lien_type'] = $all['lien_type'];
        $mortgage_id=$saveData['mortgage_id'] = $all['mortgage_id'];

        // $validator = Validator::make($saveData, $rules);
        // $validator->validate();

        $info = $this->mortgageLiensService->findOneById($mortgage_id);

        if(empty($info)){
            $info = $this->mortgageLiensService->findByLienTypeHouseId(['lien_type'=>$all['lien_type'],'house_id'=>$house_id]);
            if($info)
            {
                $mortgage_id=$info->mortgage_id;
            }    
        }
        if (empty($info)) {
            $info = $this->mortgageLiensService->create($saveData);
            $mortgage_id=$info->mortgage_id;
        }else{
            $info = $this->mortgageLiensService->update($mortgage_id, $saveData);
        }

        return response()->json(['data'    => ['mortgage_id' => $mortgage_id],
                                 'message' => __("messages.record_saved")], 200);
      
    }

    public function updateSingleOtherInfo($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: update called");

        ## check input validation
        Log::info("MortgageOtherLiensPropertyTaxes: update validation check");
        $rules           = MortgageOtherLiensPropertyTaxesValidations::mortgageOtherLiensPropertyTaxesUpdateValidation();
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $validator       = Validator::make($all, $rules);
        $validator->validate();
        $all             = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        // $saveData['lien_type'] = $all['lien_type'];
        // $mortgage_id=$saveData['mortgage_id'] = $all['mortgage_id'];
        # create information
        $this->mortgageOtherLiensPropertyTaxesService->updateOrCreate($house_id,$saveData);

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
    }


       /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 4 field in top
     */
    public function updateHoaRecord($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: update called");
        ## check input validation
        $all             = $this->request->all();
        $saveData=[];
      //  $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];

        //$saveData['winning_bid'] = CommonHelper::dbNumberFormat(@$saveData['winning_bid']);

        $this->mortgageHoaService->updateOrCreate($house_id, $saveData);
        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);
      
    }

       /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     * Update 4 field in top
     */
    public function updateOtherRecord($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: update called");
        ## check input validation
        $all             = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        $this->mortgageOtherService->updateOrCreate($house_id, $saveData);

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);

      
      
    }

    public function updateTaxRecord($house_id) {
        Log::info("MortgageOtherLiensPropertyTaxes: update called");
        ## check input validation
        $all             = $this->request->all();
        $saveData=[];
        $saveData['house_id'] = $house_id;
        $saveData[$all['name']] = $all['value'];
        //$saveData['winning_bid'] = CommonHelper::dbNumberFormat(@$saveData['winning_bid']);
        $this->mortgageTaxService->updateOrCreate($house_id, $saveData);

        return response()->json(['data'    => [],
                                 'message' => __("messages.record_saved")], 200);

      
      
    }


}
