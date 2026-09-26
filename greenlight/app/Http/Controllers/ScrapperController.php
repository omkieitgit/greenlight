<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use App\Services\ScrapperService;



class ScrapperController extends Controller
{
    //

    private $request;
    private $validations;
    private $scrapperService;

    /**
     * SaleDetailsController constructor.
     * @param Request $request
     */
    public function __construct(Request $request,scrapperService $scrapperService)
    {
        Log::info("ScrapperController: __construct called");
        $this->request                        = $request;
        $this->scrapperService                = $scrapperService;
    }

    function updateScrapperData($id){

        $scrapper_info=$this->request->all();

        $scrapper_data=$scrapper_info;



        if(!empty($scrapper_data['price_history'])){
            $this->scrapperService->deletePriceHistory($id);
            $i=0;
            foreach ($scrapper_data['price_history']['date'] as $priceHistory){
                $history=array('house_id'=>$id,
                    'price_date'=>date('Y-m-d',strtotime($priceHistory)),
                    'price'=>str_replace('$','',$scrapper_data['price_history']['price'][$i]),
                    'source'=>$scrapper_data['price_history']['source'][$i]);
                $this->scrapperService->createPriceHistory($history);
                $i++;

            }
        }
        if(!empty($scrapper_data['legal_description'])){
            $this->scrapperService->deletePropertyDesc($id);
            $desc['property_description']=$scrapper_data['property_description'];
            $desc['legal_description']=$scrapper_data['legal_description'];
            $desc['house_id']=$id;
            $this->scrapperService->createPropertyDesc($desc);

        }
        unset($scrapper_data['property_description']);
        unset($scrapper_data['legal_description']);
        unset($scrapper_data['price_history']);
        $this->scrapperService->updatePropertyInfo($id,$scrapper_data);



        return response()->json(['message' => __("messages.record_saved")], 200);
    }
}
