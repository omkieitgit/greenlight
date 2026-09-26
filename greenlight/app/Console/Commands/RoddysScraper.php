<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertyModel;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;
use App\Services\PropertyDescriptionsService;
use DateTime;
class RoddysScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:roddys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Roddys scraper for TX state';

     /**
     * Create a new command instance.
     *
     * @return void
     */
    private $scraperService;

    private  $propertyDescriptionsService;

    public function __construct(ScraperService $scraperService,PropertyDescriptionsService $propertyDescriptionsService)
    {
        Log::info("RoddysScraper: __construct called");
        parent::__construct();
        $this->scraperService=$scraperService;
        $this->propertyDescriptionsService=$propertyDescriptionsService;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
     /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("RoddysScraper: handle called");
        header('Content-type: application/json;');
        $scrapperUrl=config('constants.scraper_url.base_url').'roddys';
        $scraper_data=file_get_contents($scrapperUrl);
        $scraper_data=json_decode($scraper_data,true);
       Log::info("Total RoddysScraper  Scraper : ".count($scraper_data));
        
        if(!empty($scraper_data)){
            $i=0;
            foreach($scraper_data as $roddys){
                
                $roddys=(object) $roddys;
                Log::info(" RoddysScraper Scraper : ".$i);
            
                $roddysData=array('county'=>$roddys->county,'state'=>$roddys->state,
                                    'address'=>$roddys->address,
                                    'zip'=>!empty($roddys->zip_code)?$roddys->zip_code:'',
                                    'city'=>$roddys->city,
                                    'built_year'=>$roddys->built_year,
                                    'lot_acreage_sf'=>$roddys->built_sqft);
                
                $propertyObj=$this->scraperService->propertyForecloseAddress($roddys->address,$roddys->state);
                if($propertyObj->count()){
                    // $property=$propertyObj->first();
                    // $info = $this->scraperService->findOneById($property->house_id);
                    // $info->update($roddysData);
                    // $this->addUpdateSaleDetail($property->house_id,$roddys);
                }else{

                    $home=PropertyModel::create($roddysData);
                    $this->addUpdateSaleDetail($home->house_id,$roddys);
                }
                $i++;
            }
        }
    }

    function addUpdateSaleDetail($houseId,$roddys){
        $firstTueOfNextMonth=$this->first_tuesday("0");
        if($firstTueOfNextMonth < date('Y-m-d')){
        $firstTueOfNextMonth= $this->first_tuesday("+1");   
        }
        $roddys->sale_date=$firstTueOfNextMonth;
        if($roddys->sale_date){
            $this->storeSaleDetail($houseId,$roddys);
        }
    }

    function storeSaleDetail($houseId,$roddys){

        $saleDetail=array('house_id'=>$houseId,
                         'sale_date'=>CustomHelper::date_format_database($roddys->sale_date),
                         'opening_bid'=>$roddys->opening_bid, 
                         'trustee_name'=>$roddys->trustee_name,
                         'sale_time'=>$roddys->sale_time);

        $saleInfo=$this->scraperService->storeSaleDetail($houseId,$saleDetail);
        
        $this->bidderDetail($saleInfo->sale_id,$houseId,$roddys);

        if(!empty($roddys->legal_description)){
            $forecloseDesc=['legal_description'=>$roddys->legal_description];
            $this->propertyDescriptionsService->updateOrCreate($houseId,$forecloseDesc);
        }

    }

    function bidderDetail($saleId,$houseId,$bidDetail){

        Log::info("ForeclosehoustonTxHarris: bidderDeatil called");
        
        if(!empty($bidDetail->name_upset_bidder))
        {
            $bid_detail= [
                'sale_id'=> $saleId,
                'house_id'=> $houseId,
                'name_upset_bidder'=>!empty($bidDetail->name_upset_bidder)?$bidDetail->name_upset_bidder:'',
                'amount_of_bid'=> $bidDetail->amount_of_bid
            ];
            $this->scraperService->bidderDetail($bid_detail);
        }

    }


function first_tuesday($month=""){
    $day = new DateTime(sprintf("First Tuesday of $month month %s", date('Y')));
    return $day->format('Y-m-d');
}

    
}
