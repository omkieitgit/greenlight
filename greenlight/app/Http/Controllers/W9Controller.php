<?php

namespace App\Http\Controllers;
use Validator;
Use Log;

use Illuminate\Http\Request;
use App\Services\W9Service;

use App\Models\W9Model;
use App\Http\Validations\W9Validations;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Storage;
use Response;
use App\Services\McdService;
use App\Services\MailService;
use Illuminate\Support\Facades\Crypt;


class W9Controller extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $W9Service;
    private $mcdService;
    private $mailService;

    /**
     * @param W9Service $W9Service
     */

    public function __construct(Request $request
        
        , W9Service $W9Service
        , McdService $mcdService
        , MailService $mailService
        
    )
    {
        Log::info("W9Controller: __construct called");
        $this->request         = $request;
        $this->W9Service                = $W9Service;
        $this->mcdService = $mcdService;
        $this->mailService = $mailService;
        
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function w9Index($user_id)
    {
        Log::info("W9Controller: W9Index called");

        //$info = $this->W9Service->findAllByHouseId($house_id);
        $info = $this->W9Service->findAllByUserId($user_id);
        if(!empty($info->social_security_number_3)){
            $info->ss_number='XXX-XX-'.Crypt::decryptString($info->social_security_number_3);
        }
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }

        return response()->json(['data' => $info], 200);
    }



    public function w9Create()
    {
        #var_dump($this->request->all());die;
        Log::info("W9Controller: w9Create called");

        try
        {
            
            ## check input validation
            Log::info("w9Create: update validation check");
            $rules = W9Validations::W9Validation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();

            
            # first check record exists or not
            /*$info = $this->propertyService->findOneById($this->request->input('house_id'));

            if (empty($info))
            {
                return response()->json(['message' => __("error_messages.house_id_exists")], 400);
            }*/

            $all=$this->request->all();
            $all['social_security_number']=Crypt::encryptString($all['social_security_number']);
            $all['social_security_number_2']=Crypt::encryptString($all['social_security_number_2']);
            $all['social_security_number_3']=Crypt::encryptString($all['social_security_number_3']);
            # update information
            $info = $this->W9Service->createW9Info($all);
            $tradeshUser=$this->mcdService->getTradeshmanUser($all['user_id']);
            $tradeshUser[0]->wInfo->social_security_number='XXX-XX-'.Crypt::decryptString($tradeshUser[0]->wInfo->social_security_number);
        
            return response()->json(['data'=>$tradeshUser[0], 'message' => __("messages.record_saved")], 200);
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


     /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function W9InfoUpdate($house_id)
    {
        Log::info("W9Controller: W9InfoUpdate called");

        ## check input validation
        Log::info("W9Controller: update validation check");
        $rules = W9Validations::W9Validation();

        $all = $this->request->all();
        $user_id = $all['user_id']??'';
        
        $validator = Validator::make($all, $rules);
        $validator->validate();

        $all['federal_tax']=json_encode($all['federal_tax_data']);
        
        if(!empty($all['social_security_number'])){
            $all['social_security_number']=Crypt::encryptString($all['social_security_number']);
        }else{
            unset($all['social_security_number']);
        }
        if(!empty($all['social_security_number_2'])){
            $all['social_security_number_2']=Crypt::encryptString($all['social_security_number_2']);
        }else{
            unset($all['social_security_number_2']);
        }
        if(!empty($all['social_security_number_3'])){
            $all['social_security_number_3']=Crypt::encryptString($all['social_security_number_3']);
        }else{
            unset($all['social_security_number_3']);
        }
        # update information
        $this->W9Service->updatew9($user_id, $all);
        $tradeshUser=$this->mcdService->getTradeshmanUser($all['user_id']);
        $tradeshUser[0]->wInfo->social_security_number='XXX-XX-'.Crypt::decryptString($tradeshUser[0]->wInfo->social_security_number);
        return response()->json(['message' => __("messages.record_saved"),'data'=>$tradeshUser[0]], 200);
    }

    public function tradesmanW9($user_id){
        $info = $this->W9Service->findAllByUserId($user_id);
        $w9=storage_path('app/estates/w9.fdf');
        $file = fopen($w9, "r");
        $content='';
        while(!feof($file)) {
            $line = fgets($file);
            $content.=$this->replaceInfo($line,$info);
            
          }
          fclose($file);
        Storage::disk('estates')->put('input.fdf', $content);
        $inputFile=storage_path('app/estates/input.fdf');
        $pdfFile=storage_path('app/estates/fw9.pdf');
        $outputPath=storage_path('app/estates/output.pdf');
        
        $cmd="pdftk $pdfFile fill_form $inputFile output $outputPath";
        //echo $cmd;
         exec($cmd.' 2>&1', $output);

        $path = storage_path('app/estates/output.pdf');
        return response()->download($path, 'example.pdf', [], 'inline');
    }


    public function postsendW9Email(){
        $all = $this->request->all();
        $info = $this->W9Service->findAllByUserId($all['user_id']);
        //echo Storage::disk('estates')->url('fwp.pdf');
        $w9=storage_path('app/estates/w9.fdf');
        $file = fopen($w9, "r");
        $content='';
        while(!feof($file)) {
            $line = fgets($file);
            $content.=$this->replaceInfo($line,$info);
        }
        fclose($file);
        Storage::disk('estates')->put('input.fdf', $content);
        $inputFile=storage_path('app/estates/input.fdf');
        $pdfFile=storage_path('app/estates/fw9.pdf');
        $outputPath=storage_path('app/estates/output.pdf');
        
        $cmd="pdftk $pdfFile fill_form $inputFile output $outputPath";
        //echo $cmd;
         exec($cmd.' 2>&1', $output);
         //coleen@theestates.com
         $emails=['may@theestates.com','patricia@theestates.com','vikas@theestates.com'];
         foreach($emails as $email){
            $data=['email'=>$email,'subject'=>'W9 - '.$info->name,'outputPath'=>$outputPath];
            $this->mailService->sendW9Email($data);
         }
         return response()->json(['message' => __("messages.w9_email_send"),'data'=>''], 200);
    }


    public function postTradesmanW9(){
        $all = $this->request->all();
        $info = $this->W9Service->findAllByUserId($all['user_id']);
        //echo Storage::disk('estates')->url('fwp.pdf');
        $w9=storage_path('app/estates/w9.fdf');
        $file = fopen($w9, "r");
        $content='';
        while(!feof($file)) {
            $line = fgets($file);
            $content.=$this->replaceInfo($line,$info);
        }
        fclose($file);
        Storage::disk('estates')->put('input.fdf', $content);
        $inputFile=storage_path('app/estates/input.fdf');
        $pdfFile=storage_path('app/estates/fw9.pdf');
        $outputPath=storage_path('app/estates/output.pdf');
        
        $cmd="pdftk $pdfFile fill_form $inputFile output $outputPath";
        //echo $cmd;
         exec($cmd.' 2>&1', $output);

        //$filename = 'output.pdf';
        //$path = storage_path('app/estates/output.pdf');
        return response()->download($outputPath, 'example.pdf', [], 'inline');
    }

    function replaceInfo($content,$info){

        $snsArray=['social_security_number','social_security_number_2','social_security_number_3'];
        $federal_tax=json_decode($info->federal_tax);

        $infoArray=['name'=>'NAME','business_name'=>'BUSINESS_NAME',
                    'federal_tax'=>'FEDERAL_TAX',
                    'address'=>'ADDRESS','city'=>'CITY',
                    'account_number'=>'ACCOUNT_NUMBER',
                    'requesters_name_address'=>'REQUESTER',
                    'exempt_payee_code'=>'EXEMPT_CODE',
                    'exemption_FATCA_reporting_code'=>'EXEMPTION_CODE',
                    'taxpayer_identification_number'=>'TAXPAYER',
                    'employer_identification_number'=>'EMPLYOER',
                    'employer_identification_number_2'=>'EMPLYOER_2',
                    'social_security_number'=>'SSN_NUMBER',
                    'social_security_number_2'=>'SSN_NUMBER_1',
                    'social_security_number_3'=>'SSN_NUMBER_2',
                    'signature'=>'SIGNATURE',
                    'tax_classification'=>'tax_classification',
                    'other_instructions'=>'other_instructions',
                    ];
        
        foreach($infoArray as $key=>$value){
            $keyName='##'.$value.'##';
            if($content && strpos($content,$keyName)!==false){
                $keyValue=$info->$key;
                if(in_array($key,$snsArray)){
                    $keyValue=Crypt::decryptString($info->$key);
                }
                $content=str_replace($keyName,$keyValue,$content);
            }
        }

        foreach($federal_tax as $key=>$value){
            $keyName='##'.$key.'##';
            $content=str_replace($keyName,$value,$content);
        }
       
        return $content;
    }

    function viewSsNumber($user_id){
        $info = $this->W9Service->findAllByUserId($user_id);

        $snsArray['social_security_number']=$info->social_security_number?Crypt::decryptString($info->social_security_number):'';
        $snsArray['social_security_number_2']=$info->social_security_number_2?Crypt::decryptString($info->social_security_number_2):'';
        $snsArray['social_security_number_3']=$info->social_security_number_3?Crypt::decryptString($info->social_security_number_3):'';
        
        return response()->json(['message' => __("messages.w9_email_send"),'data'=>$snsArray], 200);
        
    }
}