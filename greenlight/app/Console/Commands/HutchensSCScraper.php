<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exceptions\CustomException;
use App\Models\OffSiteModel;
use App\Models\PropertyModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;

class HutchensSCScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:HutchensSC';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraperService;


    public function __construct(ScraperService $scraperService)
    {
        Log::info("HutchensSCScraper: __construct called");

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
        Log::alert("HutchensScraper: running");
        Log::info("HutchensScraper: handle called");

        $scrapperUrl = config('constants.scraper_url.base_url') . 'hutchensSC';
        $states = config('constants.states');
        $response = Http::get($scrapperUrl);
        if ($response->ok()) {
            $newRecordCreated = ' Record Entries:';
            $json_response = $response->json();

            
            if (!empty($json_response)) {
                //Log::debug('data: ', $json_response);;
                $i=0;
                foreach ($json_response as $key => $value) {
                    Log::debug('index ==> '.$i);
                    $value = (object) $value;
                    $address=$value->property_address;
                    $scraperData = [
                        'county' => $value->county,
                        'state' => $value->state,
                        'address' => $value->property_address,
                        'zip' => !empty($value->zip_code) ? $value->zip_code : '',
                        'city' => $value->city
                    ];

                    $propertyObj = $this->scraperService->propertyAddress($address,$value->state);
                    if ($propertyObj->count() > 0) {
                        $property = $propertyObj->first();
                        $property->update($scraperData);
                        $this->storeSaleDetail($property->house_id,$value);
                        //$this->mortgageLiens($property->house_id,$value);
                        OffSiteModel::updateOrCreate(['house_id'=>$property->house_id],[
                            'off_site'=>0,
                            'scraper_file_name' => $this->signature
                        ]);

                    } else {
                        // New record created
                        $home = PropertyModel::create($scraperData);
                        $this->storeSaleDetail($home->house_id,$value);
                        //$this->mortgageLiens($home->house_id,$value);

                        OffSiteModel::updateOrCreate(['house_id'=>$home->house_id],[
                            'off_site'=>0,
                            'scraper_file_name' => $this->signature
                        ]);
                        $newRecordCreated .=  PHP_EOL ." HutchensScraper: New Record " .$value->property_address . ' --> '.$home->house_id;
                    }
                    $i++;
                }
                Log::alert($newRecordCreated);

            } else {
                // Throw an exception
                Log::alert("HutchensScraper: Scraper response is empty, Please check ");
                throw new CustomException('HutchensScraper: Scraper response is empty.');
            }
        } else {
            // Throw an exceptions
            Log::emergency('HutchensScraper: Something went wrong, Please check BrockScott Scraper');
            throw new CustomException('HutchensScraper: scraper status is not 200.');

        }
    }

    function storeSaleDetail($houseId,$hutchnes){
        Log::info("HutchensScraper: storeSaleDetail called");
        $trustee_file_no = Str::of($hutchnes->sp)->replace(' ', '');
        $saleDetail = [
            'house_id' => $houseId,
            'sale_date' => CustomHelper::date_format_database($hutchnes->sale_date),
            //'sale_time' => '',//@$hutchnes->sale_time,
            'case_number' => trim($trustee_file_no),
            'trustee_file_no' =>trim($hutchnes->case_number),
            'sale_place' =>trim($hutchnes->sale_place),
            'trustee_url'=>$hutchnes->trustee_url,'trustee_name'=>$hutchnes->trustee_name,
            'trustee_address'=>$hutchnes->trustee_address,'trustee_phone'=>$hutchnes->trustee_phone,
            'trustee_hours'=>$hutchnes->trustee_timing,'trustee'=>$hutchnes->trustee_name,
            //'opening_bid'=>$hutchnes->opening_bid
        ];

        if(!empty($hutchnes->deedNo_pageNo)){
            $bookPage=explode('/',$hutchnes->deedNo_pageNo);
            if(!empty(trim($bookPage[0]))){
                $saleDetail['book'] = (int) filter_var(trim($bookPage[0]), FILTER_SANITIZE_NUMBER_INT);
            }
            if(!empty(trim($bookPage[1]))){
                $saleDetail['page_number'] = (int) filter_var(trim($bookPage[1]), FILTER_SANITIZE_NUMBER_INT);
            }
        }
       
        $saleInfo   =   $this->scraperService->storeSaleDetail($houseId,$saleDetail);
        $this->bidderDetail($saleInfo->sale_id,$houseId,$hutchnes);

        if(!empty($hutchnes->before_auction_note)){
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$hutchnes->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
            'before_sale_trustee_notes'=>$hutchnes->before_auction_note,
        ];
            $this->scraperService->saleBeforeNotes($trusteeNotes);
        }

        if(!empty($hutchnes->after_auction_note)){
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$hutchnes->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
        '   after_sale_trustee_notes'=>$hutchnes->after_auction_note,
            ];

            $this->scraperService->saleAfterNotes($trusteeNotes);
        }
    }


    function bidderDetail($saleId,$houseId,$hutchnes){

        Log::info("HutchensScraper: bidderDeatil called");
        $bidDetail = (object) $hutchnes;
        if(!empty($bidDetail->bid_amount))
        {
            $bid_detail= [
                'sale_id'=> $saleId,
                'house_id'=> $houseId
                ,'bid_date'=>CustomHelper::date_format_database($bidDetail->upset_date),
                'bid_upset'=>$bidDetail->bid_status=='Postpone'?1:0,
                'amount_of_bid'=> $bidDetail->bid_amount
            ];
            
            if(!empty($bidDetail->bid_date) || !empty($bidDetail->bid_amount))
                $this->scraperService->bidderDetail($bid_detail);
        }

    }

    function mortgageLiens($houseId, $hutchnes)
    {
        Log::info("HutchensScraper: mortgageLiens called");
        $mortgageLiensDetail = array('house_id' => $houseId, 'dt_book_page' => $hutchnes->deedNo_pageNo);
        $this->scraperService->mortgageLiens($mortgageLiensDetail);
    }
}
