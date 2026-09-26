<?php

namespace App\Console\Commands\Forecloser;

use ClassPreloader\Config;
use Illuminate\Console\Command;
use App\Models\PropertyModel;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;
use App\Services\PropertyDescriptionsService;

class ForeclosehoustonTxHarrisSaleScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:ForeclosehoustonTxHarrisSaleScraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'foreclosehouston tx harris sale';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $scraperService;

    private  $propertyDescriptionsService;

    public function __construct(ScraperService $scraperService,PropertyDescriptionsService $propertyDescriptionsService)
    {
        Log::info("ForeclosehoustonTxHarrisSaleScraper: __construct called");
        parent::__construct();
        $this->scraperService=$scraperService;
        $this->propertyDescriptionsService=$propertyDescriptionsService;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("ForeclosehoustonTxHarrisSaleScraper: handle called");
        header('Content-type: application/json;');
        $scrapperUrl=config('constants.scraper_url.base_url').'foreclosehouston_tx_harris_sales';
        $scraper_data=file_get_contents($scrapperUrl);
        $scraper_data=json_decode($scraper_data,true);
       Log::info("Total ForeclosehoustonTxHarrisSaleScraper  Scraper : ".count($scraper_data));
        
        if(!empty($scraper_data)){
            $i=0;
            foreach($scraper_data as $foreclosehouston){
                
                $foreclosehouston=(object) $foreclosehouston;
                Log::info(" ForeclosehoustonTxHarrisSaleScraper Scraper : ".$i);
            
                $foreclosehoustonData=array('county'=>$foreclosehouston->county,'state'=>$foreclosehouston->state,
                                    'address'=>$foreclosehouston->address,'zip'=>!empty($foreclosehouston->zip_code)?$foreclosehouston->zip_code:'','city'=>$foreclosehouston->city);
                
                $propertyObj=$this->scraperService->propertyForecloseAddress($foreclosehouston->address,$foreclosehouston->state);
                if($propertyObj->count()){
                    $property=$propertyObj->first();
                    $info = $this->scraperService->findOneById($property->house_id);
                    $info->update($foreclosehoustonData);
                    $this->addUpdateSaleDetail($property->house_id,$foreclosehouston);
                }else{

                    $home=PropertyModel::create($foreclosehoustonData);
                    $this->addUpdateSaleDetail($home->house_id,$foreclosehouston);
                }
                $i++;
            }
        }
    }

    function addUpdateSaleDetail($houseId,$foreclosehouston){

        if($foreclosehouston->sale_date){
            $foreclosehouston->sale_date=$foreclosehouston->sale_date;
            $this->storeSaleDetail($houseId,$foreclosehouston);
        }
    }

    function storeSaleDetail($houseId,$foreclosehouston){

        $saleDetail=array('house_id'=>$houseId,
                         'sale_date'=>CustomHelper::date_format_database($foreclosehouston->sale_date),
                         'opening_bid'=>$foreclosehouston->opening_bid,
                         'newspapaer_url'=>$foreclosehouston->notice_url);

        $saleInfo=$this->scraperService->storeSaleDetail($houseId,$saleDetail);
        
        $this->bidderDetail($saleInfo->sale_id,$houseId,$foreclosehouston);

        if(!empty($foreclosehouston->legal_description)){
            $forecloseDesc=['legal_description'=>$foreclosehouston->legal_description];
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
    


}
