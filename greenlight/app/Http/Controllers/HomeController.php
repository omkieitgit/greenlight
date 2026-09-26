<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DocumentService;
use Log;
use Illuminate\Support\Facades\Response as Download;

class HomeController extends Controller
{
    //
    private $documentService;
    public function __construct(DocumentService $documentService )
    {
        Log::info("HomeController: __construct called");
        $this->documentService      = $documentService;
    }

    function index(){
        return view('consumer/index');
    }
    function about(){
        return view('consumer/static/about');
    }
    function services(){
        return view('consumer/static/services');
    }

    function properties(){
        return view('consumer/static/property');
    }

    function blog(){
        return view('consumer/static/blog');
    }
    
    function contact(){
        return view('consumer/static/contact');
    }

    function viewDocument($documentType, $documentName){
         $result=$this->documentService->viewDocument($documentName,$documentType);
        if(!empty($result)){
            return $result;
        }else
        {
            echo "<h1 style='text-align:center'>Document not found</h1>";
        }
    }
}
