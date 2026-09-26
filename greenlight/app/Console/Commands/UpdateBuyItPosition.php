<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HouseBuyItService;
use Log;

class UpdateBuyItPosition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estates:updateBuyItPosition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update buy it position if user is blocked';

    private $houseBuyItService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(HouseBuyItService $houseBuyItService)
    {
        
        $this->houseBuyItService=$houseBuyItService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("UpdateBuyItPosition: handle called");
        $blockedUserList=$this->houseBuyItService->getBlockedUserList();
        Log::info("Blocked user list : ".json_encode($blockedUserList));
        if(!empty($blockedUserList)){
            foreach($blockedUserList as $blockedUser){
               
                $blockedBuyItDesignation=$this->houseBuyItService->getBuyitUser($blockedUser->id);
                Log::info("Blocked user buy it position : ".json_encode($blockedBuyItDesignation));
                if(!empty($blockedBuyItDesignation)){
                    foreach($blockedBuyItDesignation as $buyItDesign){
                        Log::info("Blocked user info : ".json_encode($buyItDesign));
                        $this->houseBuyItService->updateUserPositions($buyItDesign->house_id,$buyItDesign->designation,$buyItDesign->user_id,$buyItDesign->position);
                        $this->houseBuyItService->updateBlockedUserStatus($buyItDesign->house_id,$buyItDesign->designation,$buyItDesign->user_id);
                    }
                }
                
            }
        }
       
        
    }
}
