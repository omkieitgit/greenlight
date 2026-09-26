<?php

namespace App\Http\Controllers;

use App\Http\Validations\CommonValidations;
use App\Http\Validations\OwnerBorrowerValidations;
use App\Models\BorrowerModel;
use App\Models\DocumentBorrowerModel;
use App\Models\DocumentOwnerModel;
use App\Models\OwnerModel;
use App\Services\BorrowerService;
use App\Services\DocumentService;
use App\Services\OwnerService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use App\Models\OwnerBorrowerInfoModel;

class OwnerBorrowerController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyService;
    private $ownerService;
    private $borrowerService;
    private $documentService;
    private $disk;

    /**
     * OwnerBorrowerController constructor.
     * @param Request $request
     * @param PropertyService $propertyService
     * @param OwnerService $ownerService
     * @param BorrowerService $borrowerService
     * @param DocumentService $documentService
     */
    public function __construct(Request $request
        , PropertyService $propertyService
        , OwnerService $ownerService
        , BorrowerService $borrowerService
        , DocumentService $documentService
    )
    {
        Log::info("OwnerBorrowerController: __construct called");
        $this->request         = $request;
        $this->propertyService = $propertyService;
        $this->ownerService    = $ownerService;
        $this->borrowerService = $borrowerService;
        $this->documentService = $documentService;

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function all($house_id)
    {
        Log::info("OwnerBorrowerController: all called");

        if (empty($house_id))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        $info                      = [];
        $info['owner_borrower_info']=OwnerBorrowerInfoModel::find($house_id);
        $info['owner_info']        = $this->ownerService->findAllByHouseId($house_id);
        $info['borrower_info']     = $this->borrowerService->findAllByHouseId($house_id);

        $info['document_owner']    = $this->documentService->getOwnerDocument($house_id);
        $info['document_borrower'] = $this->documentService->getBorrowerDocument($house_id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ownerIndex($house_id)
    {
        Log::info("OwnerBorrowerController: ownerIndex called");

        $info = $this->ownerService->findAllByHouseId($house_id);
        $info                      = [];
        $info['owner_info']        = $this->ownerService->findAllByHouseId($house_id);
        $info['document_owner']    = $this->documentService->getOwnerDocument($house_id);

        if (empty($info['owner_info']))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ownerCreate()
    {
        Log::info("OwnerBorrowerController: ownerCreate called");

        ## check input validation
        Log::info("OwnerBorrowerController: update validation check");
        $rules = OwnerBorrowerValidations::ownerCreateValidation();


        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $info = $this->ownerService->createOwnerInfo($this->request->all());

        return response()->json(['row' => ['id' => $info->id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ownerUpdate($id)
    {
        Log::info("OwnerBorrowerController: ownerUpdate called");

        # first check record exists or not
        $info = $this->ownerService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }
        ## check input validation
        Log::info("OwnerBorrowerController: update validation check");
        $rules = OwnerBorrowerValidations::ownerUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $this->ownerService->updateOwnerInfo($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /*
    *
    * **/
    public function ownerUpdateAll($house_id){

        Log::info("OwnerBorrowerController: ownerUpdateAll called");
        ## check input validation
        Log::info("OwnerBorrowerController: update validation check");
        $owner_info=$this->request->input('owner_info_data');
        $borrow_info=$this->request->input('borrow_info_data');

        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.house_id_exists")], 400);
        }

        foreach($owner_info as $owner){

            $owner['house_id']=$house_id;
            $owner['deed_recorded_date']=explode('T',$owner['deed_recorded_date'])[0];

            if(!empty($owner['id'])) {
                # update information
                $rules = OwnerBorrowerValidations::ownerUpdateValidation();
                $validator = Validator::make($this->request->all(), $rules);
                $validator->validate();

                $this->ownerService->updateOwnerInfo($owner['id'], $owner);
            }
            else{
                $rules = OwnerBorrowerValidations::ownerCreateValidation();
                $validator = Validator::make($this->request->all(), $rules);
                $validator->validate();

                # update information
                $info = $this->ownerService->createOwnerInfo($owner);
            }
        }
        foreach ($borrow_info as $borrow){
            // We are not using this because , we want to return all error into one array box
            $borrow['house_id']=$house_id;
            if(!empty($borrow['id'])) {
                # update information
                $rules = OwnerBorrowerValidations::borrowerUpdateValidation();
                $validator = Validator::make($this->request->all(), $rules);
                $validator->validate();

                $this->borrowerService->updateBorrowerInfo($borrow['id'], $borrow);
            }
            else{
                $rules = OwnerBorrowerValidations::borrowerCreateValidation();
                $validator = Validator::make($this->request->all(), $rules);
                $validator->validate();

                # update information
                $info = $this->borrowerService->createBorrowerInfo($borrow);
            }
        }

        return response()->json(['message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrowerIndex($house_id)
    {
        Log::info("BorrowerBorrowerController: borrowerIndex called");

        $info                      = [];
        $info['borrower_info']     = $this->borrowerService->findAllByHouseId($house_id);
        $info['document_borrower'] = $this->documentService->getBorrowerDocument($house_id);

        if (empty($info['borrower_info']))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrowerCreate()
    {
        Log::info("BorrowerBorrowerController: borrowerCreate called");

        ## check input validation
        Log::info("BorrowerBorrowerController: update validation check");
        $rules = OwnerBorrowerValidations::borrowerCreateValidation();


        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();


        # update information
        $info = $this->borrowerService->createBorrowerInfo($this->request->all());

        return response()->json(['row' => ['id' => $info->id], 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrowerUpdate($id)
    {
        Log::info("OwnerBorrowerController: borrowerUpdate called");

        # first check record exists or not
        $info = $this->borrowerService->findOneById($id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        ## check input validation
        Log::info("OwnerBorrowerController: update validation check");
        $rules = OwnerBorrowerValidations::borrowerUpdateValidation();

        $validator = Validator::make($this->request->all(), $rules);
        $validator->validate();

        # update information
        $result=$this->borrowerService->updateBorrowerInfo($id, $this->request->all());

        return response()->json(['message' => __("messages.record_saved"),'data'=>$result], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ownerDocumentUpload()
    {

        #var_dump($this->request->all());die;
        Log::info("ownerDocumentUpload: propertyDocumentUpload called");

        try
        {
            ## check input validation
            Log::info("documentUpload: update validation check");
            $rules = commonValidations::documentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_owner');
            if ($doc_info != false)
            {
                $doc = $this->documentService->owner($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'row'   => ['url' => $doc_info['document_url'], 'id' => $doc->id]], 200);
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrowerDocumentUpload()
    {
        #var_dump($this->request->all());die;
        Log::info("borrowerDocumentUpload: propertyDocumentUpload called");

        try
        {
            ## check input validation
            Log::info("documentUpload: update validation check");
            $rules = commonValidations::documentValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_borrower');

            if ($doc_info != false)
            {
                $doc = $this->documentService->borrower($doc_info);
                return response()->json(['message' => __("messages.document_upload")
                                         , 'row'   => ['url' => $doc_info['document_url'], 'id' => $doc->id]], 200);
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

    public function ownerDelete($owner_id)
    {
        Log::info("OwnerBorrowerController: ownerDelete called");

        try
        {
            $info = OwnerModel::find($owner_id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
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

    public function borrowerDelete($borrower_id)
    {
        Log::info("OwnerBorrowerController: borrowerDelete called");

        try
        {
            $info = BorrowerModel::find($borrower_id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
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

    public function ownerDocumentDelete($document_id)
    {
        Log::info("borrowerDocumentUpload: ownerDocumentDelete called");

        try
        {
            $info = DocumentOwnerModel::find($document_id);

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

    public function borrowerDocumentDelete($document_id)
    {
        Log::info("borrowerDocumentUpload: borrowerDocumentDelete called");

        try
        {
            $info = DocumentBorrowerModel::find($document_id);

            if ($info == true)
            {
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
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

/**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ownerBorrowInfoCreate()
    {
        Log::info("OwnerBorrowerController: ownerCreate called");

        ## check input validation
        Log::info("OwnerBorrowerController: update validation check");
        $all=$this->request->all();
        $house_id=$all['house_id'];
        unset($all['house_id']);
        $info= OwnerBorrowerInfoModel::updateOrCreate(["house_id" => $house_id], $all);

        return response()->json(['row' => ['id' => $info->house_id], 'message' => __("messages.record_saved")], 200);
    }

    public function updateOwnerSingleRecord($house_id)
    {
        Log::info("OwnerBorrowerController: ownerUpdate called");
        # first check record exists or not
        $all = $this->request->all();
        $value=[];
        $value['house_id']  = $house_id;
        $value[$all['name']] = $all['value'];
        $value['id']  = $all['id'];

        if(empty(@$all['id']))
        {
            $result = $this->ownerService->createOwnerInfo($value);
            $info=$result->id;
        }
        else{
            $this->ownerService->updateOwnerInfo($all['id'], $value);
            $info=$value['id'];
        }

        # update information
        return response()->json(['message' => __("messages.record_saved"),'data'=>$info], 200);
    }

    public function updateBorrowSingleRecord($house_id)
    {
        Log::info("OwnerBorrowerController: borrowerUpdate called");

        $all = $this->request->all();
        $value=[];
        $value['house_id']  = $house_id;
        $value[$all['name']] = $all['value'];
        $value['id']  = $all['id'];

        if(empty(@$all['id']))
        {
            $result = $this->borrowerService->createBorrowerInfo($value);
            $info=$result->id;
        }
        else{
            $result = $this->borrowerService->updateBorrowerInfo($all['id'], $value);
        }

        # update information
        return response()->json(['message' => __("messages.record_saved"),'data'=>$result], 200);
    }

    public function storeUpdateSocailSingleRecord($house_id)
    {
        Log::info("OwnerBorrowerController: borrowerUpdate called");

        $all = $this->request->all();
        $value=[];
        $value['house_id']  = $house_id;
        $value['owner_id']  = $all['owner_id'];
        $value[$all['name']] = $all['value'];
        $value['id']  = $all['id'];

        if(empty(@$all['id']))
        {
            $result = $this->ownerService->updateOwnerSocialMediaInfo($all['id'], $value);
            $info=$result->id;
        }
        else{
            $result = $this->ownerService->updateOwnerSocialMediaInfo($all['id'], $value);
        }
        # update information
        return response()->json(['status'=>"success",'message' => __("messages.record_saved"),'data'=>$result], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ownerSocialMedia($house_id,$owner_id)
    {
        Log::info("OwnerBorrowerController: ownerSocialMedia called");

        $info = $this->ownerService->findOwnerSocialMedia($house_id,$owner_id);
        
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    

}
