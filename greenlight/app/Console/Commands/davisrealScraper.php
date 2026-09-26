<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertyModel;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;


class davisrealScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:davisreal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraperService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ScraperService $scraperService)
    {
        Log::info("DavisrealScraper: __construct called");

        $this->scraperService = $scraperService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("DavisrealScraper: handle called");
        die;
        $scrapperUrl=config('constants.scraper_url.base_url').'davisreal';
        $scraper_data=json_decode(file_get_contents($scrapperUrl));
        Log::info("Total sharpio Scraper : ".count($scraper_data));
        if(!empty($scraper_data)){
            foreach($scraper_data as $davisreal){

                $state=(strlen(trim($davisreal->state))==2)?trim($davisreal->state):'';

                $davisrealData=array('county'=>$davisreal->county,'state'=>$state,
                                   'address'=>$davisreal->address,
                                    'parcel_id1'=>$davisreal->parcel_id1,
                                    'parcel_id2'=>$davisreal->parcel_id2
                                   );

                $propertyObj=$this->scraperService->propertyAddress($davisreal->address,$state);
                if($propertyObj->count()){
                    $property=$propertyObj->first();
                    $info = $this->scraperService->findOneById($property->house_id);
                    $info->update($davisrealData);
                    $this->storeSaleDetail($property->house_id,$davisreal);
                }else{

                    $home=PropertyModel::create($davisrealData);
                    $this->storeSaleDetail($home->house_id,$davisreal);

                }
            }
        }
    }

    function storeSaleDetail($houseId,$davisreal){

            $saleDetail=array('house_id'=>$houseId,'sale_date'=>CustomHelper::date_format_database($davisreal->sale_date),
                            'trustee'=>$davisreal->trustee_name,'trustee_address'=>$davisreal->trustee_address,
                            'trustee_phone'=>$davisreal->phone_number,
                            'opening_bid'=>$davisreal->opening_bid!=''?str_replace('$','',$davisreal->opening_bid):'0' );

            $this->scraperService->storeSaleDetail($houseId,$saleDetail);
    }

}
