<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        // \App\Console\Commands\BrockScottScraper::class,
         'App\Console\Commands\OffSiteCron',
         'App\Console\Commands\BrockScottScraper',
         'App\Console\Commands\davisrealScraper',
         'App\Console\Commands\hctaxScraper',
         'App\Console\Commands\ShapiroupcomingScraper',
         'App\Console\Commands\ShapiroupheldScraper',
         'App\Console\Commands\HutchensNCScraper',
         'App\Console\Commands\HutchensSCScraper',
         'App\Console\Commands\RlselawScraper',
         'App\Console\Commands\LogsGaUpcomingScraper',
         'App\Console\Commands\LogsAlUpcomingScraper',
         'App\Console\Commands\LogsAzUpcomingScraper',
         'App\Console\Commands\LogsCtUpcomingScraper',
         'App\Console\Commands\LogsIlUpcomingScraper',
         'App\Console\Commands\LogsMeUpcomingScraper',
         'App\Console\Commands\LogsMaNhRiUpcomingScraper',
         'App\Console\Commands\LogsMsUpcomingScraper',
         'App\Console\Commands\LogsAzUpheldScraper',
 
         'App\Console\Commands\LogsOrUpheldScraper',
         'App\Console\Commands\LogsWaUpheldScraper',
         'App\Console\Commands\LogsNyUpcomingScraper',
         'App\Console\Commands\LogsOrUpcomingScraper',
         'App\Console\Commands\LogsTnUpcomingScraper',
         'App\Console\Commands\LogsVaUpcomingScraper',
         'App\Console\Commands\LogsWaUpcomingScraper',
         'App\Console\Commands\AuctionScraper\AuctionNcUpcomingScraper',
         'App\Console\Commands\AuctionScraper\AuctionTnUpcomingScraper',
         'App\Console\Commands\AuctionScraper\AuctionTxUpcomingScraper',

         'App\Console\Commands\UpdateBuyItPosition',
         'App\Console\Commands\ResetBlockedUserFromCmaArv',
         //'App\Console\Commands\RoddysScraper',
 
         //Forecloser
         'App\Console\Commands\ForeclosehoustonTxHarrisScraper',
         'App\Console\Commands\Forecloser\ForeclosehoustonTxHarrisSaleScraper',
         'App\Console\Commands\Forecloser\ForeclosehoustonTxFortbendSales',
         'App\Console\Commands\Forecloser\ForeclosehoustonTxMontgomerySales',
     ];
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        Log::info("Kernel: scraper schedule called ".date('Y-m-d H:i:s'));
        //withoutOverlapping
        //When
        // ->withoutOverlapping(10);
        //runInBackground()
        //emailOutputOnFailure
        //Task Hooks

        // How to run on terminal
        //php artisan schedule:run >> /dev/null 2>&1
        //* * * * * cd /home/lumen/es_api && php artisan schedule:run >> /dev/null 2>&1
        // php artisan scraper:brock_scott
        // php artisan scraper:hutchens
        // php artisan scraper:sharpio
        // php artisan scraper:off_site
        $schedule->command('scraper:brock_scott')->timezone('America/New_York')->dailyAt('01:00','09:00','17:00')->runInBackground();
       
        $schedule->command('scraper:HutchensNC')->timezone('America/New_York')->dailyAt('01:00','09:00','17:00')->runInBackground();
        $schedule->command('scraper:HutchensSC')->timezone('America/New_York')->dailyAt('01:00','09:00','17:00')->runInBackground();
        
        $schedule->command('scraper:off_site')->timezone('America/New_York')->daily();

        $schedule->command('scraper:shapiroupcoming')->timezone('America/New_York')->dailyAt('01:00','09:00','17:00')->runInBackground();
        #$schedule->command('scraper:shapiroupheld')->timezone('America/New_York')->dailyAt('01:00','09:00','17:00')->runInBackground();
        $schedule->command('scraper:Rlselaw')->timezone('America/New_York')->dailyAt('01:00','09:00','17:00')->runInBackground();
       
        
        $schedule->command('scraper:logsAlUpcoming')->timezone('America/New_York')->dailyAt('01:05','09:05','17:05')->runInBackground();
        $schedule->command('scraper:logsAzUpcoming')->timezone('America/New_York')->dailyAt('01:10','09:10','17:10')->runInBackground();
        $schedule->command('scraper:logsAzUpheld')->timezone('America/New_York')->dailyAt('01:13','09:13','17:13')->runInBackground();
        $schedule->command('scraper:logsCtUpcoming')->timezone('America/New_York')->dailyAt('01:15','09:15','17:15')->runInBackground();
        $schedule->command('scraper:logsGaUpcoming')->timezone('America/New_York')->dailyAt('01:20','09:20','17:20')->runInBackground();
        $schedule->command('scraper:logsIlUpcoming')->timezone('America/New_York')->dailyAt('01:25','09:25','17:25')->runInBackground();
        $schedule->command('scraper:logsMeUpcoming')->timezone('America/New_York')->dailyAt('01:30','09:30','17:30')->runInBackground();
        $schedule->command('scraper:logsMsUpcoming')->timezone('America/New_York')->dailyAt('01:35','09:35','17:35')->runInBackground();
        $schedule->command('scraper:logsMaNhRiUpcoming')->timezone('America/New_York')->dailyAt('01:35','09:35','17:35')->runInBackground();

        $schedule->command('scraper:logsNyUpcoming')->timezone('America/New_York')->dailyAt('01:13','09:13','17:13')->runInBackground();
        $schedule->command('scraper:logsOrUpcoming')->timezone('America/New_York')->dailyAt('01:15','09:15','17:15')->runInBackground();
        $schedule->command('scraper:logsOrUpheld')->timezone('America/New_York')->dailyAt('01:20','09:20','17:20')->runInBackground();
        $schedule->command('scraper:logsTnUpcoming')->timezone('America/New_York')->dailyAt('01:25','09:25','17:25')->runInBackground();
        $schedule->command('scraper:logsVaUpcoming')->timezone('America/New_York')->dailyAt('01:30','09:30','17:30')->runInBackground();
        $schedule->command('scraper:logsWaUpcoming')->timezone('America/New_York')->dailyAt('01:35','09:35','17:35')->runInBackground();
        $schedule->command('scraper:logsWaUpheld')->timezone('America/New_York')->dailyAt('01:35','09:35','17:35')->runInBackground();
        $schedule->command('scraper:auctionTnUpcoming')->timezone('America/New_York')->dailyAt('02:00','09:00','17:00')->runInBackground();
        $schedule->command('estates:updateBuyItPosition')->timezone('America/New_York')->hourly();
        $schedule->command('estates:ResetBlockedUserFromCmaArv')->timezone('America/New_York')->hourly();

         //Forecloser
        $schedule->command('scraper:ForeclosehoustonTxHarrisScraper')->timezone('America/New_York')->daily()->runInBackground();
        $schedule->command('scraper:ForeclosehoustonTxHarrisSaleScraper')->timezone('America/New_York')->daily()->runInBackground();
        $schedule->command('scraper:ForeclosehoustonTxFortbendSaleScraper')->timezone('America/New_York')->daily()->runInBackground();
        $schedule->command('scraper:ForeclosehoustonTxMontgomerySaleScraper')->timezone('America/New_York')->daily()->runInBackground();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
     /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/New_York';
    }
}

