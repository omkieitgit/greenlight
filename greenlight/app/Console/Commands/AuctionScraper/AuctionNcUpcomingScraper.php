<?php

namespace App\Console\Commands\AuctionScraper;

use ClassPreloader\Config;
use Illuminate\Console\Command;
use App\Models\PropertyModel;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;

class AuctionNcUpcomingScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:auctionNcUpcoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auction Nc Scraper';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $scraperService;


    public function __construct(ScraperService $scraperService)
    {
        Log::info("auctionNcUpcomingScraper: __construct called");
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
        Log::info("auctionNcUpcomingScraper: handle called");
        header('Content-type: application/json;');
        $scrapperUrl=config('constants.scraper_url.base_url').'auction_nc_upcoming';
        $scraper_data=file_get_contents($scrapperUrl);
        $scraper_data=json_decode($scraper_data,true);
       Log::info("Total auctionNcUpcoming Scraper : ".count($scraper_data));
        
        if(!empty($scraper_data)){
            $i=0;
            foreach($scraper_data as $auction){
                
                $auction=(object) $auction;
                Log::info(" auctionNcUpcoming Scraper : ".$i);
            
                $auctionData=['county'=>$auction->county,'state'=>$auction->state,
                              'address'=>$auction->full_address,'zip'=>!empty($auction->zip)?$auction->zip:'',
                              'city'=>$auction->city,'bed'=>$auction->beds,
                              'bath'=>$auction->baths,'parcel_id1'=>$auction->apn,
                              'lot_acreage_sf'=>$auction->lot_size,
                              'total_sqft'=>$auction->built_sqft,'year_built'=>$auction->built_year];
                
                $propertyObj=$this->scraperService->propertyAddress($auction->full_address,$auction->state);
                if($propertyObj->count()){
                    $property=$propertyObj->first();
                    $info = $this->scraperService->findOneById($property->house_id);
                    //$info->update($auctionData);
                    $this->addUpdateSaleDetail($property->house_id,$auction);
                }else{

                    $home=PropertyModel::create($auctionData);
                    $this->addUpdateSaleDetail($home->house_id,$auction);
                }
                $i++;
            }
        }
    }

    function addUpdateSaleDetail($houseId,$auction){

        if($auction->sale_date){
            $auction->sale_date=$auction->sale_date;
            $this->storeSaleDetail($houseId,$auction);
        }
    }

    function storeSaleDetail($houseId,$auction){

        $sale_status_list=config('property_information.sale_status');
        $sa_status=($auction->bid_status=='Ready')?'Active':(!empty($auction->bid_status)?$auction->bid_status:'N/A');
        $sale_status=array_search($sa_status,$sale_status_list);
        
        $openingBid=0;
        if(!empty($auction->opening_bid) && $auction->opening_bid!='N/A'){
            $openingBid=str_replace(',','',$auction->opening_bid);
        }
        
        $saleDetail=array('house_id'=>$houseId,'sale_date'=>CustomHelper::date_format_database($auction->sale_date),
                        'sale_time'=>$auction->sale_time,'trustee_file_no'=>$auction->case_number,
                        'trustee_url'=>$auction->trustee_url,'trustee_name'=>$auction->trustee_name,
                        'trustee_address'=>$auction->trustee_address,'trustee_phone'=>$auction->trustee_phone,
                        'trustee_hours'=>$auction->trustee_timing,'trustee'=>$auction->trustee_name,
                        'sale_status'=>$sale_status,'auction_com_url'=>$auction->trustee_url,
                        'auction_date_pulled'=>date('Y-m-d'),
                        'opening_bid'=>$openingBid);

        
        $saleInfo=$this->scraperService->getSaleDate($houseId,$saleDetail);
        if(!$saleInfo){
            $saleInfo=$this->scraperService->storeSaleDetail($houseId,$saleDetail);
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$auction->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
            'before_sale_trustee_notes'=>$auction->before_auction_note,
            ];
            $this->scraperService->saleBeforeNotes($trusteeNotes);
        }
        

    }
    


}
