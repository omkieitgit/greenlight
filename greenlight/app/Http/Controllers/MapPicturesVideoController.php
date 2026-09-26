<?php

namespace App\Http\Controllers;


use App\Http\Validations\MapVideoValidations;
use App\Models\DocumentPictureModel;
use App\Services\MapVideoService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Validations\PropertyValidations;
use App\Http\Validations\CommonValidations;
use App\Services\DocumentService;

class MapPicturesVideoController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyService;
    private $validations;
    private $documentService;
    private $mapVideoService;


    public function __construct(Request $request
        ,DocumentService $documentService
        , MapVideoService $mapVideoService
    )
    {
        Log::info("PropertyController: __construct called");
        $this->request                     = $request;
        $this->documentService             = $documentService;
        $this->mapVideoService             = $mapVideoService;

    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {

        Log::info("MapPicturesVideoController: index called");
        $info = $this->mapVideoService->findOneById($house_id);

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
    public function mapVideoUpdate($house_id)
    {
        Log::info("MapPicturesVideoController: mapVideoUpdate called");

        ## check input validation
        Log::info("MapPicturesVideoController: mapVideoUpdate update validation check");

        $rules = MapVideoValidations::mapValidation();
        $all = $this->request->all();
        $all['house_id']   = $house_id;
        $validator = Validator::make($all, $rules);
        $validator->validate();

        $this->mapVideoService->updateOrCreate( $all);

        return response()->json(['message' => __("messages.record_saved")], 200);
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function pictureIndex($house_id)
    {
        Log::info("MapPicturesVideoController: pictureIndex called");

        $info = $this->documentService->getPictureDocument($house_id);

        if (empty($info))
        {
            return response()->json(['data' => [],'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['data' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function pictureAdd()
    {

        #var_dump($this->request->all());die;
        Log::info("borrowerDocumentUpload: pictureAdd called");

        try
        {
            ## check input validation
            Log::info("pictureAdd: update validation check");
            $rules = MapVideoValidations::pictureAddValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            $doc_info = $this->documentService->documentUpload('document_picture');

            if ($doc_info != false)
            {
                $doc = $this->documentService->picture($doc_info);
                return response()->json(['message' => __("messages.picture_upload")
                                         , 'data'   => ['url' => $doc_info['document_url'], 'picture_id' => $doc->id]], 200);
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
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function pictureRemove($picture_id)
    {
        Log::info("MapPicturesVideoController: pictureRemove called");
        $info = $this->documentService->getPictureDocumentInfo($picture_id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists")], 400);
        }

        # File name
        $store_name  = $info->store_name;

        # Remove record from database
        $this->documentService->removePictureDocumentInfo($info);

        # check if same filename exists for other record or not
        $same_picture = DocumentPictureModel::where("store_name",$store_name)->count();

        if($same_picture > 0)
        {
            # Same File exists skip deleting
            Log::info("MapPicturesVideoController: pictureRemove- Same File exists skip deleting ".$same_picture);
        }
        else
        {
            # Remove file from storage
            $this->documentService->documentDelete($store_name,'document_picture');
        }

        return response()->json(['message' => __("messages.record_delete")], 200);
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSelectedImages($house_id){
        
        Log::info("MapPicturesVideoController: pictureRemove called");
        $all=$this->request->all();
        $pictureIds=$all['pictureIds'];
        if(!empty($pictureIds)){
            foreach($pictureIds  as $picture_id){
                $info = $this->documentService->getPictureDocumentInfo($picture_id);
                if (empty($info)) {
                    return response()->json(['message' => __("error_messages.record_not_exists")], 400);
                }
                # File name
                $store_name  = $info->store_name;
                # Remove record from database
                $this->documentService->removePictureDocumentInfo($info);
                # check if same filename exists for other record or not
                $same_picture = DocumentPictureModel::where("store_name",$store_name)->count();
                if($same_picture > 0) {
                    # Same File exists skip deleting
                    Log::info("MapPicturesVideoController: pictureRemove- Same File exists skip deleting ".$same_picture);
                }
                else{
                    # Remove file from storage
                    $this->documentService->documentDelete($store_name,'document_picture');
                }
            }
        }
        return response()->json(['message' => __("messages.record_delete")], 200);
    }

    public function updateSingleOrderRecord($house_id)
    {
        $all = $this->request->all();
        $value=[];
        //$value['house_id']  = $house_id;
        $value[$all['name']] = $all['value'];
        //$value['id']  = $all['id'];
        if(!empty(@$all['id']))
        {
            $info = DocumentPictureModel::where("id",$all['id'])->update($value);
            $info=$all['id'];
        }
        
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['row' => __("messages.record_saved"),'data' => $info], 200);

    }
}