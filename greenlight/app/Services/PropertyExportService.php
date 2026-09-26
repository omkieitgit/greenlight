<?php namespace App\Services;

use App\Services\PropertyService;
use App\Services\AssessmentService;
use App\Services\MortgageLiensService;
use App\Models\MortgageLiensModel;


class PropertyExportService{
        
        private $assessmentService;
        private $propertyService;
        private $mortgageLiensService;


        public function __construct( PropertyService $propertyService
                , AssessmentService $assessmentService
                , MortgageLiensService $mortgageLiensService
        ){
                $this->propertyService             = $propertyService;
                $this->assessmentService           = $assessmentService;    
                $this->mortgageLiensService        = $mortgageLiensService;    
        }


        function getPropertyInfo($house_id){
                $property_info=[];
                $property = $this->propertyService->findOneById($house_id);
                $i=0;$j=0;
                $info=[];
                foreach(collect($property) as $key=>$value){
                    $info[$i][]=$key;
                    $info[$i][] = $value;
                    if($j==6){
                        $i++;
                        $j=0;
                    }
                    $j++;
                }
                return $info;
        }

        function getAssessment($house_id){
                $header=[];
                $header[]='Property Tax Owed';
                $header[]='Owed Year';
                $header[]='Tax Assessed';
                $header[]='Tax Year';
                $temp[]   = $header;
                $assessment = $this->assessmentService->findAllByHouseId($house_id);
                foreach($assessment as $asset){
                    $parse_data = [];
                    $parse_data['Property Tax Owed'] = $asset->property_taxes_owed;
                    $parse_data['Owed Year'] =  $asset->property_taxes_owed_year;
                    $parse_data['Tax Assessed'] = $asset->taxes_assessed;
                    $parse_data['Tax Year'] = $asset->taxes_year;
                    $temp[] = $parse_data;
                }
                return $temp;   
        }

        function getMortgageOtherLien($house_id){

                $mortgage_liens = $this->mortgageLiensService->findAllLiensByHouseId($house_id);
                $header=[]; 
                $i=0;
                foreach(collect($mortgage_liens) as $key=>$value){
                        $collection = collect($value);
                        if($i==0){
                                $headers=$collection->keys();
                                foreach($headers as $head){
                                        $header[]=ucwords(str_replace('_',' ',$head));
                                }
                                $temp[]   = $header;
                        }
                        foreach($collection as $mortgage){
                                $parse_data[] = $mortgage;
                        }
                        $temp[]   = $parse_data;
                        $i++;
                }
               
                return $temp;
        }

        function getMortgageInfo(){
                MortgageLiensModel::
                select(['lien_type',
                'lien_foreclosing as first_lien_foreclosing',
                'no_str_no_appt as no_STR',
                'defective_lien',
                'lender'])
                ->where(["house_id"=>$house_id])->get();
        }
}