<?php

namespace App\Http\Controllers;
use Validator;
Use Log;
use Illuminate\Http\Request;
use App\Services\PropertyService;
use App\Services\McdService;
use App\Services\Form1099MiscService;
use App\Http\Validations\W9Validations;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Storage;


class Form1099MiscController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $propertyService;
    private $form1099MiscService;
    private $mcdService;

    /**
     * @param DocumentService $documentService
     */

    public function __construct(Request $request
        , PropertyService $propertyService
        , Form1099MiscService $form1099MiscService
        , McdService $mcdService
    )
    {
        Log::info("ClientController: __construct called");
        $this->request                = $request;
        $this->propertyService        = $propertyService;
        $this->form1099MiscService    = $form1099MiscService;
        $this->mcdService             = $mcdService;
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info("Form1099MiscController: W9Index called");
        $limit  = $this->request->get('limit') ? $this->request->get('limit') : 20;
        $offset = $this->request->get('offset') ? $this->request->get('offset') : 0;
        
        $payers_id=$this->request->get('payers_id')??'';
        $recipient_id=$this->request->get('recipient_id')??'';
        $form_year=$this->request->get('form_year')??'';


        $info = $this->form1099MiscService->findForm1099Misc($payers_id,$recipient_id,$form_year,$offset,$limit);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['data' => $info], 200);
    }

    function getMscForm($house_id){
        Log::info("Form1099MiscController: W9Index called");
        $info = $this->form1099MiscService->findForm1099MscByHouseId($house_id);
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['data' => $info], 200);

    }

    function getRecipient(){
        $info = $this->form1099MiscService->getRecipints();
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['data' => $info], 200);
    }

    function getPayers(){
        $info = $this->mcdService->getPayersInfoList();
        if (empty($info))
        {
            return response()->json(['message' => __("error_messages.record_not_exists"), 'data' => []], 200);
        }
        return response()->json(['data' => $info], 200);
    }

    public function form1099MiscCreate()
    {
        #var_dump($this->request->all());die;
        Log::info("Form1099MiscController: form1099MiscCreate called");

        try
        {
            ## check input validation
            Log::info("form1099MiscCreate: update validation check");
            $rules = W9Validations::form1099MiscValidation();

            $validator = Validator::make($this->request->all(), $rules);
            $validator->validate();
            # first check record exists or not
            $info = $this->propertyService->findOneById($this->request->input('house_id'));
            if (empty($info))
            {
                return response()->json(['message' => __("error_messages.house_id_exists")], 400);
            }
            # update information
            $all = $this->request->all();
            $all['created_at'] = time();
            $info = $this->form1099MiscService->createForm1099Misc($all);
            return response()->json(['row' => ['house_id' => $info->house_id], 'message' => __("messages.record_saved")], 200);
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }


    public function form1099MiscUpdate($house_id)
    {
        #var_dump($this->request->all());die;
        Log::info("Form1099MiscController: form1099MiscUpdate called");

        try
        {
            ## check input validation
            $all = $this->request->all();
            $all['house_id'] = $house_id;
            Log::info("form1099MiscUpdate: update validation check");
            $rules = W9Validations::form1099MiscValidation();
            $validator = Validator::make($all, $rules);
            $validator->validate();
            # first check record exists or not
            $info = $this->propertyService->findOneById($house_id);
            if (empty($info))
            {
                return response()->json(['message' => __("error_messages.house_id_exists")], 400);
            }
            # update information
            $all = $this->request->all();
            $id=$all['id']??'';
            $info = $this->form1099MiscService->createUpdateRecipients($id, $all);

            return response()->json(['data' =>$info, 'message' => __("messages.record_saved")], 200);
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }

    }

    function msc1099FormPdf(){

        Log::info("Form1099MiscController: msc1099FormPdf called");
        try
        {
            ## check input validation
            $all = $this->request->all();
            //$all['house_id'] = $house_id;
            Log::info("msc1099FormPdf: update validation check");
            $rules = W9Validations::msc1099FormPdfValidation();
            $validator = Validator::make($all, $rules);
            $validator->validate();

            $info   = $this->form1099MiscService->getRecipints($all['recipients_id']);
            $payers = $this->mcdService->getPayersInfoList($all['payers_id']);
            $info->amount= $all['amount'];
            $info->state_income_amount = $all['amount'];
            $collection = collect($info);
            $merged = $collection->merge($payers);
            $merged->all();
            
            //$merged['state_income_amount'] = $all['amount'];
            # update information
            $msc1099=storage_path('app/estates/msc1099.fdf');
            $file = fopen($msc1099, "r");
            $content='';
        
            while(!feof($file)) {
                $line = fgets($file);
                $content.=$this->replaceInfo($line,$merged);
                
            }
            fclose($file);
            
            Storage::disk('estates')->put('1099_input.fdf', $content);
            $inputFile=storage_path('app/estates/1099_input.fdf');
            $pdfFile=storage_path('app/estates/f1099msc.pdf');
            $outputPath=storage_path('app/estates/1099_output.pdf');
            
            $cmd="pdftk $pdfFile fill_form $inputFile output $outputPath";
            //echo $cmd;
            exec($cmd.' 2>&1', $output);

            //$filename = 'output.pdf';
            //$path = storage_path('app/estates/output.pdf');
            return response()->download($outputPath, 'example.pdf', [], 'inline');
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 200);
        }
    }

    function replaceInfo($content,$info){
        
        $infoArray=['payers_name'=>'PAYERS_NAME',
                    'payers_address'=>'payers_address',
                    'payers_tin'=>'PAYERS_TIN',
                    'recipients_name'=>'RECIPIENTS_NAME',
                    'recipients_tin'=>'RECIPIENTS_TIN',
                    'recipients_address'=>'RECIPIENTS_ADDRESS',
                    'recipients_city_state'=>'RECIPIENTS_CITY_STATE',
                    'amount'=>'AMOUNT',
                    'state_income_amount'=>'STATE_INCOME_AMOUNT'
                    ];
        
        foreach($infoArray as $key=>$value){
            $keyName='##'.$value.'##';
            if($content && strpos($content,$keyName)!==false){
                $data=$info[$key];
                if($key=='payers_name'){
                    $data=$info[$key].'\n'.$info['payers_address'];
                }
                $content=str_replace($keyName,$data,$content);
            }
        }
        return $content;
    }

}
