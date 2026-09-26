<?php
/**
 * Created By Rativardhan Singh Sengar  11/6/18 8:41 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/31/18 1:18 AM
 */

namespace App\Services;

use App\Models\DocumentBidderModel;
use App\Models\DocumentBorrowerModel;
use App\Models\DocumentClientBiddingFundsModel;
use App\Models\DocumentMortgage;
use App\Models\DocumentMortgageHoa;
use App\Models\DocumentMortgageOther;
use App\Models\DocumentMortgagePropertyTaxesModel;
use App\Models\DocumentOwnerModel;
use App\Models\DocumentPictureModel;
use App\Models\DocumentPropertyModel;
use App\Models\DocumentSaleModel;
use App\Models\DocumentClientModel;
use App\Models\ClientMasterClosingDocModel;
use App\Models\ClientRenovationtModel;
use App\Models\DocumentAccountingModel;
use App\Models\DocumentMortgageTax;
use App\Models\ListingDocumentModel;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;
use Illuminate\Support\Facades\Response as Download;

class DocumentService
{
    private $getOwnerDocument;
    private $getBorrowerDocument;
    private $getPictureDocument;
    private $getPictureDocumentInfo;
    private $getPropertyDocument;
    private $request;
    private $disk;

    private $documentType = ['document_property',
                              'document_owner'
                              , 'document_borrower'
                              , 'document_picture'
                              , 'document_sale'
                              , 'document_bidder'
                              , 'document_mortgage'
                              , 'document_mortgage_other'
                              , 'document_mortgage_hoa'
                              , 'document_mortgage_property_taxes'
                              , 'client_document'
                              , 'client_master_closing_doc'
                              , 'document_client_renovation_de'
                              , 'document_client_renovation_st'
                              , 'non_hud_expenditure'
                              , 'document_client_bidding_funds_authorization'
                              , 'document_client_bidding_funds_receipt'
                              , 'document_renovation_costs'
                              , 'document_accounting'
                              , 'terms_and_use'
                              , 'document_mortgage_tax'
                              , 'document_investor'
                              , 'document_agreement'
                              , 'document_artical'
                              , 'document_ein'
                              , 'document_statement'
                              , 'document_rental'
                              , 'listing_document'
                              , 'subto_document'
                              , 'vehicle_document' 
                              , 'vehicle_nos_document' 

    ];

    private $userHiddenField = ['password','master_password'];

    /**
     * DocumentService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        Log::info("DocumentService: __construct called");
        $this->request = $request;
        //$this->disk    = Storage::disk('public');
        $this->disk    = Storage::disk('digitalocean');
    }
    public function viewDocument($fileName, $document_type){
        $is_file = $document_type.'/'.$fileName;
        if ($this->disk->exists($is_file)) {
            Log::info("getDocument: exists return url and content and path ");
            $fileType=$this->disk->mimeType($is_file);
            $headers = [
                'Content-Type'        => 'Content-Type: '.$fileType,
                'Content-Disposition' => 'inline; filename="'. $fileName .'"',
            ];
            return Download::make($this->disk->get($is_file), 200, $headers); 
            //header('Content-Transfer-Encoding: binary');
            //header('Accept-Ranges: bytes');
  
        }
    }

    public function getDocument($fileName, $document_type)
    {
        Log::info("DocumentService: getDocument called");
        if (!in_array($document_type, $this->documentType))
        {
            Log::alert("getDocument: Invalid document type upload");
            return false;
        }

        $is_file = $document_type.'/'.$fileName;
        if ($this->disk->exists($is_file)) {
            Log::info("getDocument: exists return url and content and path ");
            $url =  $this->disk->url($is_file);
            $content = $this->disk->get($is_file);
            return ['url'=>$url ,
                'content' => $content
            ];
        }
        else{

            Log::alert("getDocument: File not exists");
            return false;
        }

    }
    public function saveDocument($fileName, $content, $document_type)
    {
        Log::info("DocumentService: saveDocument called");
        if (!in_array($document_type, $this->documentType))
        {
            Log::alert("saveDocument: Invalid document type upload");
            return false;
        }

        $is_file = $document_type.'/'.$fileName;
        $filename_rename = $document_type.'/'.Date('Y_m_d_his').$fileName;
        if ($this->disk->exists($is_file)) {
            Log::info("saveDocument: documentDelete deleted");
            $this->disk->copy($is_file, $filename_rename);
            $this->disk->delete($is_file);
        }

        # Upload file into server folder
        Log::info("saveDocument: Storage");
        $isUpload = $this->disk->put($is_file, $content);

        if($isUpload)
        {
            # public URL of document
            return $this->disk->url($document_type . '/' . $fileName);
        }
        else
        {
            Log::alert("saveDocument: Something went wrong, Please check this.");
            return false;
        }

    }

    /**
     * @param $file
     * @param $document_type
     * @return bool
     */
    public function documentDelete($file, $document_type)
    {
        Log::info("DocumentService: documentDelete called");
        if (!in_array($document_type, $this->documentType))
        {
            Log::error("documentUpload: Invalid document type upload");
            return false;
        }

        $is_file = $document_type.'/'.$file;

        if ($this->disk->exists($is_file)) {
            Log::info("DocumentService: documentDelete deleted");
            $this->disk->delete($is_file);
        }
        else{

            Log::info("DocumentService: documentDelete File not exists");
            return false;
        }
        return true;

    }

