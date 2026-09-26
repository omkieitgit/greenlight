<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CmaArvService;
use App\Services\EmailsAmService;

class ResetBlockedUserFromCmaArv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estates:resetBlockedUserFromCmaArv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove username and date from cma arv if user is blocked';

    private $cmaArvService;
    private $emailsAmService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CmaArvService $cmaArvService, EmailsAmService $emailsAmService)
    {
        $this->cmaArvService=$cmaArvService;      
        $this->emailsAmService=$emailsAmService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cmaArvUserList=$this->cmaArvService->getCmaArvblockedUser();
        if(!empty($cmaArvUserList)){
            foreach($cmaArvUserList as $user){
                $this->cmaArvService->updateUserAndDate($user->id);
            }
        }

        $blockedAmEmails=$this->emailsAmService->getBlockedAMUsers();
        if(!empty($blockedAmEmails)){
            foreach($blockedAmEmails as $blockedUser){
                $this->emailsAmService->updateAmEmailIsDeleted($blockedUser->emails_am_id);
            }
        }
    }
}
