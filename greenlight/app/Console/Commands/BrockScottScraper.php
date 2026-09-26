<?php

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use App\Models\OffSiteModel;
use Illuminate\Console\Command;
use App\Models\PropertyModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;

class BrockScottScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:brock_scott';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Brock Scott Scraper';
    private $scraperService;

    /**
     * brockScottScraper constructor.
     * @param ScraperService $scraperService
     */
    public function __construct(ScraperService $scraperService)
    {
        Log::info("BrockScottScraper: __construct called");
        parent::__construct();
        $this->scraperService = $scraperService;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::alert("BrockScottScraper: running");
        Log::info("BrockScottScraper: handle called");
        $scrapperUrl = config('constants.scraper_url.base_url') . 'brockandscott';
        $states = config('constants.states');
        $response = Http::get($scrapperUrl);
        if ($response->ok()) {
            $newRecordCreated = ' Record Entries:';
            $json_response = $response->json();
            if (!empty($json_response)) {
                //Log::debug('data: ', $json_response);;
                $i=0;
                foreach ($json_response as $key => $value) {
                    Log::debug('Index : ==> '.$i);
                    $value = (object)$value;
                    $scraperData = [
                        'county' => $value->county,
                        'state' => $value->state,
                        'address' => $value->address,
                        'zip' => !empty($value->zip_code) ? $value->zip_code : '',
                        'city' => $value->city
                    ];

                    $propertyObj = $this->scraperService->propertyAddress($value->address,$value->state);

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

                        $newRecordCreated .=  PHP_EOL ." BrockScottScraper: New Record " .$value->address . ' --> '.$home->house_id;
                    }
                    $i++;
                }
                Log::alert($newRecordCreated);
            } else {
                // Throw an exception
                Log::alert("BrockScottScraper: Scraper response is empty, Please check ");
                throw new CustomException('BrockScottScraper: Scraper response is empty.');
            }
        } else {
            // Throw an exceptions
            Log::emergency('BrockScottScraper: Something went wrong, Please check BrockScott Scraper');
            throw new CustomException('BrockScottScraper: scraper status is not 200.');

        }
    }

    function storeSaleDetail($houseId, $brockScott)
    {
        Log::info("BrockScottScraper: storeSaleDetail called");

        $trustee_file_no = Str::of($brockScott->sp)->replace(' ', '');
        $saleDetail = [
            'house_id' => $houseId,
            'sale_date' => CustomHelper::date_format_database($brockScott->sale_date),
            'sale_time' => $brockScott->sale_time,
            'case_number' => trim($trustee_file_no),
            'trustee_file_no' => $brockScott->case_number,
            'opening_bid' => $brockScott->opening_bid,
            'trustee_url'=>$brockScott->trustee_url,'trustee_name'=>$brockScott->trustee_name,
            'trustee_address'=>$brockScott->trustee_address,'trustee_phone'=>$brockScott->trustee_phone,
            'trustee_hours'=>$brockScott->trustee_timing,'trustee'=>$brockScott->trustee_name,
        ];
        if(!empty($brockScott->deedNo_pageNo)){
            $bookPage=explode('/',$brockScott->deedNo_pageNo);
            if(!empty(trim($bookPage[0]))){
                $saleDetail['book'] = (int) filter_var(trim($bookPage[0]), FILTER_SANITIZE_NUMBER_INT);
            }
            if(!empty(trim($bookPage[1]))){
                $saleDetail['page_number'] = (int) filter_var(trim($bookPage[1]), FILTER_SANITIZE_NUMBER_INT);
            }
        }
        $saleInfo  =  $this->scraperService->storeSaleDetail($houseId, $saleDetail);
       
        if(!empty($brockScott->before_auction_note)){
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$brockScott->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
            'before_sale_trustee_notes'=>$brockScott->before_auction_note,
            ];
            $this->scraperService->saleBeforeNotes($trusteeNotes);
        }
        if(!empty($brockScott->after_auction_note)){
            $trusteeNotes=[ 'sale_id'=>$saleInfo->sale_id,
            'trustee_name'=>$brockScott->trustee_name,
            'scrape_date_time'=>date('Y-m-d H:i:s'),
            'after_sale_trustee_notes'=>$brockScott->after_auction_note,
            ];
            $this->scraperService->saleAfterNotes($trusteeNotes);
        }
    }

    function mortgageLiens($houseId, $brockScott)
    {
        Log::info("BrockScottScraper: mortgageLiens called");
        $mortgageLiensDetail = array('house_id' => $houseId, 'dt_book_page' => $brockScott->deedNo_pageNo);
        $this->scraperService->mortgageLiens($mortgageLiensDetail);
    }

}
