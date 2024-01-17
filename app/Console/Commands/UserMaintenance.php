<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UserMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:user_disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'disable user account according to resignation date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::whereNotNull('resignation_date')
             ->whereDate('resignation_date','<=', date('Y-m-d'))
             ->update(['is_active' => 0]);

        return 0;
    }
}
