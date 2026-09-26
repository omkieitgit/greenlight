<?php

namespace App\Http\Controllers;


use App\Models\UserPlanModel;
use App\Services\DocumentService;
use Barryvdh\DomPDF\PDF;
use Validator;
Use Log;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $pdf;
    private $documentService;

    /**
     * InfoController constructor.
     * @param Request $request
     * @param PDF $pdf
     */
    public function __construct(Request $request,
    PDF $pdf,

    DocumentService $documentService
    )
    {
        Log::info("InfoController: __construct called");
        $this->request              = $request;
        $this->pdf                  = $pdf;
        $this->documentService      = $documentService;
    }

    public function faq()
    {
        Log::info("InfoController: faq called");
        return view('info.faq');
    }

    public function privacyPolicy()
    {
        Log::info("InfoController: privacyPolicy called");
        return view('info.privacy_policy');
    }

    public function returnPolicy()
    {
        Log::info("InfoController: returnPolicy called");
        return view('info.return_policy');
    }

    public function termsAndCondition()
    {
        Log::info("InfoController: termsAndCondition called");
        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        $parse_data['is_hide_by_logging'] = 1;
        return view('info.terms_and_condition', $parse_data);
    }

    public function isAgree()
    {
        Log::info("InfoController: isAgree called");
        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        return view('info.is_agree_content', $parse_data);
    }

    public function isPopup()
    {
        Log::info("InfoController: isPopup called");
        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);
        return view('info.agreement_pop_up_addendum', $parse_data);
    }

    public function termsAndConditionPDF()
    {

        Log::info("InfoController: termsAndConditionPDF called");

        $key = $this->request->input('refresh');

        $fileInfo = $this->documentService->getDocument('terms_and_use.pdf','terms_and_use');
        if($fileInfo != false && $key != 'reload_pdf' )
        {
            return response()
                ->make($fileInfo['content'], 200
                    , ['Content-Type' => 'application/pdf',]
                );
        }

        $parse_data                = [];
        $path_binary               = file_get_contents(resource_path('assets/images/es_logo.png'));
        $parse_data['logo'] = 'data:image/jpg;base64,' . base64_encode($path_binary);

        $html =  view('info.is_agree_content', $parse_data);

        $this->pdf;
        $this->pdf->loadHTML($html);
        $this->pdf->setPaper('a4', 'portrait');
        $this->pdf->setOptions([
            'isFontSubsettingEnabled'=>true,
            'isHtml5ParserEnabled'=>true
        ]);

        $info = $this->pdf->loadHTML($html)->output();
        $doc_info = $this->documentService->saveDocument('terms_and_use.pdf',$info,'terms_and_use');

        
        return response()
            ->make($info, 200
                , ['Content-Type' => 'application/pdf',]
            );
    }

}
