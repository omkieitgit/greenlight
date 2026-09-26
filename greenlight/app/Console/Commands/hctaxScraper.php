<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\PropertyModel;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;
use App\Services\PropertyDescriptionsService;

class hctaxScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:hctax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $scraperService;

    private  $propertyDescriptionsService;


    public function __construct(ScraperService $scraperService, PropertyDescriptionsService $propertyDescriptionsService)
    {
        Log::info("hctaxScraper: __construct called");

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
        Log::info("hctaxScraper: handle called");
        die;
        $scrapperUrl=config('constants.scraper_url.base_url').'hctax';
        $scraper_data=json_decode(file_get_contents($scrapperUrl));
        Log::info("Total sharpio Scraper : ".count($scraper_data));
        if(!empty($scraper_data)){
            foreach($scraper_data as $hctax){

                $state=(strlen(trim($hctax->state))==2)?trim($hctax->state):'';

                $hctaxData=array('county'=>$hctax->county,'state'=>$state,'parcel_id2'=>is_numeric($hctax->HCAD_account)?$hctax->HCAD_account:'',
                                'address'=>$hctax->address,'zip'=>!empty($hctax->zip_code)?$hctax->zip_code:'',
                    );

                $propertyObj=$this->scraperService->propertyAddress($hctax->address,$state);
                if($propertyObj->count()){
                    $property=$propertyObj->first();
                    $info = $this->scraperService->findOneById($property->house_id);
                    $info->update($hctaxData);
                    $this->addUpdateSaleDetail($property->house_id,$hctax);
                }else{

                    $home=PropertyModel::create($hctaxData);
                    $this->addUpdateSaleDetail($home->house_id,$hctax);

                }


            }
        }
    }
    function addUpdateSaleDetail($houseId,$hctax){

        $saleDetail=array('house_id'=>$houseId,'sale_date'=>CustomHelper::date_format_database($hctax->judgment),
            'priceint'=>$hctax->precinct,'case_number'=>$hctax->cause,
            'opening_bid'=>$hctax->minimum_bid!='' && strlen($hctax->minimum_bid)<12?str_replace('$','',$hctax->minimum_bid):'0'
        );

        $saleInfo=$this->scraperService->storeSaleDetail($houseId,$saleDetail);

        if(!empty($hctax->description)){
            $hctaxDesc=['legal_description'=>$hctax->description];
            $this->propertyDescriptionsService->updateOrCreate($houseId,$hctaxDesc);
        }

    }




}
