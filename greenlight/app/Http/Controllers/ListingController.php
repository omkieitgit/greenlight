<?php

namespace App\Http\Controllers;

use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Services\DocumentService;
use App\Services\ListingService;
use App\Http\Validations\ListingValidations;
use App\Models\ListingDocumentModel;


class ListingController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $documentService;
    private $listingService;
    




    /**
     * @param DocumentService $documentService
     */

    public function __construct(Request $request

        , DocumentService $documentService
        , ListingService $listingService

    )
    {
        Log::info("ListingController: __construct called");
        $this->request         = $request;
        $this->documentService                = $documentService;
        $this->listingService                = $listingService;
    
    }

    /** Client Renovation Section Start  */
    public function getDocument($house_id)
    {
        Log::info("ListingController: clienDocumentGet called");
        $listing_document                    = $this->listingService->getListingDocument($house_id);
        $info['listing_document']            = (array)json_decode($listing_document);
        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    public function postDocument()
    {
        #var_dump($this->request->all());die;
        Log::info("clientDocumentUpload: clientDocumentUpload called");

        try
        {

            ## check input validation
            Log::info("documentUpload: update validation check");
            $rules = ListingValidations::documentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('listing_document');
            if ($doc_info != false)
            {
                $doc_info['house_id'] = $this->request->house_id;
                $doc = $this->documentService->listingDocument($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'data'   => ['url' => $doc_info['document_url'], 'id' => $doc->id],
                                        ], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.something_wrong")], 400);
            }

        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function deleteDocument($document_id)
    {
        Log::info("clientDocumentUpload: documentDelete called");

        try
        {
            $info = ListingDocumentModel::find($document_id);
            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.document_delete")
                                         , 'data'=>[]],200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists"), 'data'=>[]], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }
   
    

   
}
