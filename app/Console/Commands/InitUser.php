<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\User;

class InitUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init.user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var int
     */
    protected $limit = 8599;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        for ($i = 1; $i <= $this->limit; $i++) {
            $user = new User();
            $user->initUserByUserId($i);
            echo "Check $i\n";
            \AccountRiskUtils::checkRisk($user);
        }
    }
}
