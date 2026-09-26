<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Models\PropertyModel;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;

class ShapiroupheldScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:shapiroupheld';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shapiro up held scraper';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $scraperService;


    public function __construct(ScraperService $scraperService)
    {
        Log::info("ShapiroScraper: __construct called");
        parent::__construct();
        $this->scraperService=$scraperService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("ShapiroScraper: handle called");
        header('Content-type: application/json;');
        $scrapperUrl=config('constants.scraper_url.base_url').'shapiroupheld';
        $scraper_data=file_get_contents($scrapperUrl);
        $scraper_data=json_decode($scraper_data,true);
        Log::info("Total shapiroupheld Scraper : ".count($scraper_data));
        
        if(!empty($scraper_data)){
            $i=0;
            foreach($scraper_data as $shapiro){
               
                $shapiro=(object) $shapiro;
                Log::info(" shapiroupheld Scraper : ".$i);
                $states=config('property_information.states');
                $state=array_search($shapiro->state,$states);
                
                $shapiroData=array('county'=>$shapiro->county,'state'=>$state,
                                    'address'=>$shapiro->address,'zip'=>!empty($shapiro->zip)?$shapiro->zip:'','city'=>$shapiro->city);
                
                $propertyObj=$this->scraperService->propertyAddress($shapiro->address,$state);
                if($propertyObj->count()){
                    $property=$propertyObj->first();
                    $info = $this->scraperService->findOneById($property->house_id);
                    $info->update($shapiroData);
                    $this->addUpdateSaleDetail($property->house_id,$shapiro);
                }else{

                    $home=PropertyModel::create($shapiroData);
                    $this->addUpdateSaleDetail($home->house_id,$shapiro);
                }
                $i++;
            }
        }
    }

    function addUpdateSaleDetail($houseId,$shapiro){

        if($shapiro->sale_date){
            $shapiro->sale_date=$shapiro->sale_date;
            $this->storeSaleDetail($houseId,$shapiro);
        }
        if(!empty($shapiro->sale_date1)){
            $shapiro->sale_date=$shapiro->sale_date1;
            $this->storeSaleDetail($houseId,$shapiro);
        }

    }

    function storeSaleDetail($houseId,$shapiro){

        $sale_status_list=config('property_information.sale_status');
       // $sa_status=($shapiro->bid_status=='Ready')?'Active':(!empty($shapiro->bid_status)?$shapiro->bid_status:'N/A');
        //$sale_status=array_search($sa_status,$sale_status_list);
        
        $openingBid=0;
        if(!empty($shapiro->opening_bid) && $shapiro->opening_bid!='N/A'){
            $openingBid=str_replace(',','',$shapiro->opening_bid);
        }
        
        $saleDetail=array('house_id'=>$houseId,'sale_date'=>CustomHelper::date_format_database($shapiro->sale_date),
                        //'sale_time'=>$shapiro->sale_time,
                        'case_number'=>$shapiro->case_number,
                        'trustee_url'=>$shapiro->trustee_url,'trustee'=>$shapiro->trustee,
                        'trustee_address'=>$shapiro->trustee_address,'trustee_phone'=>$shapiro->trustee_phone,
                        'trustee_hours'=>$shapiro->trustee_hours,
                       // 'sale_status'=>$sale_status,
                        'opening_bid'=>$openingBid);
                        

        $saleInfo=$this->scraperService->storeSaleDetail($houseId,$saleDetail);
       
        $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
                        'trustee_name'=>$shapiro->trustee,
                        'scrape_date_time'=>date('Y-m-d H:i:s'),
                        'after_sale_trustee_notes'=>$shapiro->after_auction_notes,
                        ];
        $this->scraperService->saleAfterNotes($trusteeNotes);
        $this->updateBidderInfo($houseId,$saleInfo->sale_id,$shapiro);
    }

    function updateBidderInfo($houseId,$saleId,$shapiro){
        $bid_detail= [
            'sale_id'=> $saleId,
            'house_id'=> $houseId,
            'bid_date'=>CustomHelper::date_format_database($shapiro->bidder_date),
            'bid_upset'=>$shapiro->bidder_upset=='upset'?1:0,
            'name_upset_bidder'=>$shapiro->bidder_name,
            'last_date_to_upset_bid'=>$shapiro->upset_date,
            'amount_of_bid'=> !empty($shapiro->bidder_amount)?str_replace('$','',$shapiro->bidder_amount):'0.00'
        ];
        $this->scraperService->bidderDetail($bid_detail);
    }
    
}