    /**
     * @param $document_type
     * @param string $field_name
     * @return array|bool
     */
    public function documentUploadBase64($all, $document_type = "document")
    {
        Log::info("DocumentService: documentUploadBase64 called");
        if (!in_array($document_type, $this->documentType))
        {
            Log::error("documentUploadBase64: Invalid document type upload");
            return false;
        }

        $base64data = $all[$document_type];
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            @list($type, $base64data ) = explode(';', $base64data );
            @list(, $base64data ) = explode(',', $base64data );
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }

        // decoding and then reeconding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }

        Log::info("documentUploadBase64: file_document isValid");

        $ext = last(explode('.',$all['org_name']));

        # create unique file name using file content
        $content         = base64_decode($base64data);
        $ext             = $ext;
        $store_file_name = md5($content) . '.' . $ext;

        # Upload file into server folder
        Log::info("documentUpload: Storage");

        $isUpload = $this->disk->put($document_type . '/' .$store_file_name, $content);

        if($isUpload)
        {
            # public URL of document
            //$document_url = $this->disk->url('document_property/' . $store_file_name);
            $document_url = $this->disk->url($document_type . '/' . $store_file_name);
            # Save info into database
            $request_info                 = $this->request->all();
            $request_info['store_name']   = $store_file_name;
            $request_info['org_name']     = $all['org_name'];
            $request_info['added_by']     = $this->request->auth->id;
            $request_info['document_url'] = $document_url;
            return $request_info;
        }
        else
        {
            return false;
        }


    }

    /**
     * @param $document_type
     * @param string $field_name
     * @return array|bool
     */
    public function documentUpload($document_type, $field_name = "document")
    {

        Log::info("DocumentService: documentUpload called");
        if (!in_array($document_type, $this->documentType))
        {
            Log::error("documentUpload: Invalid document type upload");
            return false;
        }

        $file_document = $this->request->file($field_name);
        if (empty($file_document))
        {
            Log::error("documentUpload: document content is empty " . $field_name);
            return false;
        }

        if ($file_document->isValid())
        {

            Log::info("documentUpload: file_document isValid");

            # create unique file name using file content
            $content         = file_get_contents($file_document->getRealPath());
            
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $file_document->getRealPath());
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // $content = curl_exec($ch);

            $ext             = $file_document->getClientOriginalExtension();
            $store_file_name = md5($content) . '.' . $ext;

            # Upload file into server folder
            Log::info("documentUpload: Storage");

            $this->disk->putFileAs($document_type, $file_document, $store_file_name);
            //Storage::disk('digitalocean')->putFile('uploads', request()->file, 'public');

            # public URL of document
            //$document_url = $this->disk->url('document_property/' . $store_file_name);
            $document_url = url('document/'.$document_type . '/' . $store_file_name);

            # Save info into database
            $request_info                 = $this->request->all();
            $request_info['store_name']   = $store_file_name;
            $request_info['org_name']     = $file_document->getClientOriginalName();
            $request_info['added_by']     = $this->request->auth->id;
            $request_info['document_url'] = $document_url;
            return $request_info;
        }

        return false;

    }

    /**
     * @param $info
     * @return mixed
     */
    public function picture($info)
    {
        Log::info("DocumentService: picture called");
        $info['created_at'] = time();
        return DocumentPictureModel::create($info);
    }



    public function client_bidding_funds($info)
    {
        Log::info("DocumentService: client_bidding_funds called");
        $info['created_at'] = time();
        return DocumentClientBiddingFundsModel::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function owner($info)
    {
        Log::info("DocumentService: owner called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentOwnerModel::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function borrower($info)
    {
        Log::info("DocumentService: owner called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentBorrowerModel::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function property($info)
    {
        Log::info("DocumentService: property called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentPropertyModel::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function sale($info)
    {
        Log::info("DocumentService: sale called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentSaleModel::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function bidder($info)
    {
        Log::info("DocumentService: bidder called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentBidderModel::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function mortgage_liens($info)
    {
        Log::info("DocumentService: mortgage_liens called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentMortgage::create($info);
    }


    /**
     * @param $info
     * @return mixed
     */
    public function mortgage_other_liens($info)
    {
        Log::info("DocumentService: mortgage_other_liens called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentMortgageOther::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function mortgage_hoa_liens($info)
    {
        Log::info("DocumentService: mortgage_hoa_liens called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentMortgageHoa::create($info);
    }

    public function mortgage_tax_liens($info)
    {
        Log::info("DocumentService: mortgage_hoa_liens called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentMortgageTax::create($info);
    }

    /**
     * @param $info
     * @return mixed
     */
    public function mortgage_property_taxes($info)
    {
        Log::info("DocumentService: mortgage_property_taxes called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        return DocumentMortgagePropertyTaxesModel::create($info);
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return DocumentOwnerModel[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getOwnerDocument($house_id, $is_cache = false)
    {
        Log::info("DocumentService: getOwnerDocument called");
        if ($is_cache == true)
        {
            return $this->getOwnerDocument;
        }

        $this->getOwnerDocument = DocumentOwnerModel::
        with('user')
        ->where('house_id', $house_id)->get();

        return $this->getOwnerDocument;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return DocumentBorrowerModel[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBorrowerDocument($house_id, $is_cache = false)
    {
        Log::info("DocumentService: getBorrowerDocument called");
        if ($is_cache == true)
        {
            return $this->getBorrowerDocument;
        }

        /*$this->getBorrowerDocument = DocumentBorrowerModel::leftJoin('users', function ($join)
        {
            $join->on('users.id', '=', 'added_by');
        })->where('house_id', $house_id)->get();
         $this->getBorrowerDocument->makeHidden($this->userHiddenField);
        */
        $this->getBorrowerDocument = DocumentBorrowerModel::with('user')
            ->where('house_id', $house_id)->get();

        return $this->getBorrowerDocument;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getPropertyDocument($house_id, $is_cache = false)
    {
        Log::info("DocumentService: getPropertyDocument called");
        if ($is_cache == true)
        {
            return $this->getPropertyDocument;
        }

        $this->getPropertyDocument = DocumentPropertyModel::with('user')
            ->where('house_id', $house_id)->get();

        return $this->getPropertyDocument;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getPictureDocument($house_id, $is_cache = false)
    {
        Log::info("DocumentService: getPictureDocument called");
        if ($is_cache == true)
        {
            return $this->getPictureDocument;
        }

        $this->getPictureDocument = DocumentPictureModel::with('user')->where('house_id', $house_id)->orderBy('order','ASC')->get();

        return $this->getPictureDocument;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function getPictureDocumentInfo($picture_id, $is_cache = false)
    {
        Log::info("DocumentService: getPictureDocumentInfo called");
        if ($is_cache == true)
        {
            return $this->getPictureDocumentInfo;
        }

        $this->getPictureDocumentInfo = DocumentPictureModel::find($picture_id);

        return $this->getPictureDocumentInfo;
    }

    /**
     * @param $house_id
     * @param bool $is_cache
     * @return mixed
     */
    public function removePictureDocumentInfo($picture_info)
    {

        if(empty($picture_info))
            return false;

        $picture_info->delete();
        return true;

    }

     /**
     * @param $info
     * @return mixed
     */
    public function client($info)
    {
        Log::info("DocumentService: Client called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        $info['added_by']     = $this->request->auth->id;
        return DocumentClientModel::create($info);
    }


     /**
     * @param $info
     * @return mixed
     */
    public function clientMasteeClosingDoc($info)
    {
        Log::info("DocumentService: Client called");
        $info['created_at'] = time();
        $info['updated_at'] = time();
        $info['added_by']     = $this->request->auth->id;
        return ClientMasterClosingDocModel::create($info);
    }


    /**
    * @param $info
    * @return mixed
    */
   public function accountingDocument($info)
   {
       Log::info("DocumentService: accounting called");
       $info['created_at'] = time();
       $info['updated_at'] = time();
       $info['added_by']     = $this->request->auth->id;
       return DocumentAccountingModel::create($info);
   }


   public function listingDocument($info)
   {
       Log::info("ListingService: Client called");
       $info['created_at'] = time();
       $info['updated_at'] = time();
       $info['added_by']     = $this->request->auth->id;
       return ListingDocumentModel::create($info);
   }


//    public function getDocument(){
//     if ($this->disk->exists('file.jpg')) {
//         // ...
//     }
//    }

   
}
