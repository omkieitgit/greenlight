<?php

namespace App\Http\Controllers;
use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Services\DocumentService;
use App\Services\NonHudExpenditureService;
use App\Models\NonHudExpendituresModel;
use App\Http\Validations\ClientValidations;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Storage;



class NonHudExpenditureController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $documentService;
    private $nonHudExpenditureService;


    /**
     * @param DocumentService $documentService
     */

    public function __construct(Request $request

        , DocumentService $documentService
        , NonHudExpenditureService $nonHudExpenditureService


    )
    {
        Log::info("NonHudExpenditureController: __construct called");
        $this->request                      = $request;
        $this->documentService              = $documentService;
        $this->nonHudExpenditureService     = $nonHudExpenditureService;

    }



    public function index($house_id)
    {
        Log::info("nonHudExpendituresCreate: indexAll called");



        $info    =  (array)json_decode($this->nonHudExpenditureService->getNonHudExpenditures($house_id));

        if (empty($info))
        {
            return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }



    public function nonHudExpendituresCreate()
    {
        #var_dump($this->request->all());die;
        Log::info("nonHudExpendituresCreate: clientDocumentUpload called");

        try
        {

            ## check input validation
            Log::info("nonHudExpendituresCreate: update validation check");
            $rules = ClientValidations::nonHudExpenditureValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('non_hud_expenditure');
            if ($doc_info != false)
            {
                $doc_info['house_id'] = $this->request->house_id;
                $doc = $this->nonHudExpenditureService->createNonHudExpenditures($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'data'   => ['url' => $doc_info['document_url'], 'id' => $doc->id]], 200);
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


    public function nonHudExpendituresUpdate($id)
    {
        #var_dump($this->request->all());die;
        Log::info("nonHudExpendituresCreate: nonHudExpendituresUpdate called");

        try
        {

            ## check input validation
            Log::info("nonHudExpendituresUpdate: update validation check");
            $rules = ClientValidations::nonHudExpenditureValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            #check if document is uploaded or not
            if(!empty($this->request->file('document'))){
                $renovationinfo = NonHudExpendituresModel::find($id);
                $doc_info = $this->documentService->documentUpload('non_hud_expenditure');

                Storage::disk('digitalocean')->delete('non_hud_expenditure/'.$renovationinfo['store_name']);

            }

            $doc_info   = $this->request->all();

            if ($doc_info != false)
            {
                $doc_info['house_id'] = $this->request->house_id;
                #Update data into table
                $doc = $this->nonHudExpenditureService->updateNonHudExpenditures($id, $doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'data'   => ['url' => $doc_info, 'id' => $doc->id]], 200);
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

    public function nonHudExpendituresDelete($document_id)
    {
        Log::info("nonHudExpendituresCreate: nonHudExpendituresUpdate called");

        try
        {
            $info = NonHudExpendituresModel::find($document_id);
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



    public function updateNonHudExpenditures($house_id)
    {
        #var_dump($this->request->all());die;
        Log::info("nonHudExpendituresCreate: nonHudExpendituresUpdate called");

        try
        {

            ## check input validation
            Log::info("nonHudExpendituresUpdate: update validation check");
            $rules = ClientValidations::nonHudExpenditureUpdateValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $nonHudExpenditures   = $this->request->all();

            if ($nonHudExpenditures != false)
            {

                foreach ($nonHudExpenditures['burn_rate_data'] as $amountInfo){
                    $this->nonHudExpenditureService->updateNonHudExpenditures($amountInfo['id'],$amountInfo);
                }

                #Update data into table
                return response()->json(['message' => __("messages.record_modified")
                    , 'data'   => [$amountInfo]], 200);
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

}
