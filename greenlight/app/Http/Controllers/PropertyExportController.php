<?php namespace App\Http\Controllers;



use Validator;
Use Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

use App\Services\PropertyService;
use App\Services\AssessmentService;
use App\Services\PropertyExportService;

use App\Exports\CommonExport;
use App\Exports\PropertyExport;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Excel;

class PropertyExportController extends Controller
{
        private $request;
        private $validations;
        private $propertyService;
        private $propertyDescriptionsService;
        private $assessmentService;
        private $localRealEstateService;
        private $priceHistoryService;
        private $schoolNeighborhoodService;
        private $documentService;
        private $userFavoritesService;
        private $userService;
        private $clientService;
        private $excel;
        private $propertyExportService;
        
        public function __construct(Request $request
                , PropertyService $propertyService
                , AssessmentService $assessmentService
                // , PropertyDescriptionsService $propertyDescriptionsService
                // , LocalRealEstateService $localRealEstateService
                // , PriceHistoryService $priceHistoryService
                // , SchoolNeighborhoodService $schoolNeighborhoodService
                // , DocumentService $documentService
                // , UserFavoritesService $userFavoritesService
                // , UserService $userService
                // , McdService $mcdService
                // , ClientService $clientService
                , Excel $excel
                , PropertyExportService $propertyExportService
        )
        {
                Log::info("PropertyController: __construct called");
                $this->request                     = $request;
                $this->propertyService             = $propertyService;
                $this->assessmentService           = $assessmentService;
                // $this->propertyDescriptionsService = $propertyDescriptionsService;
                // $this->localRealEstateService      = $localRealEstateService;
                // $this->priceHistoryService         = $priceHistoryService;
                // $this->schoolNeighborhoodService   = $schoolNeighborhoodService;
                // $this->documentService             = $documentService;
                // $this->userFavoritesService        = $userFavoritesService;
                // $this->userService                 = $userService;
                // $this->mcdService                  = $mcdService;
                // $this->clientService                  = $clientService;  
                $this->excel                  = $excel;
                $this->propertyExportService           = $propertyExportService;
        }
        function exportPropertInfo($house_id){

                
                $property_info['Property Details']=$this->propertyExportService->getPropertyInfo($house_id);
                $property_info['Assessment & Taxes']=$this->propertyExportService->getAssessment($house_id);
                $property_info['Mortgage, Other Liens']=$this->propertyExportService->getMortgageOtherLien($house_id);
                
                //$export = new CommonExport($info);
                $export = new PropertyExport($property_info);
               
                #ToDo: temporary fix, deleteFileAfterSend: false creating a lot of temp files, Please fix this.
                $info = $this->excel->download($export, 'ES_' . time() . '.xlsx')->deleteFileAfterSend(false);
                return $info;
                //(new InvoicesExport)->download('<div>VIKAS</div>', \Maatwebsite\Excel\Excel::HTML);
        
        
        }

}