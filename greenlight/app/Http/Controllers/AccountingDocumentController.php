<?php

namespace App\Http\Controllers;

use Validator;
Use Log;
use Illuminate\Http\Request;

use App\Http\Validations\ClientValidations;
use App\Services\DocumentService;
use App\Models\DocumentAccountingModel;
use App\Services\ClientService;

class AccountingDocumentController extends Controller
{
  private $request;
  private $documentService;
  private $clientService;

  public function __construct(Request $request,DocumentService $documentService,
                            ClientService $clientService  ){
          $this->request         = $request;
          $this->documentService = $documentService;
          $this->clientService   = $clientService;
  }
  public function index($house_id)
  {
      Log::info("accountingController: indexAll called");

      $info['accounting_document']  = DocumentAccountingModel::with('user')->where('house_id',$house_id)->get();

      if (empty($info))
      {
          return response()->json(['data' => [], 'message' => __("error_messages.record_not_exists")], 200);
      }

      return response()->json(['data' => $info], 200);
  }

  public function documentUpload()
  {

      Log::info("accountingDocumentUpload: accountingDocumentUpload called");
      try
      {
          ## check input validation
          Log::info("documentUpload: update validation check");
          $rules = ClientValidations::documentValidation();

          $validator = Validator::make($this->request->all(), $rules);
          $validator->validate();

          $doc_info = $this->documentService->documentUpload('document_accounting');
          if ($doc_info != false)
          {
                $doc_info['house_id'] = $this->request->house_id;
                $newCategory='';
                if(!is_numeric($doc_info['document_type'])){
                    $categoryName=$doc_info['document_type'];
                    $info=$this->clientService->saveUpdateRenovationCategory(['id'=>'','category_name'=>$categoryName]);
                    $doc_info['document_type']=$info->id;
                    $newCategory=['id'=>$info->id,'category_name'=>$categoryName];
                }

              $doc = $this->documentService->accountingDocument($doc_info);
              return response()->json(['message' => __("messages.document_upload")
                                       , 'data'   => ['url' => $doc_info['document_url'], 'id' => $doc->id],
                                       'category' => $newCategory]
                                       , 200);
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


  public function documentDelete($document_id)
  {
      Log::info("accountingDocumentUpload: documentDelete called");

      try
      {
          $info = DocumentAccountingModel::find($document_id);
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
