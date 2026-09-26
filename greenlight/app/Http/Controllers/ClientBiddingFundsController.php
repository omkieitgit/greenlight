<?php

namespace App\Http\Controllers;


use App\Http\Validations\ClientBiddingFundsValidations;
use App\Models\DocumentClientBiddingFundsModel;
use App\Services\ClientBiddingFundsService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Services\DocumentService;

class ClientBiddingFundsController extends Controller {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $documentService;
    private $userService;
    private $clientBiddingFundsService;

    /**
     * ClientBiddingFundsController constructor.
     * @param Request $request
     * @param DocumentService $documentService
     * @param UserService $userService
     * @param ClientBiddingFundsService $clientBiddingFundsService
     */
    public function __construct(
        Request $request,
        DocumentService $documentService,
        UserService $userService,
        ClientBiddingFundsService $clientBiddingFundsService
    ) {
        Log::info("ClientBiddingFundsController: __construct called");
        $this->request                        = $request;
        $this->documentService                = $documentService;
        $this->userService                    = $userService;
        $this->clientBiddingFundsService                    = $clientBiddingFundsService;
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id) {
        Log::info("ClientBiddingFundsController: index called");

        $temp = $this->clientBiddingFundsService->findAll($house_id);
        if (empty($temp)) {
            return response()->json(['data' => [],'status' => 'failed','message' => __("error_messages.record_not_exists")], 200);
        }
        return response()->json(['status' => 'success','data' => $temp], 200);

    }


    public function updateOrCreate($house_id) {
        Log::info("ClientBiddingFundsController: update called");

        ## check input validation
        Log::info("ClientBiddingFundsController: update validation check");
        $rules = ClientBiddingFundsValidations::updateOrCreate();
        $all = $this->request->all();
        $all['house_id'] = $house_id;
        $validator = Validator::make($all, $rules);
        $validator->validate();


        # update information
        $info = $this->clientBiddingFundsService->updateOrCreate( $all);

        return response()->json(['message' => __("messages.record_saved")
            ,'status' => 'success'
            , "data" => $info
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function documentUpload() {

        Log::info("ClientBiddingFundsController: documentUpload called");

        try {
            ## check input validation
            Log::info("ClientBiddingFundsController: update validation check");
            $all = $this->request->all();
            if(empty($all['id']))
            {
                
                $rules = ClientBiddingFundsValidations::document();
                $validator = Validator::make($all, $rules);
                $validator->validate();
            }
            $doc_info_authorization = $this->documentService->documentUpload('document_client_bidding_funds_authorization','document_authorization');
            $doc_info_receipt = $this->documentService->documentUpload('document_client_bidding_funds_receipt','document_receipt');
            
            if ($doc_info_receipt!== false) {
                $all['store_name_receipt'] = $doc_info_receipt['store_name'];
                $all['org_name_receipt'] = $doc_info_receipt['org_name'];

            }
            if($doc_info_authorization != false){
                $all['store_name'] = $doc_info_authorization['store_name'];
                $all['org_name'] = $doc_info_authorization['org_name'];
            }
            
            $all['created_at'] = time();
            $doc = $this->clientBiddingFundsService->updateOrCreate($all);
            return response()->json(['message' => __("messages.document_upload"),
                                        'data'    => $doc,
                                        'status' => 'success'
                                        ], 200);
            // }
            // else {
            //     return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
            // }

        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
        }

    }

    /**
     * @param $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function documentDelete($document_id) {
        Log::info("ClientBiddingFundsController: documentDelete called");

        try {
            $info = DocumentClientBiddingFundsModel::find($document_id);
            if ($info == true) {
                //$this->documentService->documentDelete( '','document_client_bidding_funds_authorization');
                //$this->documentService->documentDelete('document_client_bidding_funds_authorization');

                $info->delete();
                return response()->json(['message' => __("messages.document_delete"),
                                        'status' => 'success',
                                         'data'    => []], 200);
            }
            else {
                return response()->json(['message' => __("error_messages.record_not_exists"),
                                        'status' => 'failed',
                                         'data'    => []], 200);
            }
        }
        catch (Exception $ex) {
            return response()->json(['message' => __("error_messages.something_wrong"),'status' => 'failed'], 400);
        }
    }



}
