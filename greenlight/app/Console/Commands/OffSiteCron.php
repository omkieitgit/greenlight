<?php

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use App\Models\OffSiteModel;
use Illuminate\Console\Command;
use App\Models\PropertyModel;
use App\Models\SaleDetailsModel;
use App\Models\MortgageLiensModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Log;
use App\Services\ScraperService;
use App\Helpers\CustomHelper;

class OffSiteCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:off_site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Off Site Cron';


    public function __construct()
    {
        Log::info("OffSiteCron: __construct called");
        parent::__construct();
    }

    public function handle()
    {
        Log::info("OffSiteCron: handle called");
        Log::alert("OffSiteCron: called tp update offsite");
        //$article->touch();
        OffSiteModel::where('updated_at',"<=",strtotime('-24 hour'))->update(['off_site'=>1]);

    }


}
