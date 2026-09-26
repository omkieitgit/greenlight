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

class RlselawScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:Rlselaw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraperService;


    public function __construct(ScraperService $scraperService)
    {
        Log::info("RlselawScraper: __construct called");

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
        Log::alert("RlselawScraper: running");
        Log::info("RlselawScraper: handle called");

        $scrapperUrl = config('constants.scraper_url.base_url') . 'rlselaw';
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
                        'state' => 'GA',//trim($value->state),
                        'address' => $value->property_address,
                        'zip' => !empty($value->zip_code) ? $value->zip_code : '',
                        'city' => $value->city
                    ];

                    $propertyObj = $this->scraperService->propertyAddress($address);
                    if ($propertyObj->count() > 0) {
                        $property = $propertyObj->first();
                        $property->update($scraperData);
                        $this->storeSaleDetail($property->house_id,$value);
                        OffSiteModel::updateOrCreate(['house_id'=>$property->house_id],[
                            'off_site'=>0,
                            'scraper_file_name' => $this->signature
                        ]);

                    } else {
                        // New record created
                        $home = PropertyModel::create($scraperData);
                        $this->storeSaleDetail($home->house_id,$value);

                        OffSiteModel::updateOrCreate(['house_id'=>$home->house_id],[
                            'off_site'=>0,
                            'scraper_file_name' => $this->signature
                        ]);
                        $newRecordCreated .=  PHP_EOL ." RlselawScraper: New Record " .$value->property_address . ' --> '.$home->house_id;
                    }
                    $i++;
                }
                Log::alert($newRecordCreated);

            } else {
                // Throw an exception
                Log::alert("RlselawScraper: Scraper response is empty, Please check ");
                throw new CustomException('RlselawScraper: Scraper response is empty.');
            }
        } else {
            // Throw an exceptions
            Log::emergency('RlselawScraper: Something went wrong, Please check Rlselaw Scraper');
            throw new CustomException('RlselawScraper: scraper status is not 200.');

        }
    }

    function storeSaleDetail($houseId,$rlselaw){
        Log::info("RlselawScraper: storeSaleDetail called");
        $trustee_file_no = Str::of($rlselaw->sp)->replace(' ', '');
        $saleDetail = [
            'house_id' => $houseId,
            'sale_date' => CustomHelper::date_format_database($rlselaw->sale_date),
            //'sale_time' => '',//@$rlselaw->sale_time,
            'case_number' => trim($trustee_file_no),
            'trustee_file_no' =>trim($rlselaw->case_number),

            'trustee_url'=>$rlselaw->trustee_url,'trustee_name'=>$rlselaw->trustee_name,
            'trustee_address'=>$rlselaw->trustee_address,'trustee_phone'=>$rlselaw->trustee_phone,
            'trustee_hours'=>$rlselaw->trustee_timing,'trustee'=>$rlselaw->trustee_name,
            //'opening_bid'=>$rlselaw->opening_bid
        ];
        if(!empty($rlselaw->deedNo_pageNo)){
            $bookPage=explode('/',$rlselaw->deedNo_pageNo);
            if(!empty(trim($bookPage[0]))){
                $saleDetail['book'] = (int) filter_var(trim($bookPage[0]), FILTER_SANITIZE_NUMBER_INT);
            }
            if(!empty(trim($bookPage[1]))){
                $saleDetail['page_number'] = (int) filter_var(trim($bookPage[1]), FILTER_SANITIZE_NUMBER_INT);
            }
        }
        $saleInfo   =   $this->scraperService->storeSaleDetail($houseId,$saleDetail);
        $this->bidderDetail($saleInfo->sale_id,$houseId,$rlselaw);

        if(!empty($rlselaw->before_auction_note)){
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$rlselaw->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
            'before_sale_trustee_notes'=>$rlselaw->before_auction_notes,
            ];
            
            $this->scraperService->saleBeforeNotes($trusteeNotes);
        }

        if(!empty($rlselaw->after_auction_note)){
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$rlselaw->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
            'after_sale_trustee_notes'=>$rlselaw->after_auction_notes,
            ];
            $this->scraperService->saleAfterNotes($trusteeNotes);
        }


    }


    function bidderDetail($saleId,$houseId,$rlselaw){

        Log::info("RlselawScraper: bidderDeatil called");
        $bidDetail = (object) $rlselaw;
        if(!empty($bidDetail->bid_amount))
        {
            $bid_detail= [
                'sale_id'=> $saleId,
                'house_id'=> $houseId
                ,'bid_date'=>!empty($bidDetail->upset_date)?CustomHelper::date_format_database($bidDetail->upset_date):'',
                'bid_upset'=>!empty($bidDetail->bid_status)?$bidDetail->bid_status=='Postpone'?1:0:'',
                'amount_of_bid'=> $bidDetail->bid_amount
            ];

            if(!empty($bidDetail->bid_date) || !empty($bid_detail->amount_of_bid))
            $this->scraperService->bidderDetail($bid_detail);
        }

    }

    
}
