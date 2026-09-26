<?php

namespace App\Console\Commands;

use ClassPreloader\Config;
use Illuminate\Console\Command;
use App\Models\PropertyModel;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;

class LogsMeUpcomingScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:logsMeUpcoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logs ME Scraper';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $scraperService;


    public function __construct(ScraperService $scraperService)
    {
        Log::info("logsMeUpcomingScraper: __construct called");
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
        Log::info("logsMeUpcomingScraper: handle called");
        header('Content-type: application/json;');
        $scrapperUrl=config('constants.scraper_url.base_url').'logs_me_upcoming';
        $scraper_data=file_get_contents($scrapperUrl);
        $scraper_data=json_decode($scraper_data,true);
       Log::info("Total logsMeUpcoming Scraper : ".count($scraper_data));
        
        if(!empty($scraper_data)){
            $i=0;
            foreach($scraper_data as $shapiro){
                
                $shapiro=(object) $shapiro;
                Log::info(" logsMeUpcoming Scraper : ".$i);
            
                $shapiroData=array('county'=>$shapiro->county,'state'=>$shapiro->state,
                                    'address'=>$shapiro->address,'zip'=>!empty($shapiro->zip)?$shapiro->zip:'','city'=>$shapiro->city);
                
                $propertyObj=$this->scraperService->propertyAddress($shapiro->address,$shapiro->state);
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
        $sa_status=($shapiro->bid_status=='Ready')?'Active':(!empty($shapiro->bid_status)?$shapiro->bid_status:'N/A');
        $sale_status=array_search($sa_status,$sale_status_list);
        
        $openingBid=0;
        if(!empty($shapiro->opening_bid) && $shapiro->opening_bid!='N/A'){
            $openingBid=str_replace(',','',$shapiro->opening_bid);
        }
        
        $saleDetail=array('house_id'=>$houseId,'sale_date'=>CustomHelper::date_format_database($shapiro->sale_date),
                        'sale_time'=>$shapiro->sale_time,'case_number'=>$shapiro->case_number,
                        'trustee_url'=>$shapiro->trustee_url,'trustee_name'=>$shapiro->trustee_name,
                        'trustee_address'=>$shapiro->trustee_address,'trustee_phone'=>$shapiro->trustee_phone,
                        'trustee_hours'=>$shapiro->trustee_timing,'trustee'=>$shapiro->trustee_name,
                        'sale_status'=>$sale_status,
                        'opening_bid'=>$openingBid);

        $saleInfo=$this->scraperService->storeSaleDetail($houseId,$saleDetail);
        $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
        'trustee_name'=>$shapiro->trustee_name,
        'scrape_date_time'=>date('Y-m-d H:i:s'),
        'before_sale_trustee_notes'=>$shapiro->before_auction_note,
        ];
        $this->scraperService->saleBeforeNotes($trusteeNotes);

    }
    


}
